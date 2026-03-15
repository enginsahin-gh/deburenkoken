<?php

namespace App\Mail;

use App\Models\Advert;
use App\Models\Client;
use App\Models\Cook;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriberCookInfoUpdateInform extends Mailable
{
    use Queueable, SerializesModels;

    private User $user;

    private Client $client;

    public function __construct(
        User $user,
        Client $client
    ) {
        $this->user = $user;
        $this->client = $client;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Kok info Gewijzigd',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.settings.subscriber-cook-info-update-inform',
            with: [
                'client' => $this->client,
                'user' => $this->user,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
