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

class AdvertChangeCookMail extends Mailable
{
    use Queueable, SerializesModels;

    private User $user;

    private string $dishTitle;

    private string $advertUuid;

    public function __construct(
        User $user,
        string $dishTitle,
        string $advertUuid
    ) {
        $this->user = $user;
        $this->dishTitle = $dishTitle;
        $this->advertUuid = $advertUuid;
    }

    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
    //         subject: 'Advertentie gewijzigd',
    //     );
    // }

    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'emails.adverts.cook-change',
    //         with: [
    //             'username' => $this->user->getUsername(),
    //             'dishTitle' => $this->dishTitle,
    //             'dishUuid' => $this->advertUuid
    //         ]
    //     );
    // }

    public function attachments(): array
    {
        return [];
    }
}
