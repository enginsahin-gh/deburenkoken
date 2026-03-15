<?php

namespace App\Http\Middleware;

use App\Models\Instruction;
use Auth;
use Closure;

class UserStatusMiddleware
{
    public function handle($request, Closure $next)
    {
        // Kijk of de gebruiker is ingelogd
        if (Auth::check()) {
            // Haal de uuid van de gebruiker op
            $userUuid = Auth::user()->uuid;

            // Kijk of de gebruiker de stappen heeft voltooid
            $stepsCompleted = Instruction::where('user_uuid', $userUuid)->value('completed');

            if (! $stepsCompleted) {
                $stepsCompleted = false;
            } else {
                $stepsCompleted = true;
            }

            // Pass the steps completed status to the view
            view()->share('stepsCompleted', $stepsCompleted);
        }

        return $next($request);
    }
}
