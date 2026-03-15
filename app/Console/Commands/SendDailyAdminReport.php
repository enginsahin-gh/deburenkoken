<?php

namespace App\Console\Commands;

use App\Mail\DailyAdminReport;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyAdminReport extends Command
{
    protected $signature = 'admin:daily-report';

    protected $description = 'Send daily admin report email';

    public function handle()
    {
        // Haal uitbetalingsaanvragen met de status 'INITIATED' op (niet alleen van vandaag)
        $pendingPayouts = Payment::where('state', Payment::INITIATED)
            ->with('user')
            ->get();

        // Pas het uit te betalen bedrag aan (5% bijdrage aftrekken)
        $pendingPayouts->transform(function ($payout) {
            $payout->amount = $payout->amount * 0.95; // 5% bijdrage aftrekken

            return $payout;
        });

        // Haal nieuwe accounts op die vandaag zijn aangemaakt
        $newAccounts = User::whereDate('created_at', now())
            ->with('banking')
            ->get();

        $newAccounts->transform(function ($account) {
            // Controleer of de banking-relatie bestaat en of de IBAN is ingevuld
            $account->iban_verified = $account->banking && ! empty($account->banking->iban);

            return $account;
        });
        // Verzend de e-mail
        Mail::send(new DailyAdminReport($pendingPayouts, $newAccounts));

        $this->info('Daily admin report sent successfully.');
    }
}
