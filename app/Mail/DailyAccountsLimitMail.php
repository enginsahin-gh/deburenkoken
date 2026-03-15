<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class DailyAccountsLimitMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        private int $limit,
        private int $count,
        private string $date
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            to: [new Address(config('mail.admin.address'), config('mail.admin.name'))],
            subject: 'Account Limiet Bereikt - '.$this->date,
        );
    }

    public function content(): Content
    {
        $users = User::whereDate('created_at', now())
            ->with('banking')
            ->get();

        if ($users->isEmpty()) {
            $users = collect();
        }

        return new Content(
            view: 'emails.admin.daily-accounts-limit',
            with: [
                'limit' => $this->limit,
                'count' => $this->count,
                'date' => $this->date,
                'users' => $users,
            ],
        );
    }
}
