<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CancelAdvertCookMail extends Mailable
{
    use Queueable, SerializesModels;

    private string $username;

    private string $title;

    private string $advertUuid;

    public function __construct(
        string $username,
        string $title,
        string $advertUuid
    ) {
        $this->username = $username;
        $this->title = $title;
        $this->advertUuid = $advertUuid;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Advertentie geannuleerd',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.adverts.cook-cancel',
            with: [
                'username' => $this->username,
                'dishTitle' => $this->title,
                'advertUuid' => $this->advertUuid,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
