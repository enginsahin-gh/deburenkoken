<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Dac7RequiredMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Create a secure token
        $token = hash('sha256', $this->user->email.$this->user->uuid);

        return $this->subject('Herinnering voor het aanleveren van aanvullende gegevens DAC7')
            ->view('emails.dac7-required')
            ->with([
                'userName' => $this->user->username,
                'dac7FormUrl' => route('dac7.form', ['uuid' => $this->user->uuid, 'token' => $token]),
            ]);
    }
}
