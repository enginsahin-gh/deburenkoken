<?php

namespace App\Mail;

use App\Models\Advert;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdvertChangeCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    private ?string $editText;

    private Advert $advert;

    private Order $order;

    public function __construct(
        Advert $advert,
        Order $order,
        ?string $editText = null
    ) {
        $this->editText = $editText;
        $this->advert = $advert;
        $this->order = $order;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Advertentie gewijzigd',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.adverts.customer-change',
            with: [
                'text' => $this->editText,
                'advert' => $this->advert,
                'order' => $this->order,
                'client' => $this->order->client,
                'link' => env('APP_URL').'/order/cancel?uuid='.$this->order->getUuid().'&key='.encrypt($this->order->getUuid().'/'.$this->order->getClientUuid().'/'.$this->order->getCreatedAt()?->unix()),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
