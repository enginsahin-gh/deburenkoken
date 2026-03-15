<?php

namespace App\Http\Controllers;

use App\Dtos\BankingDto;
use App\Dtos\PaymentDto;
use App\Dtos\WalletLineDto;
use App\Mail\AdminIbanChangeNotification;
use App\Mail\IbanChangeMail;
use App\Mail\IbanPayoutNotification;
use App\Mail\IbanVerificationMail;
use App\Mail\PaymentMail;
use App\Models\Banking;
use App\Models\IbanChangeHistory;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletLine;
use App\Repositories\AdvertRepository;
use App\Repositories\BankingRepository;
use App\Repositories\CookRepository;
use App\Repositories\OrderRepository;
use App\Repositories\WalletRepository;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\MollieApiClient;

class WalletController extends Controller
{
    private Request $request;

    private BankingRepository $bankingRepository;

    private WalletRepository $walletRepository;

    private CookRepository $cookRepository;

    private AdvertRepository $advertRepository;

    private OrderRepository $orderRepository;

    private WalletLine $walletLine;

    public function __construct(
        Request $request,
        BankingRepository $bankingRepository,
        WalletRepository $walletRepository,
        CookRepository $cookRepository,
        AdvertRepository $advertRepository,
        OrderRepository $orderRepository,
        WalletLine $walletLine
    ) {
        $this->request = $request;
        $this->bankingRepository = $bankingRepository;
        $this->walletRepository = $walletRepository;
        $this->cookRepository = $cookRepository;
        $this->advertRepository = $advertRepository;
        $this->orderRepository = $orderRepository;
        $this->walletLine = $walletLine;
    }

