<?php

namespace App\Console\Commands;

use App\Mail\DailyAccountsInfoMail;
use App\Models\Advert;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyAccountsInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:dailyAccountsInfo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily report of newly created accounts with additional information';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Fetch counts from database
        $countNewUsers = User::whereDate('created_at', Carbon::today())->count();
        $countUpdatedEmailUsers = User::whereDate('email_verified_at', Carbon::today())->count();
        $countDeletedUsers = User::onlyTrashed()->whereDate('deleted_at', Carbon::today())->count();
        $countOrdersToday = Order::whereDate('created_at', Carbon::today())->count();
        $countAdvertsToday = Advert::whereDate('created_at', Carbon::today())->count();
        $countCancelledAdvertsToday = Advert::onlyTrashed()->whereDate('deleted_at', Carbon::today())->count();
        $activeOrdersCount = Order::whereDate('created_at', Carbon::today())->where('payment_state', Order::PAID_OUT)->count();
        $cancelledOrdersCount = Order::whereDate('created_at', Carbon::today())->where('payment_state', Order::CANCELLED)->count();
        $failedOrdersCount = Order::whereDate('created_at', Carbon::today())->where('payment_state', Order::FAILED)->count();

        // Create data array to pass to email template
        $data = [
            'countNewUsers' => $countNewUsers,
            'countUpdatedEmailUsers' => $countUpdatedEmailUsers,
            'countDeletedUsers' => $countDeletedUsers,
            'countOrdersToday' => $countOrdersToday,
            'countAdvertsToday' => $countAdvertsToday,
            'countCancelledAdvertsToday' => $countCancelledAdvertsToday,
            'countActiveOrders' => $activeOrdersCount,
            'countCancelledOrders' => $cancelledOrdersCount,
            'countFailedOrders' => $failedOrdersCount,
        ];

        // Create a new instance of Dompdf with options
        $options = new Options;
        $options->set('isRemoteEnabled', true); // Enable remote file access if needed
        $options->set('fontDir', public_path('textfonts/')); // Set the font directory path
        $options->set('defaultFont', 'Open Sans'); // Set the default font

        $dompdf = new Dompdf($options);

        // HTML content for the PDF
        $html = '<!DOCTYPE html>
        <html lang="nl">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Dagelijks Rapport</title>
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
            <h2>Dagelijks Rapport - '.Carbon::today()->format('d-m-Y').'</h2>
            <table>
                <thead>
                    <tr>
                        <th>Categorie</th>
                        <th>Aantal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Aantal nieuwe accounts</td>
                        <td>'.$countNewUsers.'</td>
                    </tr>
                    <tr>
                        <td>Aantal actieve orders</td>
                        <td>'.$activeOrdersCount.'</td>
                    </tr>
                    <tr>
                        <td>Aantal geannuleerde orders</td>
                        <td>'.$cancelledOrdersCount.'</td>
                    </tr>
                    <tr>
                        <td>Aantal gefaalde orders</td>
                        <td>'.$failedOrdersCount.'</td>
                    </tr>
                    <tr>
                        <td>Aantal advertenties vandaag aangemaakt</td>
                        <td>'.$countAdvertsToday.'</td>
                    </tr>
                    <tr>
                        <td>Aantal advertenties vandaag geannuleerd</td>
                        <td>'.$countCancelledAdvertsToday.'</td>
                    </tr>
                    <tr>
                        <td>Aantal accounts verwijderd</td>
                        <td>'.$countDeletedUsers.'</td>
                    </tr>
                    <tr>
                        <td>Aantal emailadres wijzigingen</td>
                        <td>'.$countUpdatedEmailUsers.'</td>
                    </tr>
                </tbody>
            </table>
        </body>
        </html>';

        // Load HTML content into Dompdf
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the PDF
        $dompdf->render();

        // Save the PDF to a temporary file
        $tempPdfFile = 'Dagelijks_Rapport_'.Carbon::today()->format('Ymd').'.pdf';
        file_put_contents($tempPdfFile, $dompdf->output());

        // Fetch admin users who should receive the report
        $adminUsers = User::join('model_has_roles', 'users.uuid', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'admin')
            ->select('users.email', 'users.username')
            ->get();

        // Send email with the PDF attached to each admin user
        foreach ($adminUsers as $adminUser) {
            Mail::to($adminUser->email)
                ->send(new DailyAccountsInfoMail($data, $adminUser->username, $tempPdfFile));
        }

        // Delete the temporary PDF file
        unlink($tempPdfFile);

        return Command::SUCCESS;
    }
}
