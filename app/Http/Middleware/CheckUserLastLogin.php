<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CheckUserLastLogin
{
    public function handle($request, Closure $next)
    {
        $user = Auth::check();

        // Controleer of de gebruiker is ingelogd
        if ($user) {
            $user = Auth::user();

            // hier worden 30 dagen  op de inlog datum bijgevoegd
            $logOutDate = Carbon::parse($user->last_login_date)->addDays(30);

            // Controleer of de laatste inlogdatum meer dan 30 dagen geleden is
            if ($logOutDate < now()) {
                // Als dat het geval is, log de gebruiker uit
                $user->update(['last_login_date' => null]);
                Auth::logout();

                return redirect()->route('login.home');
            }
        }

        return $next($request);
    }
}
