<?php

namespace App\Console\Commands;

use App\Dtos\PaymentDto;
use App\Mail\AutoPayoutWarningMail;
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

class ProcessAutomaticPayouts extends Command
{
    protected $signature = 'payouts:process-automatic {--test-mode : Run in test mode to simulate days} {--days=0 : Set a specific number of days for testing}';

    protected $description = 'Process automatic payouts for wallets inactive for 30 days';

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
        $this->info('Starting automatic payout process...');

        $testMode = $this->option('test-mode');
        $testDays = (int) $this->option('days');

        if ($testMode) {
            $this->info('RUNNING IN TEST MODE - actual days will be ignored');

            if ($testDays > 0) {
                $this->info("TEST MODE: Setting all wallets to {$testDays} days of inactivity");
            } else {
                $this->info('TEST MODE: Please specify days with --days=25 or --days=30 to test specific scenarios');

                return;
            }
        }

        // Get all wallets with available balance > 1
        $wallets = Wallet::whereHas('user')
            ->where('total_available', '>', 1)
            ->with(['user', 'walletLines']) // Eager load relationships
            ->get();

        $this->info("Found {$wallets->count()} wallets with available balance > 1");

        foreach ($wallets as $wallet) {
            $lastPaymentDate = $wallet->payments()
                ->where('payment_type', '!=', 'automatic') // Exclude automatic payments from last activity check
                ->latest()
                ->first()?->created_at;

            $lastActivity = $lastPaymentDate ?? $wallet->created_at;

            if (! $lastActivity) {
                $this->info('Skipping wallet - no last activity found');

                continue;
            }

            // In test mode, override the actual days calculation
            if ($testMode && $testDays > 0) {
                $daysSinceActivity = $testDays;
                $this->info("TEST MODE: Setting days since activity to {$daysSinceActivity}");
            } else {
                $daysSinceActivity = $lastActivity->diffInDays(Carbon::now());
            }

            $user = $wallet->user;

            $this->info("User {$user->username} - Last activity: {$lastActivity->format('Y-m-d H:i:s')} - Days since: {$daysSinceActivity} - Available balance: {$wallet->total_available}");

            // Check for warning at 25 days
            if ($daysSinceActivity === 25) {
                // Only send if no warning email in last 24 hours
                if (! $this->hasEmailBeenSentRecently($user)) {
                    $this->info("Sending warning email to user {$user->username}");

                    try {
                        Mail::to($user->email)->send(
                            new AutoPayoutWarningMail($user->username)
                        );
                        $this->markEmailAsSent($user);
                        $this->info("WARNING EMAIL SENT SUCCESSFULLY to {$user->email}");
                    } catch (\Exception $e) {
                        $this->error('Failed to send email: '.$e->getMessage());
                    }
                } else {
                    $this->info("Email already sent recently to {$user->username}, skipping");
                }
            }

            // Process payout at exactly 30 days
            elseif ($daysSinceActivity === 30) {
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

                // Check for recent automatic payout
                $recentAutoPayout = $wallet->payments()
                    ->where('payment_type', 'automatic')
                    ->where('created_at', '>', Carbon::now()->subDays(1))
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

                    // Send email notification
                    $this->info('Sending confirmation email...');
                    Mail::to($user->email)->send(
                        new PaymentMail($user->username, $payoutAmount)
                    );

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

        $this->info('Automatic payout process completed.');
    }

    private function hasEmailBeenSentRecently($user): bool
    {
        $lastEmailSent = $user->last_warning_email_sent_at;

        if (is_string($lastEmailSent)) {
            $lastEmailSent = Carbon::parse($lastEmailSent);
        }

        return $lastEmailSent && $lastEmailSent->gt(Carbon::now()->subDays(1));
    }

    private function markEmailAsSent($user)
    {
        $user->last_warning_email_sent_at = Carbon::now();
        $user->save();
    }
}
