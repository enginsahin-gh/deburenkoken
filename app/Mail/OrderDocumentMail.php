<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderDocumentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $clientName;

    public $orderNumber;

    public $dishName;

    public $documentType;

    public $pdfContent;

    public $filename;

    public function __construct($clientName, $orderNumber, $dishName, $documentType, $pdfContent, $filename)
    {
        $this->clientName = $clientName;
        $this->orderNumber = $orderNumber;
        $this->dishName = $dishName;
        $this->documentType = $documentType;
        $this->pdfContent = $pdfContent;
        $this->filename = $filename;
    }

    public function build()
    {
        return $this->view('emails.order-document')
            ->subject($this->documentType.' bestelling '.$this->orderNumber)
            ->attachData($this->pdfContent, $this->filename, [
                'mime' => 'application/pdf',
            ]);
    }
}
