<?php

namespace App\Http\Controllers;

use App\Models\Instruction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IntroController extends Controller
{
    /**
     * Mark the intro as completed for the current user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function markAsCompleted()
    {
        // Get the currently authenticated user's UUID
        $userUuid = Auth::user()->uuid;

        // Save the completion status in the instructions table
        Instruction::updateOrCreate(
            ['user_uuid' => $userUuid],
            ['completed' => true]
        );

        return redirect()->route('dashboard.adverts.active.home');
    }
}
