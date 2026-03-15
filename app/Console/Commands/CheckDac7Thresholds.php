<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Dac7Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckDac7Thresholds extends Command
{
    protected $signature = 'dac7:check-thresholds';

    protected $description = 'Check DAC7 thresholds for all cook users and send appropriate emails';

    protected $dac7Service;

    public function __construct(Dac7Service $dac7Service)
    {
        parent::__construct();
        $this->dac7Service = $dac7Service;
    }

    public function handle()
    {
        $this->info('Starting DAC7 threshold check for all cooks...');
        Log::info('Starting scheduled DAC7 threshold check (expired orders only)');

        // Get all users with the cook role
        $cooks = User::whereHas('roles', function ($q) {
            $q->where('name', 'cook');
        })->get();

        $this->info("Found {$cooks->count()} cooks to check.");
        Log::info("Found {$cooks->count()} cooks to check for DAC7 thresholds");

        $emailsSent = 0;

        foreach ($cooks as $cook) {
            $this->info("Checking DAC7 thresholds for {$cook->username}");

            // Get current status before check
            $statusBefore = $this->dac7Service->getDac7StatusExpiredOnly($cook);
            $warningEmailSent = $cook->dac7_warning_email_sent ?? false;
            $requiredEmailSent = $cook->dac7_required_email_sent ?? false;

            // Perform the check which may send emails
            $this->dac7Service->checkUserDac7ThresholdsExpiredOnly($cook);

            // Get updated status after check
            $cook->refresh();
            $newWarningEmailSent = $cook->dac7_warning_email_sent ?? false;
            $newRequiredEmailSent = $cook->dac7_required_email_sent ?? false;

            // Count emails sent
            if (! $warningEmailSent && $newWarningEmailSent) {
                $emailsSent++;
                $this->info("  - Warning email sent to {$cook->username}");
                Log::info("DAC7 warning email sent to {$cook->username} (UUID: {$cook->uuid}) - Orders: {$statusBefore['order_count']}, Revenue: {$statusBefore['total_revenue']}");
            }

            if (! $requiredEmailSent && $newRequiredEmailSent) {
                $emailsSent++;
                $this->info("  - Required info email sent to {$cook->username}");
                Log::info("DAC7 required email sent to {$cook->username} (UUID: {$cook->uuid}) - Orders: {$statusBefore['order_count']}, Revenue: {$statusBefore['total_revenue']}");
            }
        }

        $this->info("DAC7 threshold check completed. {$emailsSent} emails sent.");
        Log::info("DAC7 threshold check completed. {$emailsSent} emails sent.");

        return 0;
    }
}
