<?php

// temp disabled as a check for the other email overview.

// namespace App\Mail;

// use App\Models\User;
// use App\Models\Order;
// use Illuminate\Bus\Queueable;
// use Illuminate\Mail\Mailable;
// use Illuminate\Queue\SerializesModels;
// use Illuminate\Mail\Mailables\Content;
// use Illuminate\Mail\Mailables\Envelope;
// use Illuminate\Mail\Mailables\Address;
// class OrdersOverviewMail extends Mailable
// {
//     use Queueable, SerializesModels;

//     public $user;
//     public $advertUuid;
//     public $tempPdfFile;

//     public function __construct(User $user, $advertUuid, $tempPdfFile)
//     {
//         $this->user = $user;
//         $this->advertUuid = $advertUuid;
//         $this->tempPdfFile = $tempPdfFile;
//     }
//         public function envelope(): Envelope
//     {
//         return new Envelope(
//             from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
//             subject: 'Overzicht Bestellingen',
//         );
//     }

//     public function content(): Content
//     {
//         return new Content(
//             view: 'emails.admin.cook-orders-overview',
//             with: [
//                 'user' => $this->user,
//                 'advertUuid' => $this->advertUuid
//             ]
//         );
//     }

//     public function attachments(): array
//     {
//         return [$this->tempPdfFile];
//     }
// }
