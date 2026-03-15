<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CookieController extends Controller
{
    public function show()
    {
        return view('cookies');
    }

    public function saveCookieRights(Request $request)
    {
        $minutes = 525600; // Cookie duration: 1 year

        if ($request->has('cookierights')) {
            if ($request->cookierights == 'accept') {
                Cookie::queue('accepted_cookies', true, $minutes);
                Cookie::queue('essential_cookies', false, $minutes);
            } elseif ($request->cookierights == 'decline') {
                // Clear all non-essential cookies
                foreach ($request->cookies->all() as $name => $value) {
                    if (! in_array($name, ['deburenkoken_session', 'XSRF-TOKEN'])) {
                        Cookie::queue(Cookie::forget($name));
                    }
                }
                Cookie::queue('essential_cookies', true, $minutes);
                Cookie::queue('accepted_cookies', false, $minutes);
            }
        }

        // Special handling for admin users
        if ($request->user() && $request->user()->hasRole('admin')) {
            Cookie::queue('accepted_cookies', true, $minutes);
        }

        return redirect()->back();
    }
}
