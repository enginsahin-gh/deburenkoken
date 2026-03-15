<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCookCancelled extends Mailable
{
    use Queueable, SerializesModels;

    private User $user;

    private Order $order;

    private string $cancelMessage;

    public function __construct(
        User $user,
        Order $order,
        string $cancelMessage
    ) {
        $this->user = $user;
        $this->order = $order;
        $this->cancelMessage = $cancelMessage;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Bestelling is geannuleerd!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.cook-cancelled',
            with: [
                'order' => $this->order,
                'user' => $this->user,
                'cancelMessage' => $this->cancelMessage,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
