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
use Illuminate\Support\Facades\URL;

class ReactivatedAccountVerifyMail extends Mailable
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
            subject: 'Verifier je email adres',
        );
    }

    public function content(): Content
    {
        $link = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), ['id' => $this->user->getUuid(), 'reactivated' => true]); // Dit is de vervaldatum voor de link['id' => $notifiable->getKey()]);

        return new Content(
            view: 'emails.verification',
            with: [
                'user' => $this->user,
                'link' => $link,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
