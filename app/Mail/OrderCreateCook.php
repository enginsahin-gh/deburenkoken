<?php

namespace App\Mail;

use App\Models\Advert;
use App\Models\Client;
use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCreateCook extends Mailable
{
    use Queueable, SerializesModels;

    // Statische array om bij te houden welke orders al emails hebben ontvangen
    private static $sentOrderIds = [];

    private Order $order;

    private Advert $advert;

    private Client $client;

    private User $user;

    public function __construct(
        Order $order,
        Advert $advert,
        Client $client,
        User $user
    ) {
        $this->order = $order;
        $this->advert = $advert;
        $this->client = $client;
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Je hebt een nieuwe bestelling!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.create-cook',
            with: [
                'order' => $this->order,
                'advert' => $this->advert,
                'client' => $this->client,
                'user' => $this->user,
                'dish' => $this->advert->dish,
            ]
        );
    }

    // Nieuwe methode om duplicaten te voorkomen
    public function build()
    {
        // Check of we deze email al hebben verzonden voor deze bestelling
        $orderUuid = $this->order->getUuid();
        if (in_array($orderUuid, self::$sentOrderIds)) {
            return $this; // Sla deze duplicaat over
        }

        // Markeer deze bestelling als verzonden
        self::$sentOrderIds[] = $orderUuid;

        return $this;
    }

    public function attachments(): array
    {
        return [];
    }
}
