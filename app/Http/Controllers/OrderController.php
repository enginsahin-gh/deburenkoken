<?php

namespace App\Http\Controllers;

use App\Dtos\WalletLineDto;
use App\Mail\AlertAdminUserForSuspiciousCancellations;
use App\Mail\AlertNextCancellationCost;
use App\Mail\CancellationReviewMail;
use App\Mail\OrderCancelCustomer;
use App\Mail\OrderCookCancelled;
use App\Mail\OrderCustomerCancelled;
use App\Mail\OrderDocumentMail;
use App\Mail\ReviewMail;
use App\Models\Advert;
use App\Models\Cook;
use App\Models\Order;
use App\Models\User;
use App\Models\WalletLine;
use App\Repositories\BankingRepository;
use App\Repositories\ClientRepository;
use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use App\Services\Dac7Service;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Mollie\Api\MollieApiClient;

class OrderController extends Controller
{
    private Request $request;

    private OrderRepository $orderRepository;

    private WalletRepository $walletRepository;

    private UserRepository $userRepository;

    private ClientRepository $clientRepository;

    private BankingRepository $bankingRepository;

    private int $cancellationLimit = 10;          // Voor cook annuleringen

    private int $dailyCancellationLimit = 5;     // Voor klant e-mail annuleringen

    private int $ibanThreshold = 5;              // Voor klant IBAN annuleringen

    private int $emailThreshold = 5;             // Voor email alerts

    private int $totalThreshold = 10;            // Voor totaal aantal annuleringen

    public function __construct(
        Request $request,
        OrderRepository $orderRepository,
        WalletRepository $walletRepository,
        UserRepository $userRepository,
        ClientRepository $clientRepository,
        BankingRepository $bankingRepository
    ) {
        $this->request = $request;
        $this->orderRepository = $orderRepository;
        $this->walletRepository = $walletRepository;
        $this->userRepository = $userRepository;
        $this->clientRepository = $clientRepository;
        $this->bankingRepository = $bankingRepository;
    }

    public function orders(): View
    {
        $from = null;
        $to = null;
        $filters = [];

        if (! is_null($this->request->query('from'))) {
            $from = Carbon::parse($this->request->query('from'))->startOfDay();
        }

        if (! is_null($this->request->query('to'))) {
            $to = Carbon::parse($this->request->query('to'))->endOfDay();
        }

        if (! is_null($from)) {
            $filters = ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')];
        }

        // Add a filter to exclude IN_PROCESS orders
        $filters['exclude_in_process'] = true;

        $orders = $this->orderRepository->getForUser(
            $this->request->user()->getUuid(),
            $filters,
            $this->request->query('page')
        );

        foreach ($orders as $order) {
            if ($order->advert) {
                if ($order->advert->getParsedPickupTo()->isPast() &&
                    $order->getStatus() !== Order::STATUS_GEANNULEERD) {

                    // Update order status
                    $order->status = Order::STATUS_VERLOPEN;
                    $order->save();

                    // Also update wallet line if needed
                    $walletLine = $this->walletRepository->findWalletLineByOrderUuid($order->getUuid());
                    if ($walletLine && $walletLine->getState() == WalletLine::PROCESSING) {
                        $walletLine->update(['state' => WalletLine::AVAILABLE]);

                        // Also update wallet totals
                        $this->walletRepository->countAvailableWalletLinesForUser(
                            $order->getUserUuid()
                        );
                    }
                }
            }
        }

        return view('dashboard.orders.index', [
            'orders' => $orders,
            'from' => $from,
            'to' => $to,
            'min' => $orders->last()?->getCreatedAt()?->format('Y-m-d'),
        ]);
    }

    public function showOrder(string $uuid): View
    {
        $order = $this->orderRepository->find($uuid);

        if ($order->getUserUuid() !== $this->request->user()->getUuid()) {
            throw new ModelNotFoundException;
        }

        // Don't allow viewing IN_PROCESS orders
        if ($order->payment_state == Order::IN_PROCESS) {
            return redirect()->route('dashboard.orders.home')
                ->with('message', 'Deze bestelling is nog in behandeling.');
        }

        /** @var Advert $advert */
        $advert = $order->advert;

        return view('dashboard.orders.show', [
            'order' => $order,
            'cancel' => ! $advert->getParsedOrderTo()->isPast(),
            'status' => $order->getStatus() !== Order::STATUS_GEANNULEERD,
        ]);
    }

