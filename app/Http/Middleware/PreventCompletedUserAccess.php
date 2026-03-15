<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventCompletedUserAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Allow access if editing address from settings
        if (session('editing_address_from_settings')) {
            return $next($request);
        }

        if ($user && $user->userProfile && $user->cook) {
            return redirect()->route('dashboard.settings.details.home');
        }

        return $next($request);
    }
}
