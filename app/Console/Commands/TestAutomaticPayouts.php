<?php

namespace App\Console\Commands;

use App\Dtos\PaymentDto;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Wallet;
use App\Models\WalletLine;
use App\Repositories\BankingRepository;
use App\Repositories\WalletRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TestAutomaticPayouts extends Command
{
    protected $signature = 'payouts:test-automatic {--force-warning : Force sending warning email regardless of time} {--force-payout : Force processing payout regardless of time}';

    protected $description = 'Test automatic payouts using minutes instead of days';

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
        $this->info('Starting test automatic payout process...');

        $forceWarning = $this->option('force-warning');
        $forcePayout = $this->option('force-payout');

        if ($forceWarning) {
            $this->info('FORCE WARNING MODE ENABLED - will send warning emails regardless of time');
        }

        if ($forcePayout) {
            $this->info('FORCE PAYOUT MODE ENABLED - will process payouts regardless of time');
        }

        $wallets = Wallet::whereHas('user')
            ->where('total_available', '>', 1)
            ->with(['user', 'walletLines'])
            ->get();

        $this->info("Found {$wallets->count()} wallets with available balance > 1");

        foreach ($wallets as $wallet) {
            $lastPaymentDate = $wallet->payments()
                ->where('payment_type', '!=', 'automatic')
                ->latest()
                ->first()?->created_at;

            $lastActivity = $lastPaymentDate ?? $wallet->created_at;

            if (! $lastActivity) {
                $this->info('Skipping wallet - no last activity found');

                continue;
            }

            $minutesSinceActivity = $lastActivity->diffInMinutes(Carbon::now());
            $user = $wallet->user;

            $this->info("User {$user->username} - Last activity: {$lastActivity->format('Y-m-d H:i:s')} - Minutes since: {$minutesSinceActivity} - Available balance: {$wallet->total_available}");

            // Check for warning now at 5-8 minutes range (or force it)
            if (($minutesSinceActivity >= 5 && $minutesSinceActivity < 8) || $forceWarning) {
                if (! $this->hasEmailBeenSentRecently($user) || $forceWarning) {
                    $this->info("Sending warning email to user {$user->username}");

                    try {
                        // Deze warning email blijft behouden - dit is voor waarschuwing voordat automatische payout wordt gemaakt
                        // Mail::to($user->email)->send(
                        //     new AutoPayoutWarningMail($user->username)
                        // );
                        $this->markEmailAsSent($user);
                        $this->info("WARNING EMAIL SENT SUCCESSFULLY to {$user->email}");
                    } catch (\Exception $e) {
                        $this->error('Failed to send email: '.$e->getMessage());
                    }
                } else {
                    $this->info("Email already sent recently to {$user->username}, skipping. Use --force-warning to override.");
                }

                // If we're only forcing the warning email, skip the rest of the loop
                if ($forceWarning && ! $forcePayout && ! ($minutesSinceActivity >= 10)) {
                    continue;
                }
            }

            // Process payout at or after 10 minutes (or force it)
            if ($minutesSinceActivity >= 10 || $forcePayout) {
                $banking = $this->bankingRepository->findByUserUuid($user->uuid);

                if (! $banking) {
                    $this->error("Skipping payout for {$user->username} - no banking info found");

                    continue;
                }

                if (! $banking->isValidated()) {
                    $this->error("Skipping payout for {$user->username} - banking not validated");
                    $this->info('Banking details: IBAN: '.$banking->getIban().', Validated: '.($banking->isValidated() ? 'Yes' : 'No'));

                    continue;
                }

                // Check for recent automatic payout (skip if forcing)
                $recentAutoPayout = false;
                if (! $forcePayout) {
                    $recentAutoPayout = $wallet->payments()
                        ->where('payment_type', 'automatic')
                        ->where('created_at', '>', Carbon::now()->subMinutes(60)) // Check last hour
                        ->exists();
                }

                if ($recentAutoPayout) {
                    $this->info("Skipping payout for {$user->username} - recent automatic payout exists. Use --force-payout to override.");

                    continue;
                }

                $this->info("Processing automatic payout for user {$user->username}");

                $totalAmount = $wallet->total_available;
                $payoutAmount = $totalAmount * 0.95;
                $this->info("Payout amount: €{$payoutAmount} (5% fee from €{$totalAmount})");

                // Use a transaction to ensure all updates happen atomically
                DB::beginTransaction();

                try {
                    // Update wallet lines to PAYOUT_INITIATED
                    $this->info('Updating wallet lines for payout...');
                    WalletLine::where('wallet_uuid', $wallet->uuid)
                        ->where('state', WalletLine::AVAILABLE)
                        ->update(['state' => WalletLine::PAYOUT_INITIATED]);

                    // Update wallet balance - CRITICAL FIX: Force direct update to 0
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

                    // Create payment record - CRITICAL FIX: Ensure payment_type is set to 'automatic'
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

                    // DEZE REGELS VERWIJDERD - GEEN EMAIL MEER VERSTUREN BIJ AUTOMATISCHE PAYOUT AANMAAK TIJDELIJK UITGESCHAKELD:
                    // Send email notification
                    // $this->info("Sending confirmation email...");
                    // Mail::to($user->email)->send(
                    //     new PaymentMail($user->username, $payoutAmount)
                    // );

                    DB::commit();
                    $this->info("Automatic payout successfully processed for {$user->username}");

                    // Verify result
                    $freshWalletAfter = Wallet::find($wallet->uuid);
                    $this->info("Wallet balance after payout: Available: {$freshWalletAfter->total_available}, Processing: {$freshWalletAfter->total_processing}");

                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error('Error processing payout: '.$e->getMessage());
                }
            }
        }

        $this->info('Test automatic payout process completed.');
    }

    private function hasEmailBeenSentRecently($user): bool
    {
        $lastEmailSent = $user->last_warning_email_sent_at;

        if (is_string($lastEmailSent)) {
            $lastEmailSent = Carbon::parse($lastEmailSent);
        }

        return $lastEmailSent && $lastEmailSent->gt(Carbon::now()->subMinutes(10)); // Changed to 10 minutes for testing
    }

    private function markEmailAsSent($user)
    {
        $user->last_warning_email_sent_at = Carbon::now();
        $user->save();
    }
}