    public function cancel(string $uuid)
    {
        $order = $this->orderRepository->find($uuid);

        if ($order->getUserUuid() !== $this->request->user()->getUuid()) {
            throw new ModelNotFoundException;
        }

        /** @var Advert $advert */
        $advert = $order->advert;

        // Check if this would exceed the cancellation limit
        $ordersCanceledCount = $this->orderRepository->getCanceledOrdersByUser(
            $order->getUserUuid(),
            'month',
            Order::CANCELLED_BY_COOK
        )->count();

        // Set warning flag if at cancellation limit
        $atCancellationLimit = $ordersCanceledCount >= $this->cancellationLimit;

        return view('dashboard.orders.cancel', [
            'orderId' => $order->uuid,
            'cancel' => ! $advert->getParsedOrderTo()->isPast(),
            'clientName' => $order->client->getName(),
            'hideMenu' => true,
            'atCancellationLimit' => $atCancellationLimit,
        ]);
    }

    public function cancelOrder(string $uuid): RedirectResponse
    {
        $order = $this->orderRepository->find($uuid);

        if ($order->getUserUuid() !== $this->request->user()->getUuid()) {
            throw new ModelNotFoundException;
        }

        try {
            // Maak connectie met de mollie api
            $mollie = new MollieApiClient;
            $mollie->setApiKey(env('MOLLIE_API_KEY'));

            $paymentId = $order->payment_id;

            // Check if payment_id exists
            if (empty($paymentId)) {
                // If no payment_id, just cancel the order without refund
                $order->status = Order::STATUS_GEANNULEERD;
                $order->cancelled_by = Order::CANCELLED_BY_COOK;
                $order->save();

                Mail::to(
                    $order->client->getEmail(),
                    $order->client->getName()
                )->send(new OrderCancelCustomer(
                    $this->request->user(),
                    $order,
                    $order->dish,
                    $this->request->input('cancel_text') ?? 'Geannuleerd',
                    $order->client
                ));

                // Send review email after cancellation
                Mail::to(
                    $order->client->getEmail(),
                    $order->client->getName()
                )->send(new CancellationReviewMail(
                    $order->client,
                    $order->getUuid(),
                    $order->user->cook
                ));

                // Update the review_send timestamp
                $order->review_send = now();
                $order->save();

                return redirect()->route('dashboard.orders.home')->with('message', 'Bestelling is geannuleerd.');
            }

            // Haal de betaling op met de payment id
            $payment = $mollie->payments->get($paymentId);

            // Bereken het totale bedrag inclusief transactiekosten
            $totalAmount = $payment->amount->value;

            // Bepaal het aantal geannuleerde bestellingen
            $ordersCanceledCount = $this->orderRepository->getCanceledOrdersByUser(
                $order->getUserUuid(),
                'month',
                Order::CANCELLED_BY_COOK
            )->count();

            $cancelationFee = 0.60;

            // Stuur e-mail wanneer aantal één onder het limiet is
            if ($ordersCanceledCount === ($this->cancellationLimit - 1)) {
                /** @var User $user */
                $user = $order->user;
                Mail::to(
                    $user->getEmail(),
                    $user->getUsername()
                )->send(new AlertNextCancellationCost($user, $cancelationFee, 'bestelling'));
            }

            // Voeg transactiekosten toe als de limiet is bereikt of overschreden
            if ($ordersCanceledCount >= $this->cancellationLimit) {
                // Direct de transactiekosten toepassen
                $this->walletRepository->applyTransactionCost(
                    $order->getUserUuid(),
                    $order->getUuid(),
                    $cancelationFee
                );
            }

            try {
                // Voer de refund uit met het totale bedrag
                $refund = $mollie->payments->refund($payment, [
                    'amount' => [
                        'currency' => $payment->amount->currency,
                        'value' => $totalAmount,
                    ],
                ]);

                $order->cancelled_by = Order::CANCELLED_BY_COOK;
                $order->save();

                if ($refund->status == 'pending' || $refund->status == 'paid') {
                    Mail::to(
                        $order->client->getEmail(),
                        $order->client->getName()
                    )->send(new OrderCancelCustomer(
                        $this->request->user(),
                        $order,
                        $order->dish,
                        $this->request->input('cancel_text') ?? 'Geannuleerd',
                        $order->client
                    ));

                    $order->status = Order::STATUS_GEANNULEERD;
                    $order->save();

                    // Send review email after successful refund and cancellation
                    Mail::to(
                        $order->client->getEmail(),
                        $order->client->getName()
                    )->send(new CancellationReviewMail(
                        $order->client,
                        $order->getUuid(),
                        $order->user->cook
                    ));

                    // Update the review_send timestamp
                    $order->review_send = now();
                    $order->save();

                    if ($order->walletLine) {
                        $this->walletRepository->updateWalletLine(
                            WalletLine::REFUNDING,
                            $order->walletLine->getUuid()
                        );
                    }
                }
            } catch (\Mollie\Api\Exceptions\ApiException $e) {
                Log::error('Mollie Refund Error: '.$e->getMessage());

                // Still cancel the order even if Mollie refund fails
                $order->status = Order::STATUS_GEANNULEERD;
                $order->cancelled_by = Order::CANCELLED_BY_COOK;
                $order->save();

                Mail::to(
                    $order->client->getEmail(),
                    $order->client->getName()
                )->send(new OrderCancelCustomer(
                    $this->request->user(),
                    $order,
                    $order->dish,
                    $this->request->input('cancel_text') ?? 'Geannuleerd',
                    $order->client
                ));

                // Send review email even if refund fails
                Mail::to(
                    $order->client->getEmail(),
                    $order->client->getName()
                )->send(new CancellationReviewMail(
                    $order->client,
                    $order->getUuid(),
                    $order->user->cook
                ));

                // Update the review_send timestamp
                $order->review_send = now();
                $order->save();
            }

        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            Log::error('Mollie API Error: '.$e->getMessage());

            // Still cancel the order even if Mollie API fails
            $order->status = Order::STATUS_GEANNULEERD;
            $order->cancelled_by = Order::CANCELLED_BY_COOK;
            $order->save();

            Mail::to(
                $order->client->getEmail(),
                $order->client->getName()
            )->send(new OrderCancelCustomer(
                $this->request->user(),
                $order,
                $order->dish,
                $this->request->input('cancel_text') ?? 'Geannuleerd',
                $order->client
            ));

            // Send review email even if Mollie API fails
            Mail::to(
                $order->client->getEmail(),
                $order->client->getName()
            )->send(new CancellationReviewMail(
                $order->client,
                $order->getUuid(),
                $order->user->cook
            ));

            // Update the review_send timestamp
            $order->review_send = now();
            $order->save();

            return redirect()->route('dashboard.orders.home')
                ->with('message', 'Bestelling is geannuleerd, maar er was een probleem met de terugbetaling. De klant wordt hierover geïnformeerd.');
        }

        return redirect()->route('dashboard.orders.home')->with('message', 'Bestelling is geannuleerd.');
    }

