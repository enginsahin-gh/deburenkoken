<?php

namespace App\Mail;

use App\Models\Dish;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DishDeleteCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    private string $text;

    private Dish $dish;

    private Order $order;

    public function __construct(
        string $text,
        Dish $dish,
        Order $order
    ) {
        $this->text = $text;
        $this->dish = $dish;
        $this->order = $order;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Gerecht verwijderd',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.dish.customer-delete',
            with: [
                'text' => $this->text,
                'dish' => $this->dish,
                'order' => $this->order,
                'client' => $this->order->client,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
