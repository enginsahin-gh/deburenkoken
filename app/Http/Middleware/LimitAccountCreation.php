<?php

namespace App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class LimitAccountCreation
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Maximaal aantal accounts per dag
        $maxAccountsPerDay = 100;
        $today = Carbon::today();

        // Tellen van het aantal accounts dat vandaag is aangemaakt
        $accountCountToday = User::whereDate('created_at', $today)->count();

        if ($accountCountToday >= $maxAccountsPerDay) {
            return back()->with('noMoreAccountsError', 'Wegens technische redenen kan er op dit moment geen account aangemaakt worden, morgen is het weer mogelijk. Excuses voor het ongemak.');
        }

        return $next($request);
    }
}
