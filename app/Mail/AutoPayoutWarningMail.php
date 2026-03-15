<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class AutoPayoutWarningMail extends Mailable
{
    public $username;

    public function __construct($username)
    {
        $this->username = $username;
    }

    public function build()
    {
        return $this->view('emails.auto-payout-warning')
            ->subject('Aanstaande automatische uitbetaling')
            ->with([
                'username' => $this->username,
            ]);
    }
}
