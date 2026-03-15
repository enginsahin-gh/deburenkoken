<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminIbanChangeNotification extends Mailable
{
    use Queueable, SerializesModels;

    private $userData;

    private $changeCount;

    public function __construct($userData, $changeCount)
    {
        $this->userData = $userData;
        $this->changeCount = $changeCount;
    }

    public function build()
    {
        return $this->view('emails.admin.iban-change-notification')
            ->subject('Frequente IBAN-wijzigingsmelding')
            ->with([
                'userData' => $this->userData,
                'changeCount' => $this->changeCount,
            ]);
    }
}
