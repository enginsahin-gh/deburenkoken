<?php

namespace App\Console\Commands;

use App\Dtos\PaymentDto;
use App\Mail\PaymentMail;
use App\Models\Payment;
use App\Models\Wallet;
use App\Models\WalletLine;
use App\Repositories\BankingRepository;
use App\Repositories\WalletRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MonthlyAutomaticPayouts extends Command
{
    protected $signature = 'payouts:monthly {--force : Force payouts regardless of date}';

    protected $description = 'Process automatic payouts on the 1st day of each month';

    private WalletRepository $walletRepository;

    private BankingRepository $bankingRepository;

    public function __construct(
        WalletRepository $walletRepository,
        BankingRepository $bankingRepository
    ) {
        parent::__construct();
        $this->walletRepository = $walletRepository;
        $this->bankingRepository = $bankingRepository;
    }

    public function handle()
    {
        $this->info('Starting monthly automatic payout process...');

        // Check if today is the 1st day of the month OR force option is used
        $today = Carbon::now();
        $isFirstDayOfMonth = $today->day === 1;
        $force = $this->option('force');

        if (! $isFirstDayOfMonth && ! $force) {
            $this->info('Today is not the 1st day of the month. No payouts processed.');
            $this->info('Use --force to process payouts regardless of date.');

            return;
        }

        if ($force) {
            $this->info('FORCE option enabled - processing payouts regardless of date.');
        }

        // Get all wallets with available balance > 1
        $wallets = Wallet::whereHas('user')
            ->where('total_available', '>', 1)
            ->with(['user', 'walletLines'])
            ->get();

        $this->info("Found {$wallets->count()} wallets with available balance > 1");

        foreach ($wallets as $wallet) {
            $user = $wallet->user;
            $banking = $this->bankingRepository->findByUserUuid($user->uuid);

            if (! $banking) {
                $this->info("Skipping payout for {$user->username} - no banking info found");

                continue;
            }

            if (! $banking->isValidated()) {
                $this->info("Skipping payout for {$user->username} - banking not validated");

                continue;
            }

            // Check for recent automatic payout (in last 24 hours)
            $recentAutoPayout = $wallet->payments()
                ->where('payment_type', 'automatic')
                ->where('created_at', '>', Carbon::now()->subDay())
                ->exists();

            if ($recentAutoPayout) {
                $this->info("Skipping payout for {$user->username} - recent automatic payout exists");

                continue;
            }

            $this->info("Processing automatic payout for user {$user->username}");

            $totalAmount = $wallet->total_available;
            $payoutAmount = $totalAmount * 0.95; // 5% fee
            $this->info("Payout amount: €{$payoutAmount} (5% fee from €{$totalAmount})");

            // Use a transaction to ensure all updates happen atomically
            DB::beginTransaction();

            try {
                // Update wallet lines to PAYOUT_INITIATED
                $this->info('Updating wallet lines for payout...');
                WalletLine::where('wallet_uuid', $wallet->uuid)
                    ->where('state', WalletLine::AVAILABLE)
                    ->update(['state' => WalletLine::PAYOUT_INITIATED]);

                // Update wallet balance with a lock to prevent race conditions
                $this->info('Updating wallet balance...');

                // First get fresh instance with lock
                $freshWallet = Wallet::where('uuid', $wallet->uuid)
                    ->lockForUpdate()
                    ->first();

                // Then update with 0 as total_available
                $freshWallet->update([
                    'total_available' => 0,
                    'total_processing' => $freshWallet->total_processing + $payoutAmount,
                    'total_paid' => $freshWallet->total_paid + $payoutAmount,
                ]);

                // Create payment record
                $this->info('Creating payment record...');
                $payment = new Payment;
                $payment->uuid = \Illuminate\Support\Str::uuid()->toString();
                $payment->user_uuid = $user->uuid;
                $payment->banking_uuid = $banking->uuid;
                $payment->amount = $totalAmount;
                $payment->state = Payment::INITIATED;
                $payment->payment_type = 'automatic'; // Explicitly set
                $payment->save();

                $this->info("Created payment record with ID: {$payment->uuid}, type: {$payment->payment_type}");

                // Send email notification
                $this->info('Sending confirmation email...');
                Mail::to($user->email)->send(
                    new PaymentMail($user->username, $payoutAmount)
                );

                DB::commit();
                $this->info("Automatic payout successfully processed for {$user->username}");

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error('Error processing payout: '.$e->getMessage());
            }
        }

        $this->info('Monthly automatic payout process completed.');
    }
}
