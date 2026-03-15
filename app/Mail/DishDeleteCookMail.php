<?php

namespace App\Mail;

use App\Models\Dish;
use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DishDeleteCookMail extends Mailable
{
    use Queueable, SerializesModels;

    private User $user;

    private Dish $dish;

    public function __construct(
        User $user,
        Dish $dish
    ) {
        $this->user = $user;
        $this->dish = $dish;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Gerecht verwijderd',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.dish.cook-delete',
            with: [
                'username' => $this->user->getUsername(),
                'dishUuid' => $this->dish->getUuid(),
                'dishTitle' => $this->dish->getTitle(),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
