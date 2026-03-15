<?php

namespace App\Mail;

use App\Models\Client;
use App\Models\Cook;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CancellationReviewMail extends Mailable
{
    use Queueable, SerializesModels;

    private Client $client;

    private string $orderUuid;

    private Cook $cook;

    public function __construct(Client $client, string $orderUuid, Cook $cook)
    {
        $this->client = $client;
        $this->orderUuid = $orderUuid;
        $this->cook = $cook;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Beoordeel '.$this->cook->user->getUsername(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cancellation-review-mail',
            with: [
                'client' => $this->client,
                'cook' => $this->cook,
                'url' => env('APP_URL').'/review/'.$this->orderUuid.'/'.$this->client->getUuid(),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
