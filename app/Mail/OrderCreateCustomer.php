<?php

namespace App\Mail;

use App\Models\Advert;
use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCreateCustomer extends Mailable
{
    use Queueable, SerializesModels;

    // Statische array om bij te houden welke orders al emails hebben ontvangen
    private static $sentOrderIds = [];

    private Order $order;

    private Advert $advert;

    private User $user;

    private string $pdfFilePath;

    public function __construct(
        Order $order,
        Advert $advert,
        User $user,
        string $pdfFilePath
    ) {
        $this->order = $order;
        $this->advert = $advert;
        $this->user = $user;
        $this->pdfFilePath = $pdfFilePath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            subject: 'Je bestelling is geplaatst!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.create-customer',
            with: [
                'order' => $this->order,
                'advert' => $this->advert,
                'user' => $this->user,
                'cook' => $this->advert->cook,
                'client' => $this->order->client,
                'dish' => $this->advert->dish,
                'link' => env('APP_URL').'/order/cancel?uuid='.$this->order->getUuid().'&key='.encrypt($this->order->getUuid().'/'.$this->order->getClientUuid().'/'.$this->order->getCreatedAt()?->unix()),
            ]
        );
    }

    // Nieuwe methode om duplicaten te voorkomen
    public function build()
    {
        // Check of we deze email al hebben verzonden voor deze bestelling
        $orderUuid = $this->order->getUuid();
        if (in_array($orderUuid, self::$sentOrderIds)) {
            return $this; // Sla deze duplicaat over
        }

        // Markeer deze bestelling als verzonden
        self::$sentOrderIds[] = $orderUuid;

        return $this;
    }

    // public function attachments(): array
    // {
    //     return [
    //         $this->pdfFilePath // Geef het PDF-bestandspad terug als bijlage
    //     ];
    // }
}
