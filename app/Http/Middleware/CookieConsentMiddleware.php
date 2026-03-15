<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class CookieConsentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Skip cookie consent check for admin login route
        if ($request->is('admin_access') || $request->is('admin_access/*')) {
            return $next($request);
        }

        // For admin users, consider cookies as accepted
        if ($request->user() && $request->user()->hasRole('admin')) {
            view()->share('acceptedCookies', true);
            view()->share('essentialCookies', false);

            return $next($request);
        }

        // Check cookie consent for other users
        if ($request->cookie('accepted_cookies')) {
            view()->share('acceptedCookies', true);
            view()->share('essentialCookies', false);
        } elseif ($request->cookie('essential_cookies')) {
            view()->share('acceptedCookies', true);
            view()->share('essentialCookies', true);
        } else {
            view()->share('acceptedCookies', false);
            view()->share('essentialCookies', false);
        }

        return $next($request);
    }
}
