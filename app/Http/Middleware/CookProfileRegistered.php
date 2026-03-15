<?php

namespace App\Http\Middleware;

use App\Models\Banking;
use App\Models\Payment;
use App\Models\User;
use App\Repositories\BankingRepository;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CookProfileRegistered
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        if (
            ! $request->hasCookie('profile') ||
            $request->cookie('profile') !== $request->user()->getUuid()
        ) {
            return $next($request);
        }
        /** @var User $user */
        $user = $request->user();

        if ($user->userProfile()->doesntExist()) {
            return redirect()->route('verification.first');
        }

        if ($user->cook()->doesntExist()) {
            return redirect()->route('verification.location');
        }

        if ($user->banking()->doesntExist()) {
            return redirect()->route('verification.banking');
        }

        if ($user->banking()->exists()) {
            if (! $user->banking->isValidated()) {
                $request->session()->forget('ibanVerification');
                $request->session()->put('ibanVerify', true);

                return redirect()->route('dashboard.wallet.iban');
            }
        }

        return $next($request);
    }
}
