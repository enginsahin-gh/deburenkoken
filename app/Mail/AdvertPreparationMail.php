<?php

namespace App\Mail;

use App\Models\Advert;
use App\Models\Cook;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdvertPreparationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $advert;

    public $cook;

    public $shortUuid;

    public $activeOrders;

    public $hasOrdersInProcess;

    private $isBuilt = false;

    /**
     * Create a new message instance.
     */
    public function __construct(Advert $advert, Cook $cook)
    {
        $this->advert = $advert;
        $this->cook = $cook;
        $this->shortUuid = substr($advert->getUuid(), -6);

        // Filter to only get SUCCEED orders (niet IN_PROCESS)
        $this->activeOrders = $advert->order->filter(function ($order) {
            return $order->status !== Order::STATUS_GEANNULEERD
                && $order->status !== Order::STATUS_VERLOPEN
                && $order->payment_state == Order::SUCCEED; // Alleen betalingen die voltooid zijn
        })->values();

        // Check if there are any orders with payment in process
        $this->hasOrdersInProcess = $advert->order->contains(function ($order) {
            return $order->payment_state == Order::IN_PROCESS;
        });
    }

    /**
     * Build the message.
     *
     * @return $this|null
     */
    public function build()
    {
        if ($this->isBuilt) {
            return $this;
        }

        try {
            $this->isBuilt = true;

            return $this->subject("Samenvatting bestellingen advertentie {$this->shortUuid}, {$this->advert->dish->title}")
                ->view('emails.advert_preparation');
        } catch (\Exception $e) {
            \Log::error('Failed to send preparation email: '.$e->getMessage());

            return null;
        }
    }
}
