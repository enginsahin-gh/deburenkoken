<?php

namespace App\Console\Commands;

use App\Mail\OrdersOverviewMail;
use App\Models\Advert;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendCookOrdersInfo extends Command
{
    protected $signature = 'emails:send-cook-orders-info';

    protected $description = 'Send emails with order overview to users of expired adverts';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Fetch expired adverts that haven't had their email sent
        $expiredAdverts = Advert::where('order_date', '>', Carbon::now())
            ->where('order_time', '<', Carbon::now())
            ->whereNull('deleted_at')
            ->where('pickup_date', '>', Carbon::now())
            ->where('email_sent', false)
            ->get();

        foreach ($expiredAdverts as $advert) {
            // Fetch all orders associated with this advert
            $orders = $advert->order()->where('expected_pickup_time', '>', Carbon::now())
                ->where('status', Order::STATUS_ACTIEF)
                ->where('payment_state', Order::SUCCEED)
                ->get();

            // Group orders by cook
            $ordersGroupedByCook = $orders->groupBy('user_uuid');

            $options = new Options;
            $options->set('isRemoteEnabled', true);
            $options->set('fontDir', public_path('textfonts/'));
            $options->set('defaultFont', 'Open Sans');

            $dompdf = new Dompdf($options);

            foreach ($ordersGroupedByCook as $userUuid => $orders) {
                $user = User::find($userUuid);
                $allOrders = '';

                foreach ($orders as $order) {
                    $allOrders .= '<tr>
                                    <td>'.str_replace('Bestelnummer:', '', $order->getParsedOrderUuid()).'</td>
                                    <td>'.$order->getExpectedPickupTime()->format('d-m-Y H:i').'</td>
                                    <td>'.$order->getPortionAmount().'</td>
                                    <td>'.($order->payment_state === Order::IN_PROCESS ? 'In behandeling' : ($order->payment_state === Order::SUCCEED ? 'Betaald' : 'Mislukt')).'</td>
                                </tr>';
                }

                $html = '<!DOCTYPE html>
                        <html lang="nl">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Overzicht Bestellingen</title>
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
                            <h2>Overzicht Bestellingen - Verlopen Advertentie</h2>
                            <p>Beste '.$user->name.',</p>
                            
                            <p>Hieronder vindt u een overzicht van de bestellingen die behoren tot uw advertentie:</p>
                            
                            <table>
                                <thead>
                                    <tr>
                                        <th>Bestelnummer</th>
                                        <th>Geplande Ophaaltijd</th>
                                        <th>Portie Aantal</th>
                                        <th>Betaling Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    '.$allOrders.'
                                </tbody>
                            </table>
                            
                            <p>Bedankt,</p>
                            <p>Uw Team</p>
                        </body>
                        </html>';

                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                $tempPdfFile = 'Bestellingen Overzicht'.Carbon::today()->format('Ymd').'.pdf';
                file_put_contents($tempPdfFile, $dompdf->output());

                if ($user && $user->email_verified_at) {
                    Mail::to(
                        $user->email,
                        $user->username
                    )->send(new OrdersOverviewMail(
                        $user,
                        $advert->uuid,
                        $tempPdfFile
                    ));
                }
            }

            // Mark the advert as having its email sent
            $advert->email_sent = true;
            $advert->save();
        }

        $this->info('Emails with order overview for expired adverts have been sent successfully.');
    }
}
