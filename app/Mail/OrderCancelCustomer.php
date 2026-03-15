<?php

namespace App\Mail;

use App\Models\Client;
use App\Models\Dish;
use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCancelCustomer extends Mailable
{
    use Queueable, SerializesModels;

    private User $user;

    private Order $order;

    private Dish $dish;

    private string $text;

    private Client $client;

    public function __construct(
        User $user,
        Order $order,
        Dish $dish,
        string $text,
        Client $client
    ) {
        $this->user = $user;
        $this->order = $order;
        $this->dish = $dish;
        $this->text = $text;
        $this->client = $client;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Bestelling geannuleerd',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.cook-cancel',
            with: [
                'text' => $this->text,
                'order' => $this->order,
                'dish' => $this->dish,
                'client' => $this->client,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
