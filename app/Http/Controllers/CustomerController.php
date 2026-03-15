<?php

namespace App\Http\Controllers;

use App\Dtos\ClientDto;
use App\Dtos\MailingListDto;
use App\Dtos\OrderDto;
use App\Dtos\WalletLineDto;
use App\Mail\OrderCreateCook;
use App\Mail\OrderCreateCustomer;
use App\Models\Advert;
use App\Models\Banking;
use App\Models\Cook;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WalletLine;
use App\Repositories\AdvertRepository;
use App\Repositories\ClientRepository;
use App\Repositories\CookRepository;
use App\Repositories\DishRepository;
use App\Repositories\MailingListRepository;
use App\Repositories\OrderRepository;
use App\Repositories\WalletRepository;
use App\Rules\OrderAmount;
use App\Services\Dac7Service;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpWord\IOFactory;
use PhpWordToHtml\PhpWordToHtml;
use Symfony\Component\HttpFoundation\Cookie;

class CustomerController extends Controller
{
    private Request $request;

    private AdvertRepository $advertRepository;

    private OrderRepository $orderRepository;

    private ClientRepository $clientRepository;

    private MailingListRepository $mailingListRepository;

    private WalletRepository $walletRepository;

    private CookRepository $cookRepository;

    private DishRepository $dishRepository;

    private Dac7Service $dac7Service;

    public function __construct(
        Request $request,
        AdvertRepository $advertRepository,
        OrderRepository $orderRepository,
        ClientRepository $clientRepository,
        MailingListRepository $mailingListRepository,
        WalletRepository $walletRepository,
        CookRepository $cookRepository,
        DishRepository $dishRepository,
        Dac7Service $dac7Service
    ) {
        $this->request = $request;
        $this->advertRepository = $advertRepository;
        $this->orderRepository = $orderRepository;
        $this->clientRepository = $clientRepository;
        $this->mailingListRepository = $mailingListRepository;
        $this->walletRepository = $walletRepository;
        $this->cookRepository = $cookRepository;
        $this->dishRepository = $dishRepository;
        $this->dac7Service = $dac7Service;
    }

    public function getAdvertDetailsView(string $uuid): Response
    {
        $searchString = $this->request->getQueryString() ?? '';
        $advert = $this->advertRepository->find($uuid);

        return response(view('customer.detail', [
            'advert' => $advert,
            'distance' => $this->request->query('calculatedDistance'),
            'user' => $this->request->user(),
            'searchString' => $searchString,
        ]))->withCookie(Cookie::create('searchString', $searchString));
    }

    public function getDishDetailsViewByCook(
        string $cookUuid,
        string $dishUuid
    ): View {
        return view('customer.cook-dish-detail', [
            'cook' => $this->cookRepository->find($cookUuid),
            'dish' => $this->dishRepository->find($dishUuid),
        ]);
    }

    public function getAdvertDetailsViewByCook(
        string $cookUuid,
        string $advertUuid
    ): View {
        return view('customer.cook-advert-detail', [
            'advert' => $this->advertRepository->find($advertUuid),
            'user' => $this->request->user(),
            'cook' => $this->cookRepository->find($cookUuid),
            'searchString' => $this->request->getQueryString(),
            'distanceFromUser' => $this->request->query('distance-from-user'),
        ]);
    }

    public function getAdvertOrderView(string $uuid): View
    {

        if (session()->has('error')) {
            session()->flash('paymentError', 'Transactie is niet succesvol afgerond, probeer nogmaals.');
            session()->forget('error');
        }

        return view('customer.order', [
            'searchString' => $this->request->getQueryString(),
            'advert' => $this->advertRepository->find($uuid)->setDistance($this->request->query('calculatedDistance')),
        ]);
    }

