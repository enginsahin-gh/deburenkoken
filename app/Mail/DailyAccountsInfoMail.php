<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyAccountsInfoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public $username;

    private $pdfFilePath;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $username, $pdfFilePath)
    {
        $this->data = $data;
        $this->username = $username;
        $this->pdfFilePath = $pdfFilePath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Dagelijks accounts informatie',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.daily-accounts-info',
            with: [
                'username' => $this->username,
                'data' => $this->data,
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
