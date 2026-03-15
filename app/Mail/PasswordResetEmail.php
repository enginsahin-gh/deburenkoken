<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetEmail extends Mailable
{
    use Queueable, SerializesModels;

    private string $token;

    private User $user;

    public function __construct(
        string $token,
        User $user
    ) {
        $this->token = $token;
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Stel je wachtwoord opnieuw in'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password',
            with: [
                'user' => $this->user,
                'token' => $this->token,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
