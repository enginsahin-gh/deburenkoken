<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCustomerCancelled extends Mailable
{
    use Queueable, SerializesModels;

    private User $user;

    private Order $order;

    private string $text;

    public function __construct(User $user, Order $order, string $text)
    {
        $this->user = $user;
        $this->order = $order;
        $this->text = $text;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Je bestelling is geannuleerd',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.customer-cancelled',
            with: [
                'user' => $this->user,
                'order' => $this->order,
                'cancelMessage' => $this->text,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
