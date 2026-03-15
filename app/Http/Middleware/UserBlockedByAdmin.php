<?php

namespace App\Http\Middleware;

use App\Exceptions\CustomException;
use Closure;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class UserBlockedByAdmin
{
    /**
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        if (is_null($request->user())) {
            return $next($request);
        }

        if (! $request->user()->isBlockedByAdmin()) {
            return $next($request);
        }

        Auth::logout();

        throw new CustomException(403, 'U heeft geen toegang meer. Neem contact met ons op via het contactformulier');
    }
}
