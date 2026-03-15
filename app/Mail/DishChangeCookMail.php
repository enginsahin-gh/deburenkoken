<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DishChangeCookMail extends Mailable
{
    use Queueable, SerializesModels;

    private string $username;

    private string $dishTitle;

    private string $dishUuid;

    public function __construct(
        string $username,
        string $dishTitle,
        string $dishUuid
    ) {
        $this->username = $username;
        $this->dishTitle = $dishTitle;
        $this->dishUuid = $dishUuid;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Gerecht gewijzigd',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.dish.cook-change',
            with: [
                'username' => $this->username,
                'dishTitle' => $this->dishTitle,
                'dishUuid' => $this->dishUuid,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