    public function cancelCustomerOrder(): View|RedirectResponse
    {
        $encryptedKey = $this->request->query('key');
        $exploded = explode('/', decrypt($encryptedKey));

        $order = $this->orderRepository->find($this->request->query('uuid'));

        // First check if order exists
        if (is_null($order)) {
            return redirect()->route('home')->with('error', 'Invalid order.');
        }

        // Check if order is already cancelled - redirect to already-cancelled page
        if ($order->status === Order::STATUS_GEANNULEERD) {
            return view('customer.order.already-cancelled', [
                'hideMenu' => true,
            ]);
        }

        // Validate order ownership and timing
        if (
            $exploded[0] === $order->getUuid() &&
            $exploded[1] === $order->getClientUuid() &&
            $exploded[2] == $order->getCreatedAt()->unix()
        ) {
            /** @var Advert $advert */
            $advert = $order->advert;

            $cancellationWarning = false;

            // Check email-based cancellations count
            $emailCancellationsCount = $this->orderRepository->getCancellationsByEmailToday($order->client->getEmail());

            // NIEUW: Check IBAN-based cancellations
            $ibanCount = 0;
            try {
                // Alleen uitvoeren als betaling bestaat
                if (! empty($order->payment_id)) {
                    $mollie = new MollieApiClient;
                    $mollie->setApiKey(env('MOLLIE_API_KEY'));
                    $payment = $mollie->payments->get($order->payment_id);

                    if (isset($payment->details->consumerAccount)) {
                        $customerIban = $payment->details->consumerAccount;

                        // Zoek gebruikers met dezelfde IBAN
                        $usersWithSameIban = $this->bankingRepository->findUsersByBanking($customerIban);

                        // Tel annuleringen voor alle gebruikers met deze IBAN
                        foreach ($usersWithSameIban as $banking) {
                            $userUuid = $banking->getUserUuid();
                            $userCancelledOrders = $this->orderRepository->getCanceledOrdersByUser(
                                $userUuid,
                                'day',
                                Order::CANCELLED_BY_CLIENT
                            );
                            $ibanCount += $userCancelledOrders->count();
                        }

                        // Debug logging
                        \Log::info("IBAN cancellation check on warning page: IBAN=$customerIban, Count=$ibanCount");
                    }
                }
            } catch (\Exception $e) {
                // Logging bij fout maar doorgaan met pagina tonen
                \Log::error('Error in IBAN check for warning: '.$e->getMessage());
            }

            // Show warning if email has reached the limit OR IBAN has reached the limit
            if ($emailCancellationsCount >= $this->dailyCancellationLimit || $ibanCount >= $this->ibanThreshold) {
                $cancellationWarning = true;
            }

            return view('customer.order.cancel', [
                'cancellable' => ! $advert->getParsedOrderTo()->isPast(),
                'order' => $order,
                'key' => encrypt($order->client->getCreatedAt()->unix()),
                'hideMenu' => true,
                'cancellationWarning' => $cancellationWarning,
            ]);
        }

        throw new ModelNotFoundException;
    }

