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

class CookDeleteNotification extends Mailable
{
    use Queueable, SerializesModels;

    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Account verwijderd',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.delete-cook-notification',
            with: [
                'username' => $this->user->getUsername(),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
