<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    private string $username;

    private float $amount;

    public function __construct(
        string $username,
        float $amount
    ) {
        $this->username = $username;
        $this->amount = $amount;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Je uitbetalingsaanvraag is succesvol verwerkt',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment',
            with: [
                'username' => $this->username,
                'amount' => $this->amount,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