    public function submitCancelCustomerOrder(
        string $uuid,
        string $key
    ): View|RedirectResponse {
        $validated = $this->request->validate([
            'cancel_text' => 'required|string|max:1000',
        ]);

        $order = $this->orderRepository->find($uuid);

        // Check if order exists and validate key
        try {
            $decryptedKey = decrypt($key);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return redirect()->route('home')->with('error', 'Invalid order.');
        }

        if (is_null($order) || $order->client->getCreatedAt()->unix() !== $decryptedKey) {
            return redirect()->route('home')->with('error', 'Invalid order.');
        }

        // Check if already cancelled - show already-cancelled page
        if ($order->status === Order::STATUS_GEANNULEERD) {
            return view('customer.order.already-cancelled', [
                'hideMenu' => true,
            ]);
        }

        // Get admin email from .env
        $adminEmail = config('mail.admin.address');

        $paymentId = $order->payment_id;

        // Check if payment_id exists - handle cancellation without refund
        if (empty($paymentId)) {
            $order->status = Order::STATUS_GEANNULEERD;
            $order->cancelled_by = Order::CANCELLED_BY_CLIENT;
            $order->save();

            /** @var User $user */
            $user = $order->user;
            /** @var Cook $cook */
            $cook = $user->cook;

            // Als de kok zijn account zijn email geverifieerd is en de mail voor annuleren is aangevinkt dan wordt de mail verstuurd
            if ($cook->getMailCancel() && $user->getEmailVerifiedAt()) {
                try {
                    Mail::to(
                        $user->getEmail(),
                        $user->getUsername()
                    )->send(new OrderCookCancelled(
                        $user,
                        $order,
                        $validated['cancel_text'] ?? 'Geannuleerd',
                    ));
                    Log::info('Cancellation email sent to cook: '.$user->getEmail());
                } catch (\Exception $e) {
                    Log::error('Failed to send cook cancellation email: '.$e->getMessage());
                }
            }

            try {
                Mail::to(
                    $order->client->getEmail(),
                    $order->client->getName()
                )->send(new OrderCustomerCancelled(
                    $user,
                    $order,
                    $validated['cancel_text'],
                ));
                Log::info('Cancellation confirmation email sent to customer: '.$order->client->getEmail());
            } catch (\Exception $e) {
                Log::error('Failed to send customer cancellation email: '.$e->getMessage());
            }

            return view('customer.order.cancel-complete', [
                'hideMenu' => true,
            ]);
        }

        try {
            // Maak connectie met de mollie api
            $mollie = new MollieApiClient;
            $mollie->setApiKey(env('MOLLIE_API_KEY'));

            // Haal de betaling op met de payment id
            $payment = $mollie->payments->get($paymentId);

            // Initialize flags for suspicious activity
            $emailCount = 0;
            $ibanCount = 0;
            $transactionCost = 0;

            // Check email-based cancellations count
            $emailCancellationsCount = $this->orderRepository->getCancellationsByEmailToday($order->client->getEmail());

            // NIEUW: Haal IBAN op uit de betaling en check eerdere annuleringen
            if (isset($payment->details->consumerAccount)) {
                $customerIban = $payment->details->consumerAccount;

                // Zoek gebruikers met dezelfde IBAN
                $usersWithSameIban = $this->bankingRepository->findUsersByBanking($customerIban);

                // Tel annuleringen voor alle gebruikers met deze IBAN
                foreach ($usersWithSameIban as $banking) {
                    $userUuid = $banking->getUserUuid();
                    $userCancelledOrders = $this->orderRepository->getCanceledOrdersByUser(
                        $userUuid,
                        'day',
                        Order::CANCELLED_BY_CLIENT
                    );
                    $ibanCount += $userCancelledOrders->count();
                }

                // Debug logging
                \Log::info("IBAN cancellation check: IBAN=$customerIban, Count=$ibanCount");
            }

            foreach ($this->orderRepository->getCanceledOrdersByUser($order->user_uuid, 'day', Order::CANCELLED_BY_CLIENT) as $singleOrder) {
                if ($singleOrder->client->getEmail() === $order->client->getEmail()) {
                    $emailCount++;
                }
            }

            // Get total cancellations for today
            $totalCancellations = $this->orderRepository->getTotalCancellationsForToday();

            $client = $this->clientRepository->find($order->client_uuid);

            // Verzamel alle alert berichten
            $alertMessages = [];

            // Email-drempel check
            if ($emailCount >= $this->emailThreshold) {
                $alertMessages[] = 'Deze klant heeft vandaag al '.$this->emailThreshold.' of meer bestellingen geannuleerd op hetzelfde email adres.';
            }

            // NIEUW: IBAN-drempel check
            if ($ibanCount >= $this->ibanThreshold) {
                $alertMessages[] = 'Deze klant heeft vandaag al '.$this->ibanThreshold.' of meer bestellingen geannuleerd met dezelfde IBAN.';
            }

            // Totale annuleringen check
            if ($totalCancellations >= $this->totalThreshold) {
                $alertMessages[] = 'Er zijn vandaag in totaal '.$this->totalThreshold.' of meer bestellingen geannuleerd door alle gebruikers samen.';
            }

            // Stuur één e-mail met alle alerts
            if (! empty($alertMessages)) {
                $combinedMessage = implode("\n", $alertMessages);
                Mail::to(
                    config('mail.admin.address'),
                    config('mail.admin.name')
                )->send(new AlertAdminUserForSuspiciousCancellations(
                    config('mail.admin.name'),
                    $client,
                    $combinedMessage
                ));

                Log::warning('Waarschuwingsmail verzonden met alerts: '.$combinedMessage);
            }

            $clientOrders = $this->orderRepository->getClientsAdvertOrdersByClientId($order->client_uuid, $order->advert_uuid);

            // Als er meer dan DAILY_CANCELLATION_LIMIT annuleringen zijn per email OF per IBAN, voeg transactiekosten toe
            if ($emailCancellationsCount >= $this->dailyCancellationLimit || $ibanCount >= $this->ibanThreshold) {
                $transactionCost = 0.60;
            }

            // Bereken het totale bedrag inclusief transactiekosten
            $totalAmount = $payment->amount->value;
            $totalAmountWithFees = (float) $totalAmount - $transactionCost;
            $totalAmountWithFees = number_format($totalAmountWithFees, 2, '.', '');

            // Extra debugging info
            Log::info('Refund details - IBAN: '.($customerIban ?? 'none').', Email count: '.$emailCancellationsCount.
                    ', IBAN count: '.$ibanCount.', Fee applied: '.($transactionCost > 0 ? 'YES' : 'NO'));

            // Voer de refund uit met het aangepaste bedrag
            $refund = $mollie->payments->refund($payment, [
                'amount' => [
                    'currency' => $payment->amount->currency,
                    'value' => $totalAmountWithFees,
                ],
            ]);

            $order->cancelled_by = Order::CANCELLED_BY_CLIENT;
            $order->save();

            if ($refund->status == 'pending' || $refund->status == 'paid') {
                /** @var User $user */
                $user = $order->user;
                /** @var Cook $cook */
                $cook = $user->cook;

                // Als de kok zijn account zijn email geverifieerd is en de mail voor annuleren is aangevinkt dan wordt de mail verstuurd
                if ($cook->getMailCancel() && $user->getEmailVerifiedAt()) {
                    try {
                        Mail::to(
                            $user->getEmail(),
                            $user->getUsername()
                        )->send(new OrderCookCancelled(
                            $user,
                            $order,
                            $validated['cancel_text'] ?? 'Geannuleerd',
                        ));
                        Log::info('Cancellation email sent to cook: '.$user->getEmail());
                    } catch (\Exception $e) {
                        Log::error('Failed to send cook cancellation email: '.$e->getMessage());
                    }
                }

                try {
                    Mail::to(
                        $order->client->getEmail(),
                        $order->client->getName()
                    )->send(new OrderCustomerCancelled(
                        $user,
                        $order,
                        $validated['cancel_text'],
                    ));
                    Log::info('Cancellation confirmation email sent to customer: '.$order->client->getEmail());
                } catch (\Exception $e) {
                    Log::error('Failed to send customer cancellation email: '.$e->getMessage());
                }

                // Hier onder wordt de status van de bestelling geannuleerd.
                $order->status = Order::STATUS_GEANNULEERD;
                $order->save();

                if ($order->walletLine) {
                    $this->walletRepository->updateWalletLine(
                        WalletLine::REFUNDING,
                        $order->walletLine->getUuid()
                    );
                }
            }

            // Redirect naar de success pagina
            Log::info('Order successfully cancelled by customer: Order #'.$order->getUuid());

            return view('customer.order.cancel-complete', [
                'hideMenu' => true,
            ]);

        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            Log::error('Mollie API Error in customer cancellation: '.$e->getMessage());

            return redirect()->route('home')->with('error', 'Er is iets fout gegaan bij het annuleren van de bestelling. Probeer het later opnieuw.');
        }
    }