    public function submitCustomerOrder(string $uuid): RedirectResponse
    {
        // EERST advert ophalen voor validatie (zonder lock)
        $advertForValidation = $this->advertRepository->find($uuid);

        if (! $advertForValidation) {
            throw new ModelNotFoundException;
        }

        // DAN valideren met $advertForValidation
        $this->request->validate([
            'name' => 'required|max:50',
            'email' => ['required', 'email:rfc,dns', 'max:100', 'regex:/^[^@]+(\.[^@]+)*@[^@]+\.[^@]+$/'],
            'phone' => 'required|max:20',
            'amount' => ['required', new OrderAmount(
                $this->request,
                $advertForValidation  // ← gebruik $advertForValidation
            )],
            'time' => ['required', 'valid_time_and_min_time:'.$advertForValidation->pickup_from.','.$advertForValidation->pickup_to, ''],  // ← gebruik $advertForValidation
            'description' => 'nullable',
            'inform' => 'nullable',
        ]);

        // NU pas transaction starten
        DB::beginTransaction();

        try {
            // Haal advert OPNIEUW op, nu MET lock
            $advert = Advert::where('uuid', $uuid)
                ->lockForUpdate()
                ->first();

            if (is_null($advert)) {
                DB::rollBack();
                throw new ModelNotFoundException;
            }

            if ($advert->getParsedOrderTo()->isPast()) {
                DB::rollBack();

                return redirect()->route('home')->with('message', 'Er is iets fout gegaan.');
            }

            $orderedPortions = Order::where('advert_uuid', $advert->uuid)
                ->where('status', Order::STATUS_ACTIEF)
                ->whereIn('payment_state', [Order::SUCCEED, Order::PAYOUT_PENDING])
                ->lockForUpdate()
                ->sum('portion_amount');

            $availablePortions = $advert->portion_amount - $orderedPortions;

            if ($availablePortions < $this->request->input('amount')) {
                DB::rollBack();

                return redirect()->back()
                    ->with('paymentError', 'Helaas, er zijn niet genoeg porties meer beschikbaar. Er zijn nog maar '.$availablePortions.' porties beschikbaar.');
            }

            $client = $this->clientRepository->findByEmail($this->request->input('email'));

            if (is_null($client)) {
                $client = $this->clientRepository->create(
                    new ClientDto(
                        $this->request->input('name'),
                        $this->request->input('email'),
                        $this->request->input('phone')
                    )
                );
            } else {
                $client = $this->clientRepository->update(
                    $client->getUuid(),
                    new ClientDto(
                        $this->request->input('name'),
                        $client->getEmail(),
                        $this->request->input('phone')
                    )
                );
            }

            /** @var Cook $cook */
            $cook = $advert->cook;

            /** @var User $user */
            $user = $cook->user;

            $order = $this->orderRepository->create(
                new OrderDto(
                    $advert->dish,
                    $client,
                    $user,
                    $advert,
                    $this->request->input('amount'),
                    Carbon::parse($advert->getPickupDate().' '.$this->request->input('time')),
                    Order::IN_PROCESS,
                    $this->request->input('description')
                )
            );

            if ($this->request->input('inform') === 'on') {
                $mailExistsInMailingList = $this->mailingListRepository->emailExistsInMailingList($this->request->input('email'));

                if (! $mailExistsInMailingList) {
                    $this->mailingListRepository->create(
                        new MailingListDto(
                            $advert->cook,
                            $client
                        )
                    );
                }
            }

            $userWallet = $this->walletRepository->findWalletForUser($user->getUuid());

            // bereken het gerechtsbedrag  de reden dat ik er een 0 achter zet is vanwege de juiste format voor mollie
            $transactionCost = 0.00;
            $dishPrice = $advert->dish->getPortionPrice();
            $amount = $dishPrice * $order->getPortionAmount() + $transactionCost.'';
            $explodedAmount = explode('.', $amount);
            $decimalAmount = isset($explodedAmount[1]) ? $explodedAmount[1] : '';

            if (strlen($decimalAmount) === 1) {
                $amount = $amount.'0';
            } elseif ($decimalAmount == '') {
                $amount = $amount.'.00';
            }

            // Haal de de gerechtsnaam op om een beschrijving te maken voor de betaling
            $paymentDescription = $advert->dish->getTitle().', '.$order->getPortionAmount().' portie(s), bereid door '.$cook->user->getUsername();

            // Maak connectie met mollie api
            $mollie = new \Mollie\Api\MollieApiClient;
            $mollie->setApiKey(env('MOLLIE_API_KEY'));

            if ($amount == 0.00) {
                $amount = '0.01';
            }

            // Maak een betaling aan met de gegevens van de bestelling
            $payment = $mollie->payments->create([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => $amount ?? '0.01', // Bedrag dat moet worden betaald
                ],
                'description' => $paymentDescription, // Beschrijving van de betaling
                'redirectUrl' => route('advert.order.complete', ['order_id' => $order->getUuid()]), // URL om naar door te verwijzen na betaling
                'cancelUrl' => route('advert.order.cancel', ['order_id' => $order->getUuid()]), // URL om naar door te verwijzen na annuleren van de betaling
                'webhookUrl' => route('mollie.webhook'), // URL voor server-to-server status updates van Mollie
                'metadata' => [
                    'cookUuid' => $cook->getUuid(),
                    'order_id' => $order->getUuid(),
                    'advertUuid' => $advert->getUuid(),
                    'userUuid' => $user->getUuid(),
                    'client' => $client->getUuid(),
                    'searchString' => $this->request->input('searchString'),
                ],
            ]);

            // sla de payment id op in de order
            $order->update([
                'payment_id' => $payment->id,
            ]);

            DB::commit();

            // Redirect naar de betaalpagina van Mollie
            return redirect($payment->getCheckoutUrl());

        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            DB::rollBack();

            // Als er een fout optreedt bij het aanmaken van de betaling, geef een foutmelding weer
            // En sla de order op met de status failed
            if (isset($order)) {
                $order->update([
                    'payment_state' => Order::FAILED,
                ]);
            }

            return redirect()->back()
                ->with('paymentError', 'Er is een fout opgetreden bij het verwerken van de bestelling.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Order creation failed: '.$e->getMessage(), [
                'advert_uuid' => $uuid,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('paymentError', 'Er is een fout opgetreden bij het verwerken van je bestelling. Probeer het opnieuw.');
        }
    }

    public function cancelCustomOrder()
    {
        $mollie = new \Mollie\Api\MollieApiClient;
        $mollie->setApiKey(env('MOLLIE_API_KEY'));
        $order = $this->orderRepository->find($this->request->query('order_id'));
        $paymentId = $order->payment_id;

        $payment = $mollie->payments->get($paymentId);

        $searchString = $payment->metadata->searchString;
        $cookUuid = $payment->metadata->cookUuid;
        $advertUuid = $payment->metadata->advertUuid;

        if ($payment->status == 'canceled') {
            // Update order status to FAILED when payment is canceled
            $order->update([
                'payment_state' => Order::FAILED,
            ]);

            session()->flash('paymentError', 'De betaling is geannuleerd.');

            return view('customer.order', [
                'searchString' => $payment->metadata->searchString,
                'advert' => $this->advertRepository->find($advertUuid),
            ]);
        }
    }

    public function completeCustomerOrder()
    {
        $mollie = new \Mollie\Api\MollieApiClient;
        $mollie->setApiKey(env('MOLLIE_API_KEY'));

        // EERST Mollie API call (BUITEN transactie)
        try {
            $orderId = $this->request->query('order_id');

            // Haal order op ZONDER lock (nog geen transactie)
            $orderCheck = Order::where('uuid', $orderId)->first();

            if (! $orderCheck) {
                return redirect()->route('home')
                    ->with('error', 'Bestelling niet gevonden.');
            }

            $paymentId = $orderCheck->getPaymentId();
            $payment = $mollie->payments->get($paymentId);

            // Haal metadata op
            $advert = $this->advertRepository->find($payment->metadata->advertUuid);
            $cook = $advert->cook;
            $client = $this->clientRepository->find($payment->metadata->client);
            $user = $cook->user;

            $searchString = $payment->metadata->searchString;
            $cookUuid = $payment->metadata->cookUuid;
            $status = $payment->status;

        } catch (\Exception $e) {
            Log::error('Mollie payment check failed: '.$e->getMessage(), [
                'order_id' => $this->request->query('order_id'),
            ]);

            return redirect()->route('home')
                ->with('error', 'Er is een fout opgetreden bij het ophalen van de betaalstatus.');
        }

        // NU PAS transactie starten (na Mollie API call)
        DB::beginTransaction();

        try {
            // Haal order opnieuw op MET lock
            $order = Order::where('uuid', $orderId)
                ->lockForUpdate()
                ->first();

            if (! $order) {
                DB::rollBack();

                return redirect()->route('home')
                    ->with('error', 'Bestelling niet gevonden.');
            }

            // Kijk of de bestelling is betaald
            if ($payment->isPaid()) {

                // Check dubbele verwerking
                if ($order->getPaymentState() == Order::SUCCEED) {
                    DB::commit();

                    return view('customer.order.complete', [
                        'searchString' => $searchString,
                        'cookUuid' => $cookUuid,
                    ]);
                }

                $userWallet = $this->walletRepository->findWalletForUser($user->getUuid());

                // Als de gebruiker een wallet heeft sla de transactie op
                if (! is_null($userWallet)) {
                    $existingWalletLine = WalletLine::where('order_uuid', $order->uuid)
                        ->lockForUpdate()
                        ->first();

                    if (! $existingWalletLine) {
                        // AANGEPAST: Gebruik dish prijs in plaats van advert prijs
                        $this->walletRepository->addWalletLine(
                            new WalletLineDto(
                                $userWallet->getUuid(),
                                $order->getUuid(),
                                $advert->dish->getPortionPrice() * $order->getPortionAmount(),
                                WalletLine::ON_HOLD
                            )
                        );
                    }
                }

                // Als de bestelling betaald is verander de status van de order naar succeeded
                $order->update([
                    'payment_state' => Order::SUCCEED,
                ]);

                // Check DAC7 thresholds for the cook user after successful payment
                if ($user->hasRole('cook')) {
                    $this->dac7Service->checkUserDac7Thresholds($user);
                }

                // COMMIT VOOR het maken van PDF en versturen emails
                DB::commit();

                // PDF genereren en emails versturen (BUITEN transactie)
                try {
                    // Create a new instance of Dompdf
                    $options = new Options;
                    $options->set('isRemoteEnabled', true);
                    $dompdf = new \Dompdf\Dompdf($options);
                    $options->set('fontDir', public_path('textfonts/'));
                    $options->set('defaultFont', 'Open Sans');

                    // HTML content for the PDF format
                    $html = '
                    <!DOCTYPE html> <html lang="nl-NL"> <head> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <meta charset="utf-8" /> <title> </title> <style> @font-face { font-family: "Open Sans"; src: url("FONTSRC"); } @font-face { font-family: "Open Sans Bold"; src: url("FONTSRCH") format("truetype"); } body { line-height:110%; font-family:Open Sans; font-size:9pt; letter-spacing:0.2pt } h1, h2, h3, h4, h5, h6, p { margin:0pt } li, table { margin-top:0pt; margin-bottom:0pt } h1 { page-break-inside:auto; page-break-after:auto; line-height:110%; font-family:Open Sans; font-size:12pt; font-weight:bold; letter-spacing:0.2pt; color:#000000 } h2 { text-align:right; page-break-inside:auto; page-break-after:auto; line-height:110%; font-family:Open Sans; font-size:9pt; font-weight:normal; text-transform:uppercase; letter-spacing:0.2pt; color:#000000 }
                    h3 { page-break-inside:auto; page-break-after:auto; line-height:110%; font-family:Open Sans; font-size:9pt; font-weight:bold; text-transform:uppercase; letter-spacing:0.2pt; color:#000000 }
                    h4 { text-align:center; page-break-inside:avoid; page-break-after:avoid; line-height:110%; font-family:Open Sans; font-size:9pt; font-weight:bold; font-style:normal; text-transform:uppercase; letter-spacing:0.2pt; color:#000000 }
                    h5 { margin-top:2pt; page-break-inside:avoid; page-break-after:avoid; line-height:110%; font-family:Open Sans; font-size:9pt; font-weight:normal; letter-spacing:0.2pt; color:#365f91 }
                    h6 { margin-top:2pt; page-break-inside:avoid; page-break-after:avoid; line-height:110%; font-family:Open Sans; font-size:9pt; font-weight:normal; font-style:normal; letter-spacing:0.2pt; color:#243f60 }
                    .Heading7 { margin-top:2pt; page-break-inside:avoid; page-break-after:avoid; line-height:110%; font-family:Open Sans; font-size:9pt; font-weight:normal; font-style:italic; letter-spacing:0.2pt; color:#243f60 }
                    .Heading8 { margin-top:2pt; page-break-inside:avoid; page-break-after:avoid; line-height:110%; font-family:Open Sans; font-size:9pt; font-weight:normal; letter-spacing:0.2pt; color:#272727 }
                    .Heading9 { margin-top:2pt; page-break-inside:avoid; page-break-after:avoid; line-height:110%; font-family:Open Sans; font-size:9pt; font-weight:normal; font-style:italic; letter-spacing:0.2pt; color:#272727 }
                    .BalloonText { line-height:normal; font-family:"Lucida Grande"; font-size:9pt; letter-spacing:0.2pt } .Bedankt { margin-top:30pt; text-align:center; line-height:110%; font-size:9pt; font-weight:bold; text-transform:uppercase; letter-spacing:0.2pt }
                    .Bedrag { text-align:right; line-height:110%; font-size:9pt; letter-spacing:0.2pt } .BlockText { margin-right:57.6pt; margin-left:57.6pt; line-height:110%; border:0.75pt solid #365f91; padding:10pt; font-size:9pt; font-style:italic; letter-spacing:0.2pt; color:#365f91 }
                    .Footer { line-height:normal; font-size:9pt; letter-spacing:0.2pt } .Header { line-height:normal; font-size:9pt; letter-spacing:0.2pt } .Hoeveelheid { text-align:center; line-height:110%; font-size:9pt; letter-spacing:0.2pt } .Instructies { margin-top:12pt; line-height:110%; font-size:9pt; letter-spacing:0.2pt }
                    .IntenseQuote { margin:18pt 43.2pt; text-align:center; line-height:110%; border-top:0.75pt solid #365f91; border-bottom:0.75pt solid #365f91; padding-top:10pt; padding-bottom:10pt; font-size:9pt; font-style:italic; letter-spacing:0.2pt; color:#365f91 }
                    .Slogan { margin-bottom:12pt; line-height:110%; font-size:9pt; font-style:italic; letter-spacing:0.2pt; color:#595959 } .Title { margin-bottom:20pt; text-align:right; line-height:110%; font-family:Open Sans !important; font-size:20pt; font-weight:bold; text-transform:uppercase; letter-spacing:0.2pt; color:#595959 }
                    span.BookTitle { font-weight:bold; font-style:italic; letter-spacing:0pt } span.BalloonTextChar { font-family:"Lucida Grande"; letter-spacing:0.2pt } span.FooterChar { font-size:9pt } span.HeaderChar { font-size:9pt } span.Heading1Char { font-family:Open Sans; font-size:12pt; font-weight:bold }
                    span.Heading2Char { font-size:9pt; text-transform:uppercase } span.Heading3Char { font-size:9pt; font-weight:bold; text-transform:uppercase } span.Heading4Char { font-family:Open Sans; font-size:9pt; font-weight:bold; text-transform:uppercase } span.Heading5Char { font-family:Open Sans; letter-spacing:0.2pt; color:#365f91 }
                    span.Heading6Char { font-family:Open Sans; letter-spacing:0.2pt; color:#243f60 } span.Heading7Char { font-family:Open Sans; font-style:italic; letter-spacing:0.2pt; color:#243f60 } span.Heading8Char { font-family:Open Sans; letter-spacing:0.2pt; color:#272727 }
                    span.Heading9Char { font-family:Open Sans; font-style:italic; letter-spacing:0.2pt; color:#272727 } span.IntenseEmphasis { font-style:italic; color:#365f91 } span.IntenseQuoteChar { font-style:italic; letter-spacing:0.2pt; color:#365f91 } span.IntenseReference { font-weight:bold; font-variant:small-caps; text-transform:none; letter-spacing:0pt; color:#365f91 }
                    span.Niet-omgezettevermelding1 { color:#595959; background-color:#e1dfdd } span.PlaceholderText { color:#808080 } span.TitleChar { font-family:Open Sans; font-size:20pt; font-weight:bold; text-transform:uppercase; letter-spacing:0.2pt; color:#595959 } </style>
                    </head> <body> <table style="width:487.35pt; margin-right:9pt; margin-left:9pt; margin-top: 90px; border-collapse:collapse; float:left"> <tr style="height:79.5pt"> <td style="width:240.8pt; padding-right:5.4pt; padding-left:5.4pt; vertical-align:top">
                    <h1 style="margin-bottom: 37px; margin-top: 7px;"> THUISKOKNAAM </h1> <p> STRAATNAAM THUISKOK </p> <p> POSTCODE + STAD THUISKOK </p> <p> TELEFOON THUISKOK </p> </td> <td style="width:224.95pt; padding-right:5.4pt; padding-left:5.4pt; vertical-align:top">
                    <p class="Title"> FACTUUR </p> <h2> Factuurnummer: FACTUURNUMER </h2> <h2> Factuurdatum: FACTUURDATUM </h2> </td> </tr> <tr style="height:72pt"> <td style="width:240.8pt; padding-right:5.4pt; padding-left:5.4pt; vertical-align:top"> <h3> &#xa0; </h3>
                    <h3> Aan: </h3> <p> NAAM KLANT </p> </td> <td style="width:224.95pt; padding-right:5.4pt; padding-left:5.4pt; vertical-align:top"> <h3> &#xa0; </h3> <p> &#xa0; </p> <p> &#xa0; </p> <p> &#xa0; </p> <p> &#xa0; </p> </td> </tr> </table> <img src="IMGSRC" width="250" alt="logo" style="margin-left: -450px"/>
                    <p style="height: 130px;"></p> <p> &#xa0; </p> <p> &#xa0; </p> <p> &#xa0; </p> <table style="width:496.3pt; margin-top: 50px; border-collapse:collapse"> <tr style="height:5.5pt"> <td style="width:101.95pt; border:0.75pt solid #a6a6a6; padding:3.6pt 5.38pt; vertical-align:middle">
                    <h4 style="line-height:normal"> HOEVEELHEID </h4> </td> <td style="width:216.4pt; border:0.75pt solid #a6a6a6; padding:3.6pt 5.38pt; vertical-align:middle"> <h4 style="line-height:normal"> BESCHRIJVING </h4> </td> <td style="width:52.25pt; border:0.75pt solid #a6a6a6; padding:3.6pt 5.38pt; vertical-align:middle">
                    <h4 style="line-height:normal"> PRIJS PER EENHEID </h4> </td> <td style="width:78.95pt; border:0.75pt solid #a6a6a6; padding:3.6pt 5.38pt; vertical-align:middle"> <h4 style="line-height:normal"> TOTAAL </h4> </td> </tr> <tr> <td style="width:91.85pt; border:0.75pt solid #a6a6a6; padding:3.6pt 10.42pt; vertical-align:middle">
                    <p class="Hoeveelheid" style="line-height:normal"> AANTAL PORTIES </p> </td> <td style="width:206.3pt; border:0.75pt solid #a6a6a6; padding:3.6pt 10.42pt; vertical-align:middle"> <p style="line-height:normal"> GERECHTNAAM </p> </td>
                    <td style="width:42.15pt; border:0.75pt solid #a6a6a6; padding:3.6pt 10.42pt; vertical-align:middle"> <p class="Bedrag" style="line-height:normal"> PRIJDPEREENHEID </p> </td> <td style="width:68.85pt; border:0.75pt solid #a6a6a6; padding:3.6pt 10.42pt; vertical-align:middle">
                    <p class="Bedrag" style="line-height:normal"> TOTAALPRIJS </p> </td> </tr> </table> <table style="width:487.72pt; border-collapse:collapse"> <tr> <td style="width:79.35pt; padding:3.6pt 5.75pt; vertical-align:top"> <p> <strong>&#xa0;</strong> </p> </td>
                    <td style="width:294.55pt; border-right:0.75pt solid #a6a6a6; padding:3.6pt 5.38pt 3.6pt 5.75pt; vertical-align:top"> <h2> SUBTOTAAL </h2> </td> <td style="width:68.85pt; border-right:0.75pt solid #a6a6a6; border-left:0.75pt solid #a6a6a6; border-bottom:0.75pt solid #a6a6a6; padding:3.6pt 10.42pt; vertical-align:top">
                    <p class="Bedrag"> SUBPRIJS </p> </td> </tr> <tr> <td style="width:79.35pt; padding:3.6pt 5.75pt; vertical-align:top"> <p> &#xa0; </p> </td> <td style="width:294.55pt; border-right:0.75pt solid #a6a6a6; padding:3.6pt 5.38pt 3.6pt 5.75pt; vertical-align:top">
                    <h2> BTW </h2> </td> <td style="width:68.85pt; border:0.75pt solid #a6a6a6; padding:3.6pt 10.42pt; vertical-align:top"> <p class="Bedrag"> BTWPRIJS </p> </td> </tr> <tr> <td style="width:79.35pt; padding:3.6pt 5.75pt; vertical-align:top"> <p> &#xa0; </p> </td> <td style="width:294.55pt; border-right:0.75pt solid #a6a6a6; padding:3.6pt 5.38pt 3.6pt 5.75pt; vertical-align:top">
                    <h2> <strong>TOTAAL Betaald</strong> </h2> </td> <td style="width:68.85pt; border:0.75pt solid #a6a6a6; padding:3.6pt 10.42pt; vertical-align:top"> <p class="Bedrag"> TOTAALBETAALD </p> </td> </tr> </table> <p class="Instructies"> &#xa0; </p> <p class="Instructies" style="margin-top:0pt"> &#xa0; </p>
                    <p class="Instructies" style="margin-top:0pt"> Schrijf alle cheques uit ten gunste van THUISKOKNAAM </p> <p class="Instructies" style="margin-top:0pt"> Indien u vragen hebt over deze factuur, kunt u contact opnemen met THUISKOKNAAM, Telefoonnummer: TELEFOON THUISKOK, Mail: MAIL THUISKOK </p>
                    <p class="Bedankt"> Bedankt voor uw klandizie </p> </body> </html>';

                    $imgsrc = public_path('img/logo.png');
                    $fontsrc = public_path('textfonts/OpenSans-ExtraBold.ttf');
                    $fontsrch = public_path('textfonts/OpenSans-Medium.ttf');
                    $dishPrice = $advert->dish->getPortionPrice();

                    $replacements = [
                        'THUISKOKNAAM' => $cook->user->getUsername(),
                        'STRAATNAAM THUISKOK' => $cook->getStreet().' '.$cook->getHouseNumber().($cook->getAddition() ?? ''),
                        'POSTCODE + STAD THUISKOK' => $cook->getPostalCode().' '.$cook->getCity(),
                        'TELEFOON THUISKOK' => $cook->user->userProfile->getPhoneNumber(),
                        'NAAMKLANT' => $client->getName(),
                        'AANTAL PORTIES' => $order->getPortionAmount(),
                        'GERECHTNAAM' => $advert->dish->getTitle(),
                        'NAAM KLANT' => $client->getName(),
                        'MAIL THUISKOK' => $cook->user->getEmail(),
                        'FACTUURNUMER' => $order->getUuid(),
                        'FACTUURDATUM' => $order->getCreatedAt()->format('d-m-Y'),
                        'PRIJDPEREENHEID' => '€'.number_format($dishPrice, 2, ',', '.'),
                        'TOTAALPRIJS' => '€'.number_format($dishPrice * $order->getPortionAmount(), 2, ',', '.'),
                        'SUBPRIJS' => '€'.number_format($dishPrice * $order->getPortionAmount(), 2, ',', '.'),
                        'BTWPRIJS' => '€0,00',
                        'TOTAALBETAALD' => '€'.number_format($dishPrice * $order->getPortionAmount(), 2, ',', '.'),
                        'IMGSRC' => $imgsrc.'',
                        'FONTSRCH' => $fontsrc.'',
                        'FONTSRC' => $fontsrch.'',
                    ];

                    foreach ($replacements as $placeholder => $replacement) {
                        $html = str_replace($placeholder, $replacement, $html);
                    }

                    $dompdf->loadHtml($html);
                    $dompdf->setPaper('A4', 'portrait');
                    $dompdf->render();

                    $tempDir = storage_path('app/temp');
                    if (! file_exists($tempDir)) {
                        mkdir($tempDir, 0755, true);
                    }

                    $tempPdfFile = $tempDir.'/factuur_deburenkoken_'.$order->getUuid().'.pdf';
                    file_put_contents($tempPdfFile, $dompdf->output());

                    // Send emails
                    if ($cook->getMailOrder() && $cook->user->not_verified_at === null) {
                        Mail::to(
                            $user->getEmail(),
                            $user->getUsername()
                        )->send(new OrderCreateCook(
                            $order,
                            $advert,
                            $client,
                            $user
                        ));
                    }

                    Mail::to(
                        $client->getEmail(),
                        $client->getName()
                    )->send(new OrderCreateCustomer(
                        $order,
                        $advert,
                        $user,
                        $tempPdfFile,
                    ));

                    if (file_exists($tempPdfFile)) {
                        unlink($tempPdfFile);
                    }
                } catch (\Exception $e) {
                    Log::error('Error generating PDF or sending emails: '.$e->getMessage());
                    // Niet fataal - order is al verwerkt
                }

            } else {
                // als de bestelling niet betaald is sla hem op als een gefaalde bestelling
                $order->update([
                    'payment_state' => Order::FAILED,
                ]);

                DB::commit();

                // Als de bestelling niet betaald is stuur hem terug met een foutmelding
                $url = route('advert.order', ['uuid' => $advert->getUuid()]).'?calculatedDistance='.$advert->getDistance().'&searchString='.$searchString;
                if ($status === 'cancelled') {
                    $message = 'De betaling is geannuleerd.';
                } else {
                    $message = 'Er is een fout opgetreden bij het verwerken van de bestelling.';
                }

                return redirect()->to($url)->with('error', 'Er is een fout opgetreden bij het verwerken van de bestelling.');
            }

            return view('customer.order.complete', [
                'searchString' => $searchString,
                'cookUuid' => $cookUuid,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Order completion failed: '.$e->getMessage(), [
                'order_id' => $this->request->query('order_id'),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('home')
                ->with('error', 'Er is een fout opgetreden bij het afronden van je bestelling.');
        }
    }

    public function submitMailingList(string $cookUuid): RedirectResponse
    {
        $this->request->validate(['email' => 'required', 'email:rfc,dns',  'regex:/^[^@]+(\.[^@]+)*@[^@]+\.[^@]+$/']);

        $cook = $this->cookRepository->find($cookUuid);
        $email = $this->request->input('email');

        $client = $this->clientRepository->findByEmail($email);
        // als er geen client is met dit emailadres, maak een client aan
        if (! $client) {
            $client = $this->clientRepository->create(
                new ClientDto(
                    '',
                    $email,
                    ''
                )
            );
            $this->mailingListRepository->create(
                new MailingListDto(
                    $cook,
                    $client
                )
            );
        } else {
            // als er wel een client is met dit emailadres, maar nog niet in de mailinglist, voeg toe aan mailinglist
            if (! $this->mailingListRepository->emailExistsInMailingList($email)) {
                $this->mailingListRepository->create(
                    new MailingListDto(
                        $cook,
                        $client
                    )
                );
            }

        }

        return redirect()->route('search.cooks.detail', $cookUuid)->with('message', 'Aanmelden gelukt');
    }

    public function unsubscribeFromMailingList(string $cookUuid, string $clientUuid): RedirectResponse
    {
        $cook = $this->cookRepository->find($cookUuid);
        $client = $this->clientRepository->find($clientUuid);

        if (! $cook || ! $client) {
            throw new ModelNotFoundException;
        }

        // Unsubscribe the client from the mailing list
        $this->mailingListRepository->unsubscribeClient($cook, $client);

        return redirect()->route('search.cooks.detail', $cookUuid)->with('message', 'Je bent succesvol uitgeschreven van de mailinglijst.');
    }

    /**
     * Handle Mollie webhook callbacks.
     * Dit wordt aangeroepen door Mollie wanneer een betaling status verandert.
     *
     * BELANGRIJK: Dit is een server-to-server call van Mollie, geen browser request.
     * De webhook wordt aangeroepen ongeacht of de klant terugkeert naar de website.
     */
    public function handleMollieWebhook(Request $request): Response
    {
        // Mollie stuurt alleen een 'id' parameter
        $paymentId = $request->input('id');

        Log::info('Mollie webhook received', ['payment_id' => $paymentId]);

        if (! $paymentId) {
            // Altijd 200 OK retourneren om geen info te lekken naar potentiële aanvallers
            return response('OK', 200);
        }

        try {
            $mollie = new \Mollie\Api\MollieApiClient;
            $mollie->setApiKey(env('MOLLIE_API_KEY'));

            $payment = $mollie->payments->get($paymentId);

            Log::info('Mollie webhook payment status', [
                'payment_id' => $paymentId,
                'status' => $payment->status,
            ]);

            // Zoek de order op basis van payment_id
            $order = Order::where('payment_id', $paymentId)->first();

            if (! $order) {
                Log::warning('Webhook received for unknown payment', ['payment_id' => $paymentId]);

                // Retourneer 200 OK om geen informatie te lekken
                return response('OK', 200);
            }

            // Verwerk alleen als status daadwerkelijk is gewijzigd
            if ($payment->isPaid() && $order->getPaymentState() !== Order::SUCCEED) {
                $this->processSuccessfulPaymentFromWebhook($order, $payment);
            } elseif ($payment->isFailed() && $order->getPaymentState() === Order::IN_PROCESS) {
                $order->update(['payment_state' => Order::FAILED]);
                Log::info('Order marked as failed via webhook', ['order_uuid' => $order->getUuid()]);
            } elseif ($payment->isCanceled() && $order->getPaymentState() === Order::IN_PROCESS) {
                $order->update(['payment_state' => Order::FAILED]);
                Log::info('Order marked as cancelled via webhook', ['order_uuid' => $order->getUuid()]);
            } elseif ($payment->isExpired() && $order->getPaymentState() === Order::IN_PROCESS) {
                $order->update(['payment_state' => Order::FAILED]);
                Log::info('Order marked as expired via webhook', ['order_uuid' => $order->getUuid()]);
            }

            return response('OK', 200);

        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            Log::error('Mollie API error in webhook: '.$e->getMessage(), [
                'payment_id' => $paymentId,
            ]);

            // Retourneer 500 zodat Mollie het opnieuw probeert
            return response('Error', 500);

        } catch (\Exception $e) {
            Log::error('Webhook processing failed: '.$e->getMessage(), [
                'payment_id' => $paymentId,
                'trace' => $e->getTraceAsString(),
            ]);

            // Retourneer 500 zodat Mollie het opnieuw probeert bij tijdelijke fouten
            return response('Error', 500);
        }
    }

    /**
     * Verwerk een succesvolle betaling vanuit de webhook.
     * Deze methode wordt aangeroepen door de webhook handler en voert alle
     * noodzakelijke acties uit: wallet line toevoegen, order status updaten,
     * DAC7 check, en emails versturen.
     */
    private function processSuccessfulPaymentFromWebhook(Order $order, $payment): void
    {
        DB::beginTransaction();

        try {
            // Lock de order om race conditions te voorkomen (webhook + redirect kunnen tegelijk komen)
            $order = Order::where('uuid', $order->getUuid())
                ->lockForUpdate()
                ->first();

            // Check opnieuw na lock (idempotentie)
            if ($order->getPaymentState() === Order::SUCCEED) {
                DB::commit();
                Log::info('Order already processed, skipping', ['order_uuid' => $order->getUuid()]);

                return;
            }

            // Haal benodigde data op uit metadata
            $advert = $this->advertRepository->find($payment->metadata->advertUuid);
            $cook = $advert->cook;
            $user = $cook->user;
            $client = $this->clientRepository->find($payment->metadata->client);

            // Wallet line toevoegen
            $userWallet = $this->walletRepository->findWalletForUser($user->getUuid());
            if ($userWallet) {
                $existingWalletLine = WalletLine::where('order_uuid', $order->uuid)
                    ->lockForUpdate()
                    ->first();

                if (! $existingWalletLine) {
                    $this->walletRepository->addWalletLine(
                        new WalletLineDto(
                            $userWallet->getUuid(),
                            $order->getUuid(),
                            $advert->dish->getPortionPrice() * $order->getPortionAmount(),
                            WalletLine::ON_HOLD
                        )
                    );
                }
            }

            // Update order status
            $order->update(['payment_state' => Order::SUCCEED]);

            // Check DAC7 thresholds for the cook user after successful payment
            if ($user->hasRole('cook')) {
                $this->dac7Service->checkUserDac7Thresholds($user);
            }

            DB::commit();

            Log::info('Order successfully processed via webhook', ['order_uuid' => $order->getUuid()]);

            // Emails versturen (buiten transactie om geen locks vast te houden)
            $this->sendOrderConfirmationEmails($order, $advert, $cook, $client, $user);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process successful payment in webhook: '.$e->getMessage(), [
                'order_uuid' => $order->getUuid(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Verstuur order bevestigingsemails naar kok en klant.
     * Dit is geëxtraheerd uit completeCustomerOrder zodat het ook vanuit de webhook kan worden aangeroepen.
     */
    private function sendOrderConfirmationEmails(Order $order, $advert, $cook, $client, $user): void
    {
        try {
            // Create a new instance of Dompdf
            $options = new Options;
            $options->set('isRemoteEnabled', true);
            $dompdf = new \Dompdf\Dompdf($options);
            $options->set('fontDir', public_path('textfonts/'));
            $options->set('defaultFont', 'Open Sans');

            // HTML content for the PDF format
            $html = '
            <!DOCTYPE html> <html lang="nl-NL"> <head> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <meta charset="utf-8" /> <title> </title> <style> @font-face { font-family: "Open Sans"; src: url("FONTSRC"); } @font-face { font-family: "Open Sans Bold"; src: url("FONTSRCH") format("truetype"); } body { line-height:110%; font-family:Open Sans; font-size:9pt; letter-spacing:0.2pt } h1, h2, h3, h4, h5, h6, p { margin:0pt } li, table { margin-top:0pt; margin-bottom:0pt } h1 { page-break-inside:auto; page-break-after:auto; line-height:110%; font-family:Open Sans; font-size:12pt; font-weight:bold; letter-spacing:0.2pt; color:#000000 } h2 { text-align:right; page-break-inside:auto; page-break-after:auto; line-height:110%; font-family:Open Sans; font-size:9pt; font-weight:normal; text-transform:uppercase; letter-spacing:0.2pt; color:#000000 }
            h3 { page-break-inside:auto; page-break-after:auto; line-height:110%; font-family:Open Sans; font-size:9pt; font-weight:bold; text-transform:uppercase; letter-spacing:0.2pt; color:#000000 }
            h4 { text-align:center; page-break-inside:avoid; page-break-after:avoid; line-height:110%; font-family:Open Sans; font-size:9pt; font-weight:bold; font-style:normal; text-transform:uppercase; letter-spacing:0.2pt; color:#000000 }
            h5 { margin-top:2pt; page-break-inside:avoid; page-break-after:avoid; line-height:110%; font-family:Open Sans; font-size:9pt; font-weight:normal; letter-spacing:0.2pt; color:#365f91 }
            h6 { margin-top:2pt; page-break-inside:avoid; page-break-after:avoid; line-height:110%; font-family:Open Sans; font-size:9pt; font-weight:normal; font-style:normal; letter-spacing:0.2pt; color:#243f60 }
            .Heading7 { margin-top:2pt; page-break-inside:avoid; page-break-after:avoid; line-height:110%; font-family:Open Sans; font-size:9pt; font-weight:normal; font-style:italic; letter-spacing:0.2pt; color:#243f60 }
            .Heading8 { margin-top:2pt; page-break-inside:avoid; page-break-after:avoid; line-height:110%; font-family:Open Sans; font-size:9pt; font-weight:normal; letter-spacing:0.2pt; color:#272727 }
            .Heading9 { margin-top:2pt; page-break-inside:avoid; page-break-after:avoid; line-height:110%; font-family:Open Sans; font-size:9pt; font-weight:normal; font-style:italic; letter-spacing:0.2pt; color:#272727 }
            .BalloonText { line-height:normal; font-family:"Lucida Grande"; font-size:9pt; letter-spacing:0.2pt } .Bedankt { margin-top:30pt; text-align:center; line-height:110%; font-size:9pt; font-weight:bold; text-transform:uppercase; letter-spacing:0.2pt }
            .Bedrag { text-align:right; line-height:110%; font-size:9pt; letter-spacing:0.2pt } .BlockText { margin-right:57.6pt; margin-left:57.6pt; line-height:110%; border:0.75pt solid #365f91; padding:10pt; font-size:9pt; font-style:italic; letter-spacing:0.2pt; color:#365f91 }
            .Footer { line-height:normal; font-size:9pt; letter-spacing:0.2pt } .Header { line-height:normal; font-size:9pt; letter-spacing:0.2pt } .Hoeveelheid { text-align:center; line-height:110%; font-size:9pt; letter-spacing:0.2pt } .Instructies { margin-top:12pt; line-height:110%; font-size:9pt; letter-spacing:0.2pt }
            .IntenseQuote { margin:18pt 43.2pt; text-align:center; line-height:110%; border-top:0.75pt solid #365f91; border-bottom:0.75pt solid #365f91; padding-top:10pt; padding-bottom:10pt; font-size:9pt; font-style:italic; letter-spacing:0.2pt; color:#365f91 }
            .Slogan { margin-bottom:12pt; line-height:110%; font-size:9pt; font-style:italic; letter-spacing:0.2pt; color:#595959 } .Title { margin-bottom:20pt; text-align:right; line-height:110%; font-family:Open Sans !important; font-size:20pt; font-weight:bold; text-transform:uppercase; letter-spacing:0.2pt; color:#595959 }
            span.BookTitle { font-weight:bold; font-style:italic; letter-spacing:0pt } span.BalloonTextChar { font-family:"Lucida Grande"; letter-spacing:0.2pt } span.FooterChar { font-size:9pt } span.HeaderChar { font-size:9pt } span.Heading1Char { font-family:Open Sans; font-size:12pt; font-weight:bold }
            span.Heading2Char { font-size:9pt; text-transform:uppercase } span.Heading3Char { font-size:9pt; font-weight:bold; text-transform:uppercase } span.Heading4Char { font-family:Open Sans; font-size:9pt; font-weight:bold; text-transform:uppercase } span.Heading5Char { font-family:Open Sans; letter-spacing:0.2pt; color:#365f91 }
            span.Heading6Char { font-family:Open Sans; letter-spacing:0.2pt; color:#243f60 } span.Heading7Char { font-family:Open Sans; font-style:italic; letter-spacing:0.2pt; color:#243f60 } span.Heading8Char { font-family:Open Sans; letter-spacing:0.2pt; color:#272727 }
            span.Heading9Char { font-family:Open Sans; font-style:italic; letter-spacing:0.2pt; color:#272727 } span.IntenseEmphasis { font-style:italic; color:#365f91 } span.IntenseQuoteChar { font-style:italic; letter-spacing:0.2pt; color:#365f91 } span.IntenseReference { font-weight:bold; font-variant:small-caps; text-transform:none; letter-spacing:0pt; color:#365f91 }
            span.Niet-omgezettevermelding1 { color:#595959; background-color:#e1dfdd } span.PlaceholderText { color:#808080 } span.TitleChar { font-family:Open Sans; font-size:20pt; font-weight:bold; text-transform:uppercase; letter-spacing:0.2pt; color:#595959 } </style>
            </head> <body> <table style="width:487.35pt; margin-right:9pt; margin-left:9pt; margin-top: 90px; border-collapse:collapse; float:left"> <tr style="height:79.5pt"> <td style="width:240.8pt; padding-right:5.4pt; padding-left:5.4pt; vertical-align:top">
            <h1 style="margin-bottom: 37px; margin-top: 7px;"> THUISKOKNAAM </h1> <p> STRAATNAAM THUISKOK </p> <p> POSTCODE + STAD THUISKOK </p> <p> TELEFOON THUISKOK </p> </td> <td style="width:224.95pt; padding-right:5.4pt; padding-left:5.4pt; vertical-align:top">
            <p class="Title"> FACTUUR </p> <h2> Factuurnummer: FACTUURNUMER </h2> <h2> Factuurdatum: FACTUURDATUM </h2> </td> </tr> <tr style="height:72pt"> <td style="width:240.8pt; padding-right:5.4pt; padding-left:5.4pt; vertical-align:top"> <h3> &#xa0; </h3>
            <h3> Aan: </h3> <p> NAAM KLANT </p> </td> <td style="width:224.95pt; padding-right:5.4pt; padding-left:5.4pt; vertical-align:top"> <h3> &#xa0; </h3> <p> &#xa0; </p> <p> &#xa0; </p> <p> &#xa0; </p> <p> &#xa0; </p> </td> </tr> </table> <img src="IMGSRC" width="250" alt="logo" style="margin-left: -450px"/>
            <p style="height: 130px;"></p> <p> &#xa0; </p> <p> &#xa0; </p> <p> &#xa0; </p> <table style="width:496.3pt; margin-top: 50px; border-collapse:collapse"> <tr style="height:5.5pt"> <td style="width:101.95pt; border:0.75pt solid #a6a6a6; padding:3.6pt 5.38pt; vertical-align:middle">
            <h4 style="line-height:normal"> HOEVEELHEID </h4> </td> <td style="width:216.4pt; border:0.75pt solid #a6a6a6; padding:3.6pt 5.38pt; vertical-align:middle"> <h4 style="line-height:normal"> BESCHRIJVING </h4> </td> <td style="width:52.25pt; border:0.75pt solid #a6a6a6; padding:3.6pt 5.38pt; vertical-align:middle">
            <h4 style="line-height:normal"> PRIJS PER EENHEID </h4> </td> <td style="width:78.95pt; border:0.75pt solid #a6a6a6; padding:3.6pt 5.38pt; vertical-align:middle"> <h4 style="line-height:normal"> TOTAAL </h4> </td> </tr> <tr> <td style="width:91.85pt; border:0.75pt solid #a6a6a6; padding:3.6pt 10.42pt; vertical-align:middle">
            <p class="Hoeveelheid" style="line-height:normal"> AANTAL PORTIES </p> </td> <td style="width:206.3pt; border:0.75pt solid #a6a6a6; padding:3.6pt 10.42pt; vertical-align:middle"> <p style="line-height:normal"> GERECHTNAAM </p> </td>
            <td style="width:42.15pt; border:0.75pt solid #a6a6a6; padding:3.6pt 10.42pt; vertical-align:middle"> <p class="Bedrag" style="line-height:normal"> PRIJDPEREENHEID </p> </td> <td style="width:68.85pt; border:0.75pt solid #a6a6a6; padding:3.6pt 10.42pt; vertical-align:middle">
            <p class="Bedrag" style="line-height:normal"> TOTAALPRIJS </p> </td> </tr> </table> <table style="width:487.72pt; border-collapse:collapse"> <tr> <td style="width:79.35pt; padding:3.6pt 5.75pt; vertical-align:top"> <p> <strong>&#xa0;</strong> </p> </td>
            <td style="width:294.55pt; border-right:0.75pt solid #a6a6a6; padding:3.6pt 5.38pt 3.6pt 5.75pt; vertical-align:top"> <h2> SUBTOTAAL </h2> </td> <td style="width:68.85pt; border-right:0.75pt solid #a6a6a6; border-left:0.75pt solid #a6a6a6; border-bottom:0.75pt solid #a6a6a6; padding:3.6pt 10.42pt; vertical-align:top">
            <p class="Bedrag"> SUBPRIJS </p> </td> </tr> <tr> <td style="width:79.35pt; padding:3.6pt 5.75pt; vertical-align:top"> <p> &#xa0; </p> </td> <td style="width:294.55pt; border-right:0.75pt solid #a6a6a6; padding:3.6pt 5.38pt 3.6pt 5.75pt; vertical-align:top">
            <h2> BTW </h2> </td> <td style="width:68.85pt; border:0.75pt solid #a6a6a6; padding:3.6pt 10.42pt; vertical-align:top"> <p class="Bedrag"> BTWPRIJS </p> </td> </tr> <tr> <td style="width:79.35pt; padding:3.6pt 5.75pt; vertical-align:top"> <p> &#xa0; </p> </td> <td style="width:294.55pt; border-right:0.75pt solid #a6a6a6; padding:3.6pt 5.38pt 3.6pt 5.75pt; vertical-align:top">
            <h2> <strong>TOTAAL Betaald</strong> </h2> </td> <td style="width:68.85pt; border:0.75pt solid #a6a6a6; padding:3.6pt 10.42pt; vertical-align:top"> <p class="Bedrag"> TOTAALBETAALD </p> </td> </tr> </table> <p class="Instructies"> &#xa0; </p> <p class="Instructies" style="margin-top:0pt"> &#xa0; </p>
            <p class="Instructies" style="margin-top:0pt"> Schrijf alle cheques uit ten gunste van THUISKOKNAAM </p> <p class="Instructies" style="margin-top:0pt"> Indien u vragen hebt over deze factuur, kunt u contact opnemen met THUISKOKNAAM, Telefoonnummer: TELEFOON THUISKOK, Mail: MAIL THUISKOK </p>
            <p class="Bedankt"> Bedankt voor uw klandizie </p> </body> </html>';

            $imgsrc = public_path('img/logo.png');
            $fontsrc = public_path('textfonts/OpenSans-ExtraBold.ttf');
            $fontsrch = public_path('textfonts/OpenSans-Medium.ttf');
            $dishPrice = $advert->dish->getPortionPrice();

            $replacements = [
                'THUISKOKNAAM' => $cook->user->getUsername(),
                'STRAATNAAM THUISKOK' => $cook->getStreet().' '.$cook->getHouseNumber().($cook->getAddition() ?? ''),
                'POSTCODE + STAD THUISKOK' => $cook->getPostalCode().' '.$cook->getCity(),
                'TELEFOON THUISKOK' => $cook->user->userProfile->getPhoneNumber(),
                'NAAMKLANT' => $client->getName(),
                'AANTAL PORTIES' => $order->getPortionAmount(),
                'GERECHTNAAM' => $advert->dish->getTitle(),
                'NAAM KLANT' => $client->getName(),
                'MAIL THUISKOK' => $cook->user->getEmail(),
                'FACTUURNUMER' => $order->getUuid(),
                'FACTUURDATUM' => $order->getCreatedAt()->format('d-m-Y'),
                'PRIJDPEREENHEID' => '€'.number_format($dishPrice, 2, ',', '.'),
                'TOTAALPRIJS' => '€'.number_format($dishPrice * $order->getPortionAmount(), 2, ',', '.'),
                'SUBPRIJS' => '€'.number_format($dishPrice * $order->getPortionAmount(), 2, ',', '.'),
                'BTWPRIJS' => '€0,00',
                'TOTAALBETAALD' => '€'.number_format($dishPrice * $order->getPortionAmount(), 2, ',', '.'),
                'IMGSRC' => $imgsrc.'',
                'FONTSRCH' => $fontsrc.'',
                'FONTSRC' => $fontsrch.'',
            ];

            foreach ($replacements as $placeholder => $replacement) {
                $html = str_replace($placeholder, $replacement, $html);
            }

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $tempDir = storage_path('app/temp');
            if (! file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $tempPdfFile = $tempDir.'/factuur_deburenkoken_'.$order->getUuid().'.pdf';
            file_put_contents($tempPdfFile, $dompdf->output());

            // Send emails
            if ($cook->getMailOrder() && $cook->user->not_verified_at === null) {
                Mail::to(
                    $user->getEmail(),
                    $user->getUsername()
                )->send(new OrderCreateCook(
                    $order,
                    $advert,
                    $client,
                    $user
                ));
            }

            Mail::to(
                $client->getEmail(),
                $client->getName()
            )->send(new OrderCreateCustomer(
                $order,
                $advert,
                $user,
                $tempPdfFile,
            ));

            if (file_exists($tempPdfFile)) {
                unlink($tempPdfFile);
            }

            Log::info('Order confirmation emails sent', ['order_uuid' => $order->getUuid()]);

        } catch (\Exception $e) {
            Log::error('Error generating PDF or sending emails: '.$e->getMessage(), [
                'order_uuid' => $order->getUuid(),
            ]);
            // Niet fataal - order is al verwerkt
        }
    }
}
