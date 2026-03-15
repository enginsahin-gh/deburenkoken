<?php

namespace App\Services;

use App\Mail\ChangedMailVerification;
use App\Mail\ChangedVerifiedMail;
use App\Mail\CookDeleteNotification;
use App\Mail\PasswordResetEmail;
use App\Mail\PasswordResetNotificationMail;
use App\Mail\VerificationChangedMail;
use App\Mail\VerifiedMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class MailService
{
    public function sendChangedMailVerification(User $user): void
    {
        Mail::to(
            $user->getEmail(),
            $user->getUsername()
        )->send(new ChangedMailVerification($user));
    }

    public function sendVerificationChangedMail(User $user): void
    {
        Mail::to(
            $user->getEmail(),
            $user->getUsername()
        )->send(new VerificationChangedMail($user));
    }

    public function sendMailVerification(User $user): void
    {
        $user->sendEmailVerificationNotification();
    }

    public function sendVerifiedMail(User $user): void
    {
        Mail::to(
            $user->getEmail(),
            $user->getUsername()
        )->send(new VerifiedMail($user));
    }

    public function sendChangedVerifiedMail(User $user): void
    {
        Mail::to(
            $user->getEmail(),
            $user->getUsername()
        )->send(new ChangedVerifiedMail($user));
    }

    public function sendPasswordChangedNotification(User $user): void
    {
        Mail::to(
            $user->getEmail(),
            $user->getUsername()
        )->send(new PasswordResetNotificationMail($user));
    }

    public function sendPasswordResetNotification(User $user, string $token): void
    {
        Mail::to(
            $user->getEmail(),
            $user->getUsername()
        )->send(new PasswordResetEmail($token, $user));
    }

    public function sendDeleteNotification(User $user): void
    {
        Mail::to(
            $user->getEmail(),
            $user->getUsername()
        )->send(new CookDeleteNotification($user));
    }
}