    /**
     * Controleert of een waarschuwing al is verzonden op dezelfde dag
     *
     * @param  string  $type  Het type waarschuwing (email, iban, total)
     * @param  string|null  $identifier  Optionele identifier (email adres of IBAN)
     */
    private function isAlertAlreadySent(string $type, ?string $identifier = null): bool
    {
        $today = Carbon::now()->format('Y-m-d');
        $cacheKey = "cancellation_alert_{$type}_{$today}";

        if ($identifier) {
            $cacheKey .= '_'.md5($identifier);
        }

        return \Cache::has($cacheKey);
    }

    /**
     * Markeert een waarschuwing als verzonden voor de rest van de dag
     *
     * @param  string  $type  Het type waarschuwing (email, iban, total)
     * @param  string|null  $identifier  Optionele identifier (email adres of IBAN)
     */
    private function markAlertAsSent(string $type, ?string $identifier = null): void
    {
        $today = Carbon::now()->format('Y-m-d');
        $cacheKey = "cancellation_alert_{$type}_{$today}";

        if ($identifier) {
            $cacheKey .= '_'.md5($identifier);
        }

        // Bewaar 24 uur in de cache
        \Cache::put($cacheKey, true, 60 * 60 * 24);
    }

    public function downloadOrderDocument(string $uuid)
    {
        $order = $this->orderRepository->find($uuid);

        if ($order->getUserUuid() !== $this->request->user()->getUuid()) {
            throw new ModelNotFoundException;
        }

        $user = $this->request->user();
        $isBusinessCook = $user->type_thuiskok === 'Zakelijke Thuiskok';

        // Use factuur.blade.php for business cooks and purchase-receipt.blade.php for private cooks
        $view = $isBusinessCook ? 'pdf.factuur' : 'pdf.purchase-receipt';
        $filename = $isBusinessCook ? 'factuur-' : 'aankoopbewijs-';
        $filename .= $order->getParsedOrderUuid().'.pdf';

        // Add data for proper price calculations
        $portionPrice = $order->advert->getPortionPrice();
        $portionAmount = $order->getPortionAmount();

        // Calculate the price excluding VAT (base price)
        $priceExVat = round($portionPrice / 1.09, 2);

        // Calculate total without VAT
        $totalExVat = $priceExVat * $portionAmount;

        // Calculate VAT amount
        $vatAmount = round($totalExVat * 0.09, 2);

        // Calculate total with VAT
        $totalWithVat = $totalExVat + $vatAmount;

        // Render de view naar HTML
        $html = view($view, [
            'order' => $order,
            'user' => $user,
            'priceExVat' => $priceExVat,
            'totalExVat' => $totalExVat,
            'vatAmount' => $vatAmount,
            'totalWithVat' => $totalWithVat,
        ])->render();

        // Maak PDF met dompdf
        $options = new Options;
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);

