<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlertAdminUserForSuspiciousCancellations extends Mailable
{
    use Queueable, SerializesModels;

    public $username;

    public $client;

    public $adminMessage;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($username, $client, $adminMessage)
    {
        $this->username = $username;
        $this->client = $client;
        $this->adminMessage = $adminMessage;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: 'Suspicious Cancellations Behaviour Detected!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.suspicious-cancellations',
            with: [
                'username' => $this->username,
                'clientName' => $this->client->name,
                'clientEmail' => $this->client->email,
                'clientPhone' => $this->client->phone_number,
                'adminMessage' => $this->adminMessage,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
