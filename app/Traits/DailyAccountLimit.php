<?php

namespace App\Http\Controllers;

use App\Mail\DailyAccountsLimitMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

trait DailyAccountLimit
{
    private function checkDailyAccountLimit(): bool|RedirectResponse
    {
        $today = Carbon::now()->format('Y-m-d');
        $cacheKey = "daily_registrations_{$today}";

        $dailyCount = Cache::remember($cacheKey, Carbon::now()->endOfDay(), function () {
            return DB::table('users')
                ->whereDate('created_at', Carbon::today())
                ->count();
        });

        if ($dailyCount >= 2) {
            // Mail naar admin uit .env
            Mail::to(
                config('mail.admin.address'),
                config('mail.admin.name')
            )->send(new DailyAccountsLimitMail(
                limit: 2,
                count: $dailyCount,
                date: now()->format('d-m-Y')
            ));

            return redirect()
                ->back()
                ->with('noMoreAccountsError', 'Helaas is het niet meer mogelijk om vandaag een account aan te maken. Morgen is het wel weer mogelijk. Excuses voor het ongemak.');
        }

        Cache::increment($cacheKey);

        return true;
    }
}
