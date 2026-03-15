<?php

namespace App\Console\Commands;

use App\Mail\PaymentRequestMail;
use App\Models\Banking;
use App\Models\Payment;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPaymentRequestEmails extends Command
{
    protected $signature = 'emails:send-payment-requests';

    protected $description = 'Send emails to users who made payment requests today';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Zoek alle gebruikers die vandaag een betalingsverzoek hebben gedaan
        $usersPayments = Payment::whereDate('created_at', Carbon::today())
            ->get();
        if ($usersPayments->isEmpty()) {
            $this->info('Er zijn vandaag geen Uitbetalingsverzoeken ingediend.');

            return;
        }

        // Create a new instance of Dompdf
        $options = new Options;

        // Stel de optie 'isRemoteEnabled' in op true
        $options->set('isRemoteEnabled', true);

        // Maak een nieuwe instantie van Dompdf met de opgegeven opties
        $dompdf = new \Dompdf\Dompdf($options);

        $options->set('fontDir', public_path('textfonts/'));
        $options->set('defaultFont', 'Open Sans');

        // HTML content for the PDF format
        $html = '<!DOCTYPE html>
                    <html lang="nl">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Betaalverzoeken Overzicht</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                            }
                            table {
                                width: 100%;
                                border-collapse: collapse;
                                margin-bottom: 20px;
                            }
                            table th, table td {
                                border: 1px solid #dddddd;
                                text-align: left;
                                padding: 8px;
                            }
                            table th {
                                background-color: #f2f2f2;
                            }
                        </style>
                    </head>
                    <body>
                        <h2>Uitbetalings Verzoeken - Dagelijks Overzicht - DATE</h2>
                                USERS
                        </table>
                    </body>
                    </html>';

        $userRows = '';
        // Loop door elke gebruiker en stuur een e-mail
        foreach ($usersPayments as $usersPayment) {

            $userAmount = $usersPayment->amount;
            $userProfile = UserProfile::where('user_uuid', $usersPayment->user_uuid)->first();
            $user = User::where('uuid', $usersPayment->user_uuid)->first();
            $banking = Banking::where('user_uuid', $usersPayment->user_uuid)->first();

            $userRows .= '<table>      
                                            <thead>
                                                <tr>
                                                    <th colspan="1">Thuiskok</th>
                                                    <td>'.$user->username.'</td>
                                                </tr>
                                                <tr>
                                                    <th>Volledige naam</th>
                                                    <th>Bedrag</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                 <tr>
                                                    <td>'.$userProfile->firstname.' '.$userProfile->lastname.'</td>
                                                    <td>'.$userAmount.'</td>
                                                </tr>
                                                <tr>
                                                    <th>IBAN - Laatst gewijzigd</th>
                                                    <th>'.$banking->updated_at.'</th>                          
                                                </tr>
                                                <tr colspan="2">
                                                    <td>'.$banking->iban.'</td>
                                                </tr>
                                                <tr>
                                                    <th colspan="3">Contactgegevens</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="1">Email</th>
                                                    <th colspan="1">Telefoonnummer</th>
                                                </tr>
                                                <tr>
                                                    <td>'.$user->email.'</td>
                                                    <td colspan="1">'.$userProfile->phone_number.'</td>
                                                </tr>
                                            </tbody>
                                        </table>';

        }

        $replacements = [
            'USERS' => $userRows,
            'DATE' => Carbon::today()->format('d-m-Y'),
        ];

        // Replace the placeholders
        foreach ($replacements as $placeholder => $replacement) {
            $html = str_replace($placeholder, $replacement, $html);
        }

        $dompdf->loadHtml($html);

        // Set paper size and orientation if needed
        $dompdf->setPaper('A4', 'portrait');

        // Render the PDF
        $dompdf->render();

        //

        // Save the PDF to a temporary file

        $tempPdfFile = 'Dagelijkse_UitBetaalverzoeken_'.Carbon::today().'.pdf';

        file_put_contents($tempPdfFile, $dompdf->output());
        // Send emails with the PDF attached

        //  get admin users

        $adminUsers = User::join('model_has_roles', 'users.uuid', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'admin')
            ->select('users.email', 'users.username')
            ->get();

        foreach ($adminUsers as $adminUser) {
            Mail::to(
                $adminUser->email,
                $adminUser->username
            )->send(new PaymentRequestMail(
                $adminUser,
                $tempPdfFile, // Attach the PDF document to the email
            ));
        }
        unlink($tempPdfFile);

        $this->info('Payment request emails have been sent successfully.');
    }
}
