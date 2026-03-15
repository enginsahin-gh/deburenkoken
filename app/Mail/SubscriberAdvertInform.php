<?php

namespace App\Mail;

use App\Models\Advert;
use App\Models\Client;
use App\Models\Cook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriberAdvertInform extends Mailable
{
    use Queueable, SerializesModels;

    private Cook $cook;

    private Advert $advert;

    private Client $client;

    public function __construct(
        Cook $cook,
        Advert $advert,
        Client $client
    ) {
        $this->cook = $cook;
        $this->advert = $advert;
        $this->client = $client;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Nieuwe advertentie geplaatst',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscriber-advert',
            with: [
                'url' => env('APP_URL').'/search/cooks/'.$this->cook->getUuid().'/details/advert/'.$this->advert->getUuid(),
                'client' => $this->client,
                'cook' => $this->cook,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
