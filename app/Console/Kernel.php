<?php

namespace App\Console;

use App\Console\Commands\DatabaseBackupDaily;
use App\Console\Commands\DatabaseBackupHourly;
use App\Console\Commands\DatabaseBackupMonthly;
use App\Console\Commands\DeleteDatabaseBackup;
use App\Console\Commands\MonthlyAutomaticPayouts;
use App\Console\Commands\ProcessAutomaticPayouts;
use App\Console\Commands\ProcessOrders;
use App\Console\Commands\RemoveOldAccounts;
use App\Console\Commands\ReviewMail;
use App\Console\Commands\SendAllPaymentRequestEmails;
use App\Console\Commands\SendCookOrdersInfo;
use App\Console\Commands\SendDailyAccountsInfoMail;
use App\Console\Commands\SendPaymentRequestEmails;
use App\Console\Commands\TestAutomaticPayouts;
use App\Console\Commands\UpdateOrderStatusesCommand;
use App\Console\Commands\WalletConsistencyCheckCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        TestAutomaticPayouts::class,
        // ProcessAutomaticPayouts::class, // Optioneel: commentaar of verwijderen
        MonthlyAutomaticPayouts::class,
        UpdateOrderStatusesCommand::class,
        WalletConsistencyCheckCommand::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Bestaande schedules
        $schedule->command(ReviewMail::class)->everyMinute()->withoutOverlapping();
        $schedule->command(RemoveOldAccounts::class)->daily()->withoutOverlapping();
        $schedule->command(ProcessOrders::class)->everyMinute()->withoutOverlapping();
        $schedule->command(SendPaymentRequestEmails::class)->daily()->withoutOverlapping();
        $schedule->command(SendAllPaymentRequestEmails::class)->weekly()->withoutOverlapping();
        $schedule->command(SendDailyAccountsInfoMail::class)->daily()->withoutOverlapping();
        $schedule->command(SendCookOrdersInfo::class)->everyMinute()->withoutOverlapping();

        $schedule->command('dac7:check-thresholds')->everyMinute();

        // Modified order status update schedule with log output
        $schedule->command('orders:update-statuses')
            ->everyMinute()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/scheduler.log'));

        // Added new wallet consistency check schedule (now running every minute)
        $schedule->command('wallet:consistency-check')
            ->everyMinute()
            ->withoutOverlapping();

        // Backup schedules - met extra veiligheidsmarge om overlapping te voorkomen
        // Monthly backup - First of the month at 01:00 (permanent storage)
        $schedule->command('db:backup-monthly')
            ->monthlyOn(1, '01:00')
            ->withoutOverlapping(120 * 60)
            ->runInBackground();

        // Daily backup - Every day at 01:00 (keep for 3 months)
        $schedule->command('db:backup-daily')
            ->dailyAt('01:00')
            ->withoutOverlapping(60 * 60)
            ->runInBackground();

        // Hourly backup - Every hour (keep for 1 week)
        $schedule->command('db:backup-hourly')
            ->hourlyAt(0)  // Precies aan het begin van elk uur (00 minuten)
            ->withoutOverlapping(30 * 60)
            ->runInBackground();

        // Cleanup old backups - Run at a different time than other backup jobs
        $schedule->command('backup:delete-old')
            ->dailyAt('03:30')
            ->withoutOverlapping(60 * 60)
            ->runInBackground();

        // Other schedules
        $schedule->command('admin:daily-report')
            ->dailyAt('20:00')
            ->withoutOverlapping();
        $schedule->command('adverts:send-preparation-emails')
            ->everyMinute()
            ->withoutOverlapping();
        //     $schedule->command('payouts:process-automatic')
        //         ->daily()
        //         ->withoutOverlapping();

        $schedule->command('payouts:monthly')
            ->daily() // Dagelijks checken, maar het runt alleen op de 1e
            ->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
