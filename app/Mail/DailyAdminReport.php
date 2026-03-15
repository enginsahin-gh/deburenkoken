<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class DailyAdminReport extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        private Collection $pendingPayouts,
        private Collection $newAccounts
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            to: [new Address(config('mail.admin.address'), config('mail.admin.name'))],
            subject: 'Dagelijks Beheerrapport - '.now()->format('d/m/Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.daily-report',
            with: [
                'pendingPayouts' => $this->pendingPayouts,
                'newAccounts' => $this->newAccounts,
                'date' => now()->format('d/m/Y'),
            ],
        );
    }
}
