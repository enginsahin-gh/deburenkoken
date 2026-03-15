<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    private User $user;

    private string $pdfFilePath;

    public function __construct(

        User $user,
        string $pdfFilePath
    ) {
        $this->user = $user;
        $this->pdfFilePath = $pdfFilePath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Alle Uitbetalingsverzoeken Overzicht',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.payment-request-all-mail',
            with: [
                'user' => $this->user,
            ]
        );
    }

    public function attachments(): array
    {
        return [
            $this->pdfFilePath, // Geef het PDF-bestandspad terug als bijlage
        ];
    }
}