    public function overview(): View
    {
        $user = $this->request->user();
        $userUuid = $user->getUuid();

        // Refreshen van wallet gegevens voor display - met extra forceren van correcte berekening
        DB::beginTransaction();
        try {
            // Forceer update van orderstatussen eerst
            $this->processOrderStatusUpdates($userUuid);

            $wallet = Wallet::where('user_uuid', $userUuid)->lockForUpdate()->first();

            if ($wallet) {
                // Explicit nieuw berekenen van de beschikbare en in behandeling saldi

                // Beschikbare bedragen (inclusief transactiekosten die negatief zijn)
                $availableAmount = WalletLine::whereHas('wallet', function ($query) use ($userUuid) {
                    $query->where('user_uuid', $userUuid);
                })
                    ->whereIn('state', [
                        WalletLine::AVAILABLE,
                        WalletLine::COMPLETED,
                    ])
                    ->sum('amount');

                // Transactiekosten (zouden al negatieve waarden moeten zijn)
                $transactionCosts = WalletLine::whereHas('wallet', function ($query) use ($userUuid) {
                    $query->where('user_uuid', $userUuid);
                })
                    ->where('state', WalletLine::CANCELLATION_COST)
                    ->sum('amount');

                // Bedragen in verwerking
                $processingAmount = WalletLine::whereHas('wallet', function ($query) use ($userUuid) {
                    $query->where('user_uuid', $userUuid);
                })
                    ->whereIn('state', [
                        WalletLine::PROCESSING,
                        WalletLine::ON_HOLD,
                        WalletLine::RESERVED,
                    ])
                    ->sum('amount');

                // Totaal beschikbaar is reguliere beschikbare plus transactiekosten (die negatief zijn)
                $totalAvailable = $availableAmount + $transactionCosts;

                // Update de wallet balansen
                $wallet->update([
                    'total_available' => $totalAvailable,
                    'total_processing' => $processingAmount,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to refresh wallet balances: '.$e->getMessage());
        }

        // Get fresh data with forced recalculation
        $wallet = $this->walletRepository->findWalletForUser($userUuid);
        if (! $wallet) {
            $wallet = $this->walletRepository->createWalletForUser($user);
        }

        $this->processOrderStatusUpdates($userUuid);

        $wallet->refresh();

        // ---- Voor testen zonder beperking op meerdere malen per dag uitbetalen
        // $recentPayout = false;
        //
        // $transactions = $this->prepareTransactionsData($userUuid);
        //
        // $checkLastPayment = false;
        //
        // $payoutOption = $wallet->getTotalAvailable() > 0 && !$recentPayout;
        // -------

        // ------------- Productie: Maximaal 1 keer per dag uitbetalen
        $recentPayout = Payment::where('user_uuid', $userUuid)
            ->where('created_at', '>=', today())
            ->exists();

        $transactions = $this->prepareTransactionsData($userUuid);

        $checkLastPayment = $this->bankingRepository->checkLastPaymentDate($userUuid);

        $payoutOption = $wallet->getTotalAvailable() > 0 && ! $recentPayout;
        // ------------

        $payments = Payment::with(['banking'])
            ->where('user_uuid', $userUuid)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($payment) {
                // Bereken 5% bijdrage
                $feeAmount = $payment->amount * 0.05;
                // Bereken uit te betalen bedrag (95% van totaal)
                $payoutAmount = $payment->amount - $feeAmount;

                return [
                    'uuid' => $payment->uuid,
                    'created_at' => $payment->created_at,
                    'amount' => $payment->amount,
                    'fee_amount' => $feeAmount,
                    'payout_amount' => $payoutAmount,
                    'payment_date' => $payment->payment_date,
                    'banking' => $payment->banking,
                ];
            });

        $payoutSuccess = session('payout_success', false);

        return view('dashboard.wallet.index', [
            'reserved' => number_format($wallet->getTotalProcessing(), 2),
            'available' => number_format($wallet->getTotalAvailable(), 2),
            'wallet' => $wallet,
            'iban' => $this->bankingRepository->findByUserUuid($userUuid),
            'payments' => $payments,
            'paidedOut' => $checkLastPayment,
            'payoutOption' => $payoutOption,
            'processingTransactions' => $transactions['processingTransactions'],
            'availableTransactions' => $transactions['availableTransactions'],
            'payoutSuccess' => $payoutSuccess,
        ]);
    }

    private function recalculateAllWalletBalances(string $userUuid): void
    {
        DB::beginTransaction();

        try {
            $wallet = Wallet::where('user_uuid', $userUuid)->lockForUpdate()->first();

            if (! $wallet) {
                DB::rollBack();

                return;
            }

            // Zorg ervoor dat alle order statussen up-to-date zijn
            $this->processOrderStatusUpdates($userUuid);

            // Bereken de juiste saldo's direct uit de database
            $processingAmount = WalletLine::whereHas('wallet', function ($query) use ($userUuid) {
                $query->where('user_uuid', $userUuid);
            })
                ->whereIn('state', [
                    WalletLine::PROCESSING,
                    WalletLine::ON_HOLD,
                    WalletLine::RESERVED,
                ])
                ->sum('amount');

            $availableAmount = WalletLine::whereHas('wallet', function ($query) use ($userUuid) {
                $query->where('user_uuid', $userUuid);
            })
                ->whereIn('state', [
                    WalletLine::AVAILABLE,
                    WalletLine::COMPLETED,
                ])
                ->sum('amount');

            // Update de wallet met de exacte berekende waardes
            $wallet->update([
                'total_processing' => $processingAmount,
                'total_available' => $availableAmount,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * Process all orders to ensure wallet line states are correct
     */
    private function processOrderStatusUpdates(string $userUuid): void
    {
        // Get all adverts for this user
        $activeAdverts = $this->advertRepository->getUsersAdverts(
            $userUuid,
            Carbon::now()->subYear()->format('Y-m-d'),
            Carbon::now()->endOfMonth()->addYear()->format('Y-m-d')
        );

        foreach ($activeAdverts as $advert) {
            $pickupTo = $advert->getParsedPickupTo();

            foreach ($advert->order()->with('advert.dish')->get() as $order) {
                // Only process orders with successful payment
                if ($order->payment_state == Order::SUCCEED &&
                    $order->status !== Order::STATUS_GEANNULEERD) {

                    $walletLine = $this->walletRepository->findWalletLineByOrderUuid($order->getUuid());

                    if (! $walletLine) {
                        continue;
                    }

                    // Alleen bepaalde staten niet aanraken
                    $protectedStates = [
                        WalletLine::CANCELLATION_COST,
                        WalletLine::CANCELLATION_COST_PAID,
                        WalletLine::CANCELLATION_COST_PAID_OUT,
                        WalletLine::PAYOUT_INITIATED,
                        WalletLine::PAID_OUT,
                        WalletLine::REFUNDING,
                        WalletLine::REFUNDED,
                    ];

                    if (in_array($walletLine->getState(), $protectedStates)) {
                        continue;
                    }

                    // BELANGRIJK: Als pick-up tijd voorbij is, zet naar AVAILABLE
                    if ($pickupTo->isPast()) {
                        if ($walletLine->getState() !== WalletLine::AVAILABLE) {
                            $walletLine->update(['state' => WalletLine::AVAILABLE]);

                            // Update order status if needed
                            if ($order->status !== Order::STATUS_VERLOPEN) {
                                $order->update(['status' => Order::STATUS_VERLOPEN]);
                            }
                        }
                    } else {
                        // In PROCESSING houden tot pick-up tijd voorbij is
                        if ($walletLine->getState() !== WalletLine::PROCESSING) {
                            $walletLine->update(['state' => WalletLine::PROCESSING]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Prepare transaction data for the UI
     */
    private function prepareTransactionsData(string $userUuid): array
    {
        $wallet = $this->walletRepository->findWalletForUser($userUuid);

        if (! $wallet) {
            return [
                'processingTransactions' => collect(),
                'availableTransactions' => collect(),
            ];
        }

        $walletLines = $wallet->walletLines()
            ->with('order.advert.dish')
            ->orderBy('created_at', 'desc')
            ->get();

        $processingStates = [
            WalletLine::PROCESSING,
            WalletLine::ON_HOLD,
            WalletLine::RESERVED,
        ];

        $availableStates = [
            WalletLine::AVAILABLE,
            WalletLine::COMPLETED,
        ];

        return [
            'processingTransactions' => $walletLines->filter(function ($line) use ($processingStates) {
                return in_array($line->getState(), $processingStates);
            }),
            'availableTransactions' => $walletLines->filter(function ($line) use ($availableStates) {
                return in_array($line->getState(), $availableStates);
            }),
        ];
    }

    public function calculateTotalAvailable(string $userUuid): float
    {
        // Calculate available balance from regular transactions
        $availableAmount = WalletLine::whereHas('wallet', function ($query) use ($userUuid) {
            $query->where('user_uuid', $userUuid);
        })
            ->whereIn('state', [
                WalletLine::AVAILABLE,
                WalletLine::COMPLETED,
            ])
            ->sum('amount');

        // Calculate total cancellation costs (these are already negative values)
        $cancellationCosts = WalletLine::whereHas('wallet', function ($query) use ($userUuid) {
            $query->where('user_uuid', $userUuid);
        })
            ->where('state', WalletLine::CANCELLATION_COST)
            ->sum('amount');

        // Return combined total (since cancellation costs are negative, they will reduce the balance)
        return $availableAmount + $cancellationCosts;
    }

    public function calculateTotalReserved(string $userUuid): float
    {
        return WalletLine::whereHas('wallet', function ($query) use ($userUuid) {
            $query->where('user_uuid', $userUuid);
        })
            ->whereIn('state', [
                WalletLine::PROCESSING,
                WalletLine::ON_HOLD,
                WalletLine::RESERVED,
            ])
            ->sum('amount');
    }

    public function countReservedWalletLinesForUser(string $userId): float
    {
        $walletLinesAmount = WalletLine::whereHas('wallet', function ($query) use ($userId) {
            $query->where('user_uuid', $userId);
        })
            ->where(function ($query) {
                $query->where('state', WalletLine::ON_HOLD)
                    ->orWhere('state', WalletLine::PROCESSING);
            })
            ->sum('amount');

        return $walletLinesAmount;
    }

    public function iban(): View
    {
        $verification = $this->request->session()->get('ibanVerification');
        $verify = $this->request->session()->get('ibanVerify');

        $this->request->session()->forget('ibanVerification');
        $this->request->session()->forget('ibanVerify');

        return view('dashboard.wallet.iban', [
            'iban' => $this->bankingRepository->findByUserUuid($this->request->user()->getUuid()),
            'cook' => $this->cookRepository->findByUserUuid($this->request->user()->getUuid()),
            'verification' => $verification,
            'verify' => $verify,
        ]);
    }

    public function addIban(): RedirectResponse
    {
        $validated = $this->request->validate([
            'type' => ['string', 'required'],
        ]);

        /** @var User $user */
        $user = $this->request->user();

        // Find user's IBAN
        $iban = $this->bankingRepository->findByUserUuid($user->getUuid());

        // Only check for daily limit if IBAN exists, is validated, and was successfully updated today
        // comment 402 tot 409 uit en  comment 423 tot 425 uit om het dag limiet even uit te zetten.
        if ($iban &&
            $iban->getIban() !== '' &&
            $iban->isValidated() &&
            $iban->getUpdatedAt()->format('Y-m-d') === today()->format('Y-m-d') &&
            ! is_null($iban->payment_id)) {
            return back()->with('message', 'Je kan maar 1 keer per dag je bankgegevens aanpassen');
        }
        $dailyVerificationLimit = 100; // Doe voor testen deze op 0

        $todayVerifications = Banking::whereDate('updated_at', today())
            ->whereNotNull('payment_id')
            ->where(function ($query) {
                $query->where('iban', '')
                    ->orWhere('validated', false);
            })
            ->count();

        // Check if this would be a new verification (not a change)
        $isNewVerification = is_null($iban) || empty($iban->getIban()) || ! $iban->isValidated();

        if ($isNewVerification && $todayVerifications >= $dailyVerificationLimit) {
            return back()->with('message', 'Helaas is het vandaag niet meer mogelijk om een account te verifieren, morgen is het weer mogelijk.');
        }

        // Create new IBAN record if none exists
        if (is_null($iban)) {
            $iban = $this->bankingRepository->create(
                new BankingDto(
                    $user->getUuid(),
                    '',
                    '',
                    false,
                    '',
                    ''
                )
            );
        }

        $transactionCost = '0.01';

        try {
            // Create Mollie payment for verification
            $mollie = new \Mollie\Api\MollieApiClient;
            $mollie->setApiKey(env('MOLLIE_API_KEY'));

            $payment = $mollie->payments->create([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => $transactionCost,
                ],
                'description' => 'Rekening Verificatie',
                'redirectUrl' => route('dashboard.wallet.iban.confirm'),
                'cancelUrl' => route('dashboard.wallet.iban.cancel'),
                'webhookUrl' => route('mollie.webhook.wallet'),
                'metadata' => [
                    'type' => 'iban_verification',
                    'user_uuid' => $user->getUuid(),
                    'banking_uuid' => $iban->getUuid(),
                ],
            ]);

            // Store payment ID for verification
            $iban->update([
                'payment_id' => $payment->id,
            ]);

            return redirect($payment->getCheckoutUrl());

        } catch (ApiException $e) {
            // Clear payment ID on failure to allow retry
            if ($iban) {
                $iban->update([
                    'payment_id' => null,
                ]);
            }

            return redirect()->route('dashboard.wallet.iban')
                ->with('message', 'IBAN verificatie is niet gelukt. Probeer het opnieuw.');
        }
    }

    public function downloadInvoice(string $paymentUuid)
    {
        /** @var User $user */
        $user = $this->request->user();

        // Find the payment and check if it belongs to this user
        $payment = $this->bankingRepository->findPaymentByUuid($paymentUuid);

        if (! $payment || $payment->getUserUuid() !== $user->getUuid() || ! $payment->getPaymentDate()) {
            return redirect()->route('dashboard.wallet.home')
                ->with('message', 'Deze factuur is niet beschikbaar.');
        }

        // Gegevens verzamelen voor de factuur
        $cook = $this->cookRepository->findByUserUuid($user->getUuid());

        // Let op de spelling: getAdress in plaats van getAddress
        $address = $cook ? $cook->getAdress() : '';

        // Controleer of de factuur al bestaat
        $invoiceFileName = 'invoice_'.$payment->getUuid().'.pdf';
        $invoicePath = storage_path('app/invoices/'.$invoiceFileName);

        // Maak het directory aan als het nog niet bestaat
        if (! Storage::exists('invoices')) {
            Storage::makeDirectory('invoices');
        }

        // Als de factuur nog niet bestaat, genereer deze dan
        if (! file_exists($invoicePath)) {
            // Render de view naar HTML
            $html = view('pdf.invoice', [
                'payment' => $payment,
                'user' => $user,
                'address' => $address,
            ])->render();

            // Maak PDF met dompdf
            $options = new Options;
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);

            $pdf = new Dompdf($options);
            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();

            // Sla de PDF op in de storage
            Storage::put('invoices/'.$invoiceFileName, $pdf->output());
        }

        // Return de factuur als download
        return response()->download($invoicePath, 'factuur_'.substr($payment->getUuid(), -6).'.pdf');
    }

    public function confirmIbanVerification(): RedirectResponse
    {
        $mollie = new \Mollie\Api\MollieApiClient;
        $mollie->setApiKey(env('MOLLIE_API_KEY'));

        $user = $this->request->user();
        $iban = $this->bankingRepository->findByUserUuid($user->getUuid());

        if (! $iban || ! $iban->getPaymentId()) {
            return redirect()->route('dashboard.wallet.iban')
                ->with('message', 'Er is iets fout gegaan. Probeer opnieuw je IBAN toe te voegen.');
        }

        try {
            $payment = $mollie->payments->get($iban->getPaymentId());

            if ($payment->isPaid()) {
                $oldIban = $iban->getIban();
                $newIban = $payment->details->consumerAccount;

                $changeCount = IbanChangeHistory::where('user_uuid', $user->getUuid())->count() + 1;

                IbanChangeHistory::create([
                    'user_uuid' => $user->getUuid(),
                    'old_iban' => $oldIban,
                    'new_iban' => $newIban,
                    'change_count' => $changeCount,
                ]);

                // Stuur mail naar admin als er te veel wijzigingen zijn
                if ($changeCount > 3) {
                    Mail::to(
                        config('mail.admin.address'),
                        config('mail.admin.name')
                    )->send(new AdminIbanChangeNotification($user, $changeCount));
                }

                $bankingDto = new BankingDto(
                    $user->getUuid(),
                    $payment->details->consumerName,
                    $payment->details->consumerAccount,
                    true,
                    $payment->details->consumerBic,
                    $payment->id
                );

                $isNewIban = empty($oldIban) && ! $iban->isValidated();

                if (is_null($iban)) {
                    $this->bankingRepository->create($bankingDto);
                } else {
                    $this->bankingRepository->update($bankingDto, $iban->getUuid());
                    $iban->update(['verified' => true]);
                }

                if ($isNewIban) {
                    Mail::to(
                        $user->getEmail(),
                        $user->getUsername()
                    )->send(new IbanVerificationMail($user));
                } elseif (! empty($oldIban) && $oldIban !== $newIban) {
                    Mail::to(
                        $user->getEmail(),
                        $user->getUsername()
                    )->send(new IbanChangeMail($user));
                }

                return redirect()->route('dashboard.wallet.iban');
            } else {
                $wasAlreadyVerified = $iban->isValidated() && $iban->isVerified() && ! empty($iban->getIban());

                $iban->update([
                    'payment_id' => null,
                    'verified' => $wasAlreadyVerified ? $iban->isVerified() : false,
                ]);

                return redirect()->route('dashboard.wallet.iban')
                    ->with('message', 'IBAN verificatie is niet gelukt. Probeer het opnieuw.');
            }
        } catch (ApiException $e) {
            $wasAlreadyVerified = $iban->isValidated() && $iban->isVerified() && ! empty($iban->getIban());

            $iban->update([
                'payment_id' => null,
                'verified' => $wasAlreadyVerified ? $iban->isVerified() : false,
            ]);

            return redirect()->route('dashboard.wallet.iban')
                ->with('message', 'Er is een fout opgetreden. Probeer het opnieuw.');
        }
    }

    public function cancelIbanVerification(): RedirectResponse
    {
        $user = $this->request->user();
        $iban = $this->bankingRepository->findByUserUuid($user->getUuid());

        if ($iban) {
            // Alleen verified op false zetten als er nog geen geldig IBAN was
            $wasAlreadyVerified = $iban->isValidated() && $iban->isVerified() && ! empty($iban->getIban());

            $iban->update([
                'payment_id' => null,
                'verified' => $wasAlreadyVerified ? $iban->isVerified() : false,
            ]);
        }

        return redirect()->route('dashboard.wallet.iban')->with('message', 'IBAN verificatie is geannuleerd');
    }

    /**
     * @throws FileNotFoundException
     */
    public function addIdCardToIban(): RedirectResponse
    {
        $this->request->validate([
            'id-card' => ['required', 'image'],
        ]);

        /** @var UploadedFile $image */
        $idCard = $this->request->file('id-card');
        $fileName = 'iban'.DIRECTORY_SEPARATOR.$this->request->user()->getUuid().DIRECTORY_SEPARATOR.time().'.'.$idCard->getClientOriginalExtension();

        $check = Storage::disk('local')->put($fileName, $idCard->get());

        if ($check) {
            $banking = $this->bankingRepository->findByUserUuid($this->request->user()->getUuid());

            $this->bankingRepository->verified(
                $banking->getUuid(),
                $fileName,
                false
            );

            return redirect()->route('dashboard.wallet.iban')->with('message', 'Identiteitsbewijs succesvol geupload');
        }

        return redirect()->route('dashboard.wallet.iban')->with('message', 'Er is iets fout gegaan');
    }

    public function payoutToCook(Request $request): RedirectResponse
    {
        $user = $this->request->user();
        $userUuid = $user->getUuid();

        DB::beginTransaction();

        try {
            $wallet = Wallet::where('user_uuid', $userUuid)->lockForUpdate()->first();

            if (! $wallet) {
                DB::rollBack();

                return redirect()->route('dashboard.wallet.home')->with('message', 'Wallet niet gevonden');
            }

            $banking = $this->bankingRepository->findByUserUuid($userUuid);

            if (is_null($banking)) {
                DB::rollBack();

                return redirect()->route('dashboard.wallet.home')->with('message', 'Voeg een bankrekening toe');
            }

            if (! $banking->isValidated() || ! $banking->isVerified()) {
                DB::rollBack();

                return redirect()->route('dashboard.wallet.home')
                    ->with('message', 'Je bankrekening is nog niet geverifieerd. Verifieer je bankrekening voordat je een uitbetaling aanvraagt.');
            }

            // Lock wallet lines tijdens de transactie om te voorkomen dat andere processen
            // (zoals automatische order status updates) wallet lines van PROCESSING naar AVAILABLE
            // veranderen tussen het ophalen en updaten. Het uitbetalingsbedrag wordt berekend uit
            // de gelocked records om te garanderen dat alleen beschikbaar saldo wordt uitbetaald.
            $availableWalletLines = WalletLine::whereHas('wallet', function ($query) use ($userUuid) {
                $query->where('user_uuid', $userUuid);
            })
                ->whereIn('state', [WalletLine::AVAILABLE, WalletLine::COMPLETED])
                ->lockForUpdate()
                ->get();

            // Bereken het totale beschikbare bedrag uit de gelocked wallet lines
            $totalAvailableFromLines = $availableWalletLines->sum('amount');

            // Haal ook de annuleringskosten op (deze zijn negatief)
            $cancellationCostLines = WalletLine::whereHas('wallet', function ($query) use ($userUuid) {
                $query->where('user_uuid', $userUuid);
            })
                ->where('state', WalletLine::CANCELLATION_COST)
                ->lockForUpdate()
                ->get();

            $cancellationCosts = $cancellationCostLines->sum('amount');

            // Het uit te betalen bedrag: beschikbare wallet lines + annuleringskosten (die negatief zijn)
            $totalAvailable = $totalAvailableFromLines + $cancellationCosts;

            if ($totalAvailable < 1) {
                DB::rollBack();

                return redirect()->route('dashboard.wallet.home')->with('message', 'Het minimale uitbetaalbedrag is €1,00');
            }

            // if ($this->bankingRepository->checkLastPaymentDate($userUuid)) {
            //     DB::rollBack();
            //     return redirect()->route('dashboard.wallet.home')->with('message', 'Je kan maar 1 keer per dag uitbetalen');
            // }

            // Update alle beschikbare wallet lines naar PAYOUT_INITIATED
            foreach ($availableWalletLines as $walletLine) {
                $walletLine->update([
                    'state' => WalletLine::PAYOUT_INITIATED,
                    'updated_at' => now(),
                ]);

                if ($order = $walletLine->order) {
                    $order->update(['payment_state' => Order::PAYOUT_PENDING]);
                }
            }

            // Update alle annuleringskosten naar PAID_OUT
            foreach ($cancellationCostLines as $costLine) {
                $costLine->update(['state' => WalletLine::CANCELLATION_COST_PAID_OUT]);
            }

            // Reset beschikbaar saldo naar 0
            $wallet->update(['total_available' => 0]);

            // Log voor debugging
            Log::info("Payout voor user {$userUuid}: Beschikbaar=€{$totalAvailableFromLines}, Kosten=€{$cancellationCosts}, Totaal=€{$totalAvailable}, Wallet lines={$availableWalletLines->count()}");

            $payment = $this->bankingRepository->createPayment(
                new PaymentDto(
                    $userUuid,
                    $banking->getUuid(),
                    $totalAvailable,
                    Payment::INITIATED,
                    'manual',
                    null
                )
            );

            DB::commit();

            return redirect()->route('dashboard.wallet.home')
                ->with('message', 'Uitbetaling is aangevraagd en wordt behandeld door het team van DeBurenKoken.nl')
                ->with('payout_success', true);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Payout failed voor user {$userUuid}: ".$e->getMessage());

            return redirect()->route('dashboard.wallet.home')
                ->with('message', 'Er is iets fout gegaan bij de uitbetaling. Probeer het opnieuw.');
        }
    }

    public function payTransactionCosts()
    {
        $mollie = new \Mollie\Api\MollieApiClient;
        $mollie->setApiKey(env('MOLLIE_API_KEY'));

        $wallet = $this->walletRepository->findWalletForUser(
            $this->request->user()->getUuid()
        );

        // GEFIXED: Gebruik het negatieve saldo in plaats van totale transactiekosten
        $transactionCost = 0;
        if ($wallet->getTotalAvailable() < 0) {
            $transactionCost = abs($wallet->getTotalAvailable());
        }

        if ($transactionCost == 0) {
            return redirect()->route('dashboard.wallet.iban');
        }

        try {
            $amount = number_format($transactionCost, 2, '.', '');

            // Maak een betaling aan met de gegevens van de bestelling
            $payment = $mollie->payments->create([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => $amount, // Bedrag dat moet worden betaald (alleen transactiekosten)
                ],
                'description' => 'Transactiekosten', // Beschrijving van de betaling
                'redirectUrl' => route('dashboard.wallet.pay.transaction.confirm'), // URL om naar door te verwijzen na betaling
                'cancelUrl' => route('dashboard.wallet.pay.transaction.cancel'), // URL om naar door te verwijzen na annuleren van de betaling
                'webhookUrl' => route('mollie.webhook.wallet'), // URL voor server-to-server status updates van Mollie
                'metadata' => [
                    'type' => 'transaction_costs',
                    'user_uuid' => $this->request->user()->getUuid(),
                    'wallet_uuid' => $wallet->getUuid(),
                ],
            ]);

            session()->flash('paymentId', $payment->id);

            // Redirect naar de betaalpagina van Mollie
            return redirect($payment->getCheckoutUrl());

        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            // Als er een fout optreedt bij het aanmaken van de betaling
            Log::error('Transactiekosten betaling fout: '.$e->getMessage());

            return redirect()->route('dashboard.wallet.home')->with('message', 'Er is iets fout gegaan bij het betalen van de transactiekosten.');
        }
    }

    public function payTransactionCostsCancel(): RedirectResponse
    {
        // Clear the payment session
        session()->forget('paymentId');

        return redirect()->route('dashboard.wallet.home')
            ->with('message', 'Betaling van transactiekosten is geannuleerd');
    }

    public function payTransactionCostsConfirm()
    {
        $mollie = new \Mollie\Api\MollieApiClient;
        $mollie->setApiKey(env('MOLLIE_API_KEY'));

        $user = $this->request->user();
        $wallet = $this->walletRepository->findWalletForUser($user->getUuid());
        $transactions = $this->walletRepository->getTransactionCostsWalletLines($wallet->getUuid());

        $paymentId = session()->get('paymentId');

        if (is_null($paymentId)) {
            return redirect()->route('dashboard.wallet.home')->with('message', 'Er is iets fout gegaan');
        }

        $payment = $mollie->payments->get($paymentId);

        if ($payment->isPaid()) {
            DB::beginTransaction();

            try {
                // Check if there are any transactions to process
                if ($transactions->isEmpty()) {
                    DB::rollBack();

                    return redirect()->route('dashboard.wallet.home')->with('message', 'Geen openstaande transactiekosten gevonden');
                }

                // Bereken hoeveel van het beschikbare saldo "gebruikt" wordt
                $totalCostsPaid = abs($transactions->sum('amount'));
                $amountPaid = floatval($payment->amount->value);

                // Markeer alle transactiekosten als betaald
                foreach ($transactions as $transaction) {
                    $transaction->update(['state' => WalletLine::CANCELLATION_COST_PAID]);
                }

                // Haal beschikbare wallet lines op
                $availableLines = WalletLine::whereHas('wallet', function ($query) use ($user) {
                    $query->where('user_uuid', $user->getUuid());
                })
                    ->whereIn('state', [WalletLine::AVAILABLE, WalletLine::COMPLETED])
                    ->get();

                // Het deel van het beschikbare saldo dat "gebruikt" werd voor de schuld
                $usedFromAvailable = $totalCostsPaid - $amountPaid;

                $remainingToMark = $usedFromAvailable;
                foreach ($availableLines as $line) {
                    if ($remainingToMark <= 0) {
                        break;
                    }

                    if ($line->getAmount() <= $remainingToMark) {
                        // Hele wallet line wordt gemarkeerd als gebruikt voor schuldafbetaling
                        $line->update(['state' => WalletLine::CANCELLATION_COST_PAID_OUT]);
                        $remainingToMark -= $line->getAmount();
                    }
                }

                DB::commit();

                return redirect()->route('dashboard.wallet.home')->with('message', 'Transactiekosten zijn betaald');

            } catch (\Exception $e) {
                DB::rollBack();

                return redirect()->route('dashboard.wallet.home')->with('message', 'Er is een fout opgetreden bij het verwerken van de betaling');
            }
        } else {
            return redirect()->route('dashboard.wallet.home')->with('message', 'Betaling is niet gelukt');
        }
    }

    /**
     * Handle Mollie webhook callbacks voor wallet-gerelateerde betalingen.
     * Dit omvat transactiekosten betalingen.
     *
     * BELANGRIJK: Dit is een server-to-server call van Mollie, geen browser request.
     */
    public function handleMollieWalletWebhook(Request $request): \Illuminate\Http\Response
    {
        $paymentId = $request->input('id');

        Log::info('Mollie wallet webhook received', ['payment_id' => $paymentId]);

        if (! $paymentId) {
            return response('OK', 200);
        }

        try {
            $mollie = new \Mollie\Api\MollieApiClient;
            $mollie->setApiKey(env('MOLLIE_API_KEY'));

            $payment = $mollie->payments->get($paymentId);

            Log::info('Mollie wallet webhook payment status', [
                'payment_id' => $paymentId,
                'status' => $payment->status,
                'type' => $payment->metadata->type ?? 'unknown',
            ]);

            // Controleer het type betaling
            if (! isset($payment->metadata->type)) {
                Log::warning('Wallet webhook received for payment without type metadata', ['payment_id' => $paymentId]);

                return response('OK', 200);
            }

            if ($payment->metadata->type === 'transaction_costs' && $payment->isPaid()) {
                $this->processTransactionCostsPaymentFromWebhook($payment);
            } elseif ($payment->metadata->type === 'iban_verification' && $payment->isPaid()) {
                $this->processIbanVerificationFromWebhook($payment);
            }

            return response('OK', 200);

        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            Log::error('Mollie API error in wallet webhook: '.$e->getMessage(), [
                'payment_id' => $paymentId,
            ]);

            return response('Error', 500);

        } catch (\Exception $e) {
            Log::error('Wallet webhook processing failed: '.$e->getMessage(), [
                'payment_id' => $paymentId,
                'trace' => $e->getTraceAsString(),
            ]);

            return response('Error', 500);
        }
    }

    /**
     * Verwerk transactiekosten betaling vanuit de webhook.
     */
    private function processTransactionCostsPaymentFromWebhook($payment): void
    {
        $userUuid = $payment->metadata->user_uuid;
        $walletUuid = $payment->metadata->wallet_uuid;

        DB::beginTransaction();

        try {
            $wallet = Wallet::where('uuid', $walletUuid)->lockForUpdate()->first();

            if (! $wallet) {
                Log::warning('Wallet not found for transaction costs webhook', ['wallet_uuid' => $walletUuid]);
                DB::rollBack();

                return;
            }

            $transactions = $this->walletRepository->getTransactionCostsWalletLines($wallet->getUuid());

            if ($transactions->isEmpty()) {
                Log::info('No transaction costs to process via webhook', ['wallet_uuid' => $walletUuid]);
                DB::commit();

                return;
            }

            // Bereken hoeveel van het beschikbare saldo "gebruikt" wordt
            $totalCostsPaid = abs($transactions->sum('amount'));
            $amountPaid = floatval($payment->amount->value);

            // Markeer alle transactiekosten als betaald
            foreach ($transactions as $transaction) {
                $transaction->update(['state' => WalletLine::CANCELLATION_COST_PAID]);
            }

            // Haal beschikbare wallet lines op
            $availableLines = WalletLine::where('wallet_uuid', $walletUuid)
                ->whereIn('state', [WalletLine::AVAILABLE, WalletLine::COMPLETED])
                ->get();

            // Het deel van het beschikbare saldo dat "gebruikt" werd voor de schuld
            $usedFromAvailable = $totalCostsPaid - $amountPaid;

            $remainingToMark = $usedFromAvailable;
            foreach ($availableLines as $line) {
                if ($remainingToMark <= 0) {
                    break;
                }

                if ($line->getAmount() <= $remainingToMark) {
                    $line->update(['state' => WalletLine::CANCELLATION_COST_PAID_OUT]);
                    $remainingToMark -= $line->getAmount();
                }
            }

            DB::commit();

            Log::info('Transaction costs processed successfully via webhook', [
                'wallet_uuid' => $walletUuid,
                'amount_paid' => $amountPaid,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process transaction costs in webhook: '.$e->getMessage(), [
                'wallet_uuid' => $walletUuid,
            ]);
            throw $e;
        }
    }

    /**
     * Verwerk IBAN verificatie betaling vanuit de webhook.
     */
    private function processIbanVerificationFromWebhook($payment): void
    {
        $userUuid = $payment->metadata->user_uuid;
        $bankingUuid = $payment->metadata->banking_uuid;

        Log::info('Processing IBAN verification from webhook', [
            'user_uuid' => $userUuid,
            'banking_uuid' => $bankingUuid,
            'payment_id' => $payment->id,
        ]);

        DB::beginTransaction();

        try {
            $iban = Banking::where('uuid', $bankingUuid)->lockForUpdate()->first();

            if (! $iban) {
                Log::warning('Banking record not found for IBAN verification webhook', [
                    'banking_uuid' => $bankingUuid,
                ]);
                DB::rollBack();

                return;
            }

            // Idempotentie check: als IBAN al geverifieerd is met dit payment ID, skip
            if ($iban->isValidated() && $iban->isVerified() && $iban->getPaymentId() === $payment->id) {
                Log::info('IBAN already verified via this payment', ['banking_uuid' => $bankingUuid]);
                DB::commit();

                return;
            }

            $user = User::where('uuid', $userUuid)->first();

            if (! $user) {
                Log::warning('User not found for IBAN verification webhook', ['user_uuid' => $userUuid]);
                DB::rollBack();

                return;
            }

            $oldIban = $iban->getIban();
            $newIban = $payment->details->consumerAccount;

            // Track IBAN change history
            $changeCount = IbanChangeHistory::where('user_uuid', $user->getUuid())->count() + 1;

            IbanChangeHistory::create([
                'user_uuid' => $user->getUuid(),
                'old_iban' => $oldIban,
                'new_iban' => $newIban,
                'change_count' => $changeCount,
            ]);

            // Update banking record
            $iban->update([
                'account_holder' => $payment->details->consumerName,
                'iban' => $newIban,
                'validated' => true,
                'bic' => $payment->details->consumerBic,
                'verified' => true,
            ]);

            DB::commit();

            // Emails versturen (buiten transactie)
            $isNewIban = empty($oldIban);

            if ($changeCount > 3) {
                Mail::to(
                    config('mail.admin.address'),
                    config('mail.admin.name')
                )->send(new AdminIbanChangeNotification($user, $changeCount));
            }

            if ($isNewIban) {
                Mail::to(
                    $user->getEmail(),
                    $user->getUsername()
                )->send(new IbanVerificationMail($user));
            } elseif (! empty($oldIban) && $oldIban !== $newIban) {
                Mail::to(
                    $user->getEmail(),
                    $user->getUsername()
                )->send(new IbanChangeMail($user));
            }

            Log::info('IBAN verification processed successfully via webhook', [
                'user_uuid' => $userUuid,
                'new_iban' => substr($newIban, 0, 4).'****'.substr($newIban, -4),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process IBAN verification in webhook: '.$e->getMessage(), [
                'banking_uuid' => $bankingUuid,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
