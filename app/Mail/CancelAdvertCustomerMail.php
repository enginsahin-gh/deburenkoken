<?php

namespace App\Mail;

use App\Models\Advert;
use App\Models\Client;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CancelAdvertCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    private Advert $advert;

    private Order $order;

    private Client $client;

    private ?string $text;

    public function __construct(
        Advert $advert,
        Order $order,
        Client $client,
        ?string $text = null
    ) {
        $this->advert = $advert;
        $this->order = $order;
        $this->client = $client;
        $this->text = $text;
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
            view: 'emails.adverts.customer-cancel',
            with: [
                'advert' => $this->advert,
                'order' => $this->order,
                'client' => $this->client,
                'text' => $this->text,
                'cook' => $this->advert->cook,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
