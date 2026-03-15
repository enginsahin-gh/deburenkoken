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

class DishChangeCustomerMail extends Mailable
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
            subject: 'Gerecht gewijzigd',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.dish.customer-change',
            with: [
                'text' => $this->text,
                'dish' => $this->dish,
                'order' => $this->order,
                'client' => $this->order->client,
                'link' => env('APP_URL').'/order/cancel?uuid='.$this->order->getUuid().'&key='.encrypt($this->order->getUuid().'/'.$this->order->getClientUuid().'/'.$this->order->getCreatedAt()?->unix()),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
