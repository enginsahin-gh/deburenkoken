<?php

namespace App\Http\Middleware;

use App\Models\WebsiteStatus;
use Closure;
use Illuminate\Http\Request;

class CheckWebsiteStatus
{
    public function handle(Request $request, Closure $next)
    {
        // Skip check for admin login routes
        if ($request->is('admin_access*')) {
            return $next($request);
        }

        // Check if user is logged in as admin
        if ($request->user() && $request->user()->hasRole('admin')) {
            return $next($request);
        }

        // Check website status
        $websiteStatus = WebsiteStatus::first();

        // If website is offline and user is not an admin
        if (! $websiteStatus || ! $websiteStatus->is_online) {
            // Allow logout and main login functionality when offline
            if ($request->is('logout') || $request->is('login') || $request->is('login/submit')) {
                return $next($request);
            }

            // Block forgot password routes when offline
            if ($request->is('login/forgot*')) {
                return response()->view('errors.maintenance');
            }

            return response()->view('errors.maintenance');
        }

        return $next($request);
    }
}