        $pdf = new Dompdf($options);
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    public function sendOrderDocument(string $uuid)
    {
        $order = $this->orderRepository->find($uuid);

        if ($order->getUserUuid() !== $this->request->user()->getUuid()) {
            throw new ModelNotFoundException;
        }

        $user = $this->request->user();
        $isBusinessCook = $user->type_thuiskok === 'Zakelijke Thuiskok';

        // Use factuur.blade.php for business cooks and purchase-receipt.blade.php for private cooks
        $view = $isBusinessCook ? 'pdf.factuur' : 'pdf.purchase-receipt';
        $documentType = $isBusinessCook ? 'Factuur' : 'Aankoopbewijs';

        // Add data for proper price calculations
        $portionPrice = $order->advert->getPortionPrice();
        $portionAmount = $order->getPortionAmount();

        // Calculate the price excluding VAT (base price)
        $priceExVat = round($portionPrice / 1.09, 2);

        // Calculate total without VAT
        $totalExVat = $priceExVat * $portionAmount;

        // Calculate VAT amount
        $vatAmount = round($totalExVat * 0.09, 2);

        // Calculate total with VAT
        $totalWithVat = $totalExVat + $vatAmount;

        // Render de view naar HTML
        $html = view($view, [
            'order' => $order,
            'user' => $user,
            'priceExVat' => $priceExVat,
            'totalExVat' => $totalExVat,
            'vatAmount' => $vatAmount,
            'totalWithVat' => $totalWithVat,
        ])->render();

        // Maak PDF met dompdf
        $options = new Options;
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);

        $pdf = new Dompdf($options);
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        $filename = strtolower($documentType).'-'.$order->getParsedOrderUuid().'.pdf';
        $pdfContent = $pdf->output();

        Mail::to($order->client->getEmail())
            ->send(new OrderDocumentMail(
                $order->client->getName(),
                $order->getParsedOrderUuid(),
                $order->dish->getTitle(),
                $documentType,
                $pdfContent,
                $filename
            ));

        return redirect()->back()->with('message', $documentType.' is verzonden naar '.$order->client->getEmail());
    }

    private function checkDac7ThresholdsForOrder(Order $order)
    {
        // Controleer of de order een geldige user heeft
        if ($order && $order->user) {
            // Roep de DAC7 service aan om thresholds te controleren
            app(Dac7Service::class)->checkUserDac7Thresholds($order->user);
        }
    }
}
