<?php

namespace App\Http\Middleware;

use Closure;

class ValidateUuid
{
    public function handle($request, Closure $next)
    {
        $uuid = $request->route('uuid');

        if ($uuid === null || ! ctype_xdigit($uuid) || strlen($uuid) !== 32) {
            abort(404, 'Invalid UUID');
        }

        return $next($request);
    }
}
