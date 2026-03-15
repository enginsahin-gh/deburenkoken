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

class AlertNextCancellationCost extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;

    protected $fee;

    protected $cancellationType;

    public function __construct(
        $user,
        $fee,
        $cancellationType
    ) {
        $this->user = $user;
        $this->fee = $fee;
        $this->cancellationType = $cancellationType;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Annuleringskosten van toepassing',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.alert_next_cancellation_cost',
            with: [
                'username' => $this->user,
                'fee' => $this->fee,
                'cancellationType' => $this->cancellationType,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
