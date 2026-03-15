<?php

namespace App\Http\Controllers;

use App\Dtos\AdvertDto;
use App\Dtos\WalletLineDto;
use App\Mail\AdvertChangeCookMail;
use App\Mail\AdvertChangeCustomerMail;
use App\Mail\AdvertPreparationMail;
use App\Mail\AlertNextCancellationCost;
use App\Mail\CancelAdvertCookMail;
use App\Mail\CancelAdvertCustomerMail;
use App\Mail\CancellationReviewMail;
use App\Mail\ReviewMail;
use App\Mail\SubscriberAdvertInform;
use App\Models\Advert;
use App\Models\Banking;
use App\Models\Client;
use App\Models\Cook;
use App\Models\Order;
use App\Models\User;
use App\Models\WalletLine;
use App\Repositories\AdvertRepository;
use App\Repositories\CookRepository;
use App\Repositories\DishRepository;
use App\Repositories\OrderRepository;
use App\Repositories\WalletRepository;
use App\Rules\MaxDaysBetweenOrderAndPickup;
use App\Rules\MaxThreeMonths;
use App\Rules\MinTimeDifference;
use App\Rules\PastTime;
use App\Rules\Timestamps;
use App\Services\Dac7Service;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Cookie;

class AdvertsController extends Controller
{
    private Request $request;

    private DishRepository $dishRepository;

    private AdvertRepository $advertRepository;

    private OrderRepository $orderRepository;

    private CookRepository $cookRepository;

    private WalletRepository $walletRepository;

    private Advert $advert;

    public function __construct(
        Request $request,
        DishRepository $dishRepository,
        AdvertRepository $advertRepository,
        OrderRepository $orderRepository,
        CookRepository $cookRepository,
        WalletRepository $walletRepository,
        Advert $advert
    ) {
        $this->request = $request;
        $this->dishRepository = $dishRepository;
        $this->advertRepository = $advertRepository;
        $this->orderRepository = $orderRepository;
        $this->cookRepository = $cookRepository;
        $this->walletRepository = $walletRepository;
        $this->advert = $advert;
    }

    public function activeAdverts(): View
    {
        /** @var User $user */
        $user = $this->request->user();

        $from = $this->request->query('from') ?? $user->getCreatedAt()->format('Y-m-d');
        $to = $this->request->query('to') ?? Carbon::now()->endOfMonth()->addYear()->format('Y-m-d');

        $adverts = $this->advertRepository->getUsersAdverts(
            $user->getUuid(),
            $from,
            $to
        )->load('order.client');

        $newAdvertsCollection = new Collection;
        $page = $this->request->query('page');
        $perPage = 10;
        $past = false;

        foreach ($adverts as $advert) {
            // Forceer bijwerken van order statussen voor deze advertentie
            if ($advert->getParsedPickupTo()->isPast()) {
                // Update alle nog actieve orders naar VERLOPEN als de afhaalperiode voorbij is
                $advert->order()
                    ->where('status', Order::STATUS_ACTIEF)
                    ->where('payment_state', Order::SUCCEED)
                    ->update(['status' => Order::STATUS_VERLOPEN]);

                // Update wallet lines voor deze orders
                foreach ($advert->order()->where('status', Order::STATUS_VERLOPEN)->get() as $order) {
                    $walletLine = $this->walletRepository->findWalletLineByOrderUuid($order->getUuid());
                    if ($walletLine && $walletLine->getState() == WalletLine::PROCESSING) {
                        $walletLine->update(['state' => WalletLine::AVAILABLE]);

                        // Controleer Dac7 na statusverandering
                        if ($order->user) {
                            app(Dac7Service::class)->checkUserDac7Thresholds($order->user);
                        }
                    }
                }
            }

            // Synchroniseer het succeededAmount met de database-waarde
            $advert->succeeded_amount = $advert->getSucceedAmount();

            // Filter logic remains unchanged
            if (
                ! $advert->published() && $past && is_null($advert->deleted_at) ||
                (! $advert->isCancelled() && $advert->getParsedPickupTo()->isFuture() && $past) ||
                ($advert->published() && $advert->getParsedOrderTo()->isPast() && $advert->getParsedPickupTo()->isPast() && ! $past)
            ) {
                continue;
            }

            $newAdvertsCollection->add($advert);
        }

        $filterString = "?from=$from&to=$to";
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $newAdverts = new LengthAwarePaginator(
            $newAdvertsCollection->forPage($page, $perPage),
            $newAdvertsCollection->count(),
            $perPage,
            $page,
            []
        );

        $newAdverts->setPath("/dashboard/adverts/active$filterString");

        return view('dashboard.adverts.index', [
            'adverts' => $newAdverts,
            'past' => false,
            'title' => 'Advertenties',
            'from' => $from,
            'to' => $to,
            'min' => date('Y-m-d'),
            'availableCount' => $this->walletRepository->countAvailableWalletLinesForUser($user->getUuid()),
        ]);
    }

    public function pastAdverts(): View
    {
        /** @var User $user */
        $user = $this->request->user();

        $from = $this->request->query('from') ?? $user->getCreatedAt()->format('Y-m-d');
        $to = $this->request->query('to') ?? Carbon::now()->endOfMonth()->addYear()->format('Y-m-d');

        $adverts = $this->advertRepository->getPastUserAdverts(
            $this->request->user()->getUuid(),
            $from,
            $to
        )->load('order.client');

        // Laad de juiste order-relatie voor elke advertentie
        $advertIds = $adverts->pluck('uuid')->toArray();
        $allOrders = \App\Models\Order::whereIn('advert_uuid', $advertIds)
            ->with('client')
            ->get()
            ->groupBy('advert_uuid');

        $newAdvertsCollection = new Collection;
        $page = $this->request->query('page');
        $perPage = 10;
        $past = true;

        foreach ($adverts as $advert) {
            // Forceer bijwerken van order statussen voor deze advertentie
            if ($advert->getParsedPickupTo()->isPast()) {
                // Update alle nog actieve orders naar VERLOPEN als de afhaalperiode voorbij is
                $advert->order()
                    ->where('status', Order::STATUS_ACTIEF)
                    ->where('payment_state', Order::SUCCEED)
                    ->update(['status' => Order::STATUS_VERLOPEN]);

                // Update wallet lines voor deze orders
                foreach ($advert->order()->where('status', Order::STATUS_VERLOPEN)->get() as $order) {
                    $walletLine = $this->walletRepository->findWalletLineByOrderUuid($order->getUuid());
                    if ($walletLine && $walletLine->getState() == WalletLine::PROCESSING) {
                        $walletLine->update(['state' => WalletLine::AVAILABLE]);
                    }
                }
            }

            // Stel expliciet de order-relatie in op basis van de eerdere query
            if (isset($allOrders[$advert->getUuid()])) {
                $advert->setRelation('order', $allOrders[$advert->getUuid()]);
            }

            // Synchroniseer het succeededAmount met de database-waarde
            $advert->succeeded_amount = $advert->getSucceedAmount();

            if (
                ! $advert->published() && $past && is_null($advert->deleted_at) ||
                (! $advert->isCancelled() && $advert->getParsedPickupTo()->isFuture() && $past) ||
                ($advert->published() && $advert->getParsedOrderTo()->isPast() && $advert->getParsedPickupTo()->isPast() && ! $past)
            ) {
                continue;
            }

            $newAdvertsCollection->add($advert);
        }

        $filterString = "?from=$from&to=$to";

        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $newAdverts = new LengthAwarePaginator($newAdvertsCollection->forPage($page, $perPage), $newAdvertsCollection->count(), $perPage, $page, []);

        $newAdverts->setPath("/dashboard/adverts/past$filterString");

        return view('dashboard.adverts.index', [
            'adverts' => $newAdverts,
            'past' => true,
            'title' => 'Advertenties',
            'from' => $from,
            'to' => $to,
            'min' => $adverts->last()?->getCreatedAt()?->format('Y-m-d'),
        ]);
    }

    public function showAdvert(string $uuid): View|RedirectResponse
    {
        $advert = $this->advertRepository->find($uuid);

        if (! $advert) {
            return redirect()->route('dashboard.adverts.active.home')->with('error', 'Direct access not allowed.');
        }

        // Haal alle orders voor deze advertentie op, exclusief IN_PROCESS bestellingen
        $orders = $this->orderRepository->getAllOrdersByAdvertUuid($advert->getUuid())
            ->filter(function ($order) {
                // Alleen bestellingen met succesvol betaalde status tonen
                return $order->payment_state != Order::IN_PROCESS && $order->payment_state != Order::FAILED;
            });

        return view('dashboard.adverts.detail', [
            'advert' => $advert,
            'orders' => $orders,
            'user' => $this->request->user(),
            'past' => (bool) $this->request->query('past', false),
        ]);
    }

    private function countTodayAdvertsForUser(string $userUuid): int
    {
        return Advert::withTrashed()
            ->whereHas('dish', function ($query) use ($userUuid) {
                $query->where('user_uuid', $userUuid);
            })
            ->whereDate('created_at', Carbon::today())
            ->count();
    }

    public function createAdvert(): View|RedirectResponse
    {
        $availableCount = $this->walletRepository->countAvailableWalletLinesForUser($this->request->user()->getUuid());
        if ($availableCount < 0) {
            return redirect()->route('dashboard.adverts.active.home');
        }

        $user = $this->request->user();
        $userProfile = $user->userProfile;
        $cook = $user->cook;
        $banking = $user->banking;

        $todayAdvertCount = $this->countTodayAdvertsForUser($user->getUuid());

        if ($todayAdvertCount >= 25) {
            return redirect()->route('dashboard.adverts.active.home')->with('error', 'Je hebt het maximale aantal advertenties (25) voor vandaag bereikt. Probeer het morgen opnieuw.');
        }

        $selectedDish = session('dish_id') ?? false;
        session()->forget('dish_id');

        $hasRequiredInfo = ! is_null($cook) && ! is_null($banking);

        if ($banking) {
            $hasRequiredInfo = $hasRequiredInfo && $banking->isVerified();
        }

        return view('dashboard.adverts.single', [
            'edit' => false,
            'dishes' => $this->dishRepository->getDishesByUserUuid($user), // IMAGE RELATIE AL GELADEN
            'title' => 'Advertenties',
            'active' => ! is_null($user->cook),
            'published' => false,
            'adress' => $cook ? $cook->getAdress() : null,
            'phone' => $userProfile ? $userProfile->getPhoneNumber() : null,
            'selectedDish' => $selectedDish,
            'email' => $user->getEmail(),
            'canCreateMoreAdverts' => $todayAdvertCount < 25,
            'hasRequiredInfo' => $hasRequiredInfo,
            'todayAdvertCount' => $todayAdvertCount,
        ]);
    }

    public function createAdvertWithDish(Request $request, $uuid): RedirectResponse
    {
        return redirect()->route('dashboard.adverts.create')->with('dish_id', $uuid);
    }

    public function editAdvert(string $uuid): View|RedirectResponse
    {
        $advert = $this->advertRepository->find($uuid);

        if (! $advert) {
            return redirect()->route('dashboard.adverts.active.home')->with('error', 'Direct access not allowed.');
        }

        $user = $this->request->user();
        $userProfile = $user->userProfile;
        $cook = $user->cook;

        return view('dashboard.adverts.single', [
            'edit' => true,
            'dishes' => $this->dishRepository->getDishesByUserUuid($user),
            'advert' => $advert,
            'user' => $this->request->user(),
            'title' => 'Advertenties',
            'active' => ! is_null($this->request->user()->cook),
            'published' => is_null($advert->published()),
            'concept' => (int) ! $advert->isPublished(),
            'selectedDish' => false,
            'adress' => $cook ? $cook->getAdress() : null,
            'phone' => $userProfile ? $userProfile->getPhoneNumber() : null,
            'email' => $user->getEmail(),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function storeAdvert(): RedirectResponse
    {
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $user = $this->request->user();

        $todayAdvertCount = $this->countTodayAdvertsForUser($user->getUuid());
        if ($todayAdvertCount >= 25) {
            throw ValidationException::withMessages([
                'limit' => 'Je hebt het maximale aantal advertenties (25) voor vandaag bereikt. Probeer het morgen opnieuw.',
            ]);
        }

        // Create submission hash
        $submissionData = [
            'user_uuid' => $user->getUuid(),
            'dish' => $this->request->input('dish'),
            'available' => $this->request->input('available'),
            'pickup_date' => $this->request->input('pickup_date'),
            'pickup_from' => $this->request->input('pickup_from'),
            'pickup_to' => $this->request->input('pickup_to'),
            'order_date' => $this->request->input('order_date'),
            'order_time' => $this->request->input('order_time'),
        ];

        $submissionHash = md5(serialize($submissionData));
        $submissionKey = 'advert_submission_'.$submissionHash;

        // Check if this exact submission was made in the last 5 minutes
        if (session()->has($submissionKey) && session($submissionKey) > now()->subMinutes(5)) {
            return redirect()->route('dashboard.adverts.active.home')
                ->with('error', 'Deze advertentie is al aangemaakt. Controleer je advertentielijst.');
        }

        // VALIDATE FIRST - before setting session key
        $this->request->validate([
            'dish' => 'required',
            'available' => ['required', 'numeric', 'min:1', 'max:25'],
            'pickup_date' => ['required', 'date', new MaxThreeMonths],
            'pickup_from' => ['required', 'date_format:H:i'],
            'pickup_to' => ['required', 'date_format:H:i', 'after:pickup_from', new MinTimeDifference(30)],
            'order_date' => ['required', 'date', new MaxDaysBetweenOrderAndPickup, new MaxThreeMonths, new Timestamps($this->request), new PastTime($this->request)],
            'order_time' => ['required', 'date_format:H:i'],
        ]);

        $banking = $user->banking;

        if (is_null($banking) || ! $banking->isValidated()) {
            return redirect()->route('dashboard.wallet.iban')->with('message', 'Voeg een geldig IBAN toe voordat je een advertentie plaatst.');
        }

        // Get and validate dish
        $selectedDish = $this->dishRepository->find($this->request->input('dish'));
        if (! $selectedDish) {
            return redirect()->back()->with('error', 'Het gekozen gerecht bestaat niet.');
        }

        if (! $selectedDish->getPortionPrice() || $selectedDish->getPortionPrice() <= 0) {
            return redirect()->back()->with('error', 'Het gekozen gerecht heeft nog geen prijs ingesteld. Ga naar Gerechten en stel eerst een prijs in.');
        }

        // ONLY NOW set the session key after all validation passes
        session()->put($submissionKey, now());

        $publish = $this->request->input('concept') === 'true';
        $createCook = false;
        $createBanking = false;

        if ($publish && (is_null($user->cook) || is_null($banking))) {
            session()->forget($submissionKey); // Remove key if this check fails

            return redirect()->back()->with('error', 'Je moet eerst je gegevens en IBAN invullen voordat je een advertentie online kunt plaatsen.');
        }

        if (is_null($user->cook)) {
            $createCook = true;
        }

        if (is_null($banking)) {
            $createBanking = true;
        }

        // Check for duplicate adverts
        $recentAdvert = $this->advertRepository->getUsersAdverts($user->getUuid())
            ->where('created_at', '>', now()->subHour())
            ->where('dish_uuid', $selectedDish->getUuid())
            ->where('pickup_date', $this->request->input('pickup_date'))
            ->where('pickup_from', $this->request->input('pickup_from'))
            ->where('order_date', $this->request->input('order_date'))
            ->first();

        if ($recentAdvert) {
            session()->forget($submissionKey);

            return redirect()->route('dashboard.adverts.active.home')
                ->with('error', 'Er is al een identieke advertentie aangemaakt in het afgelopen uur.');
        }

        try {
            // Create advert
            $advert = $this->advertRepository->create(
                new AdvertDto(
                    $selectedDish,
                    $this->request->input('available'),
                    $this->request->input('pickup_date'),
                    $this->request->input('pickup_from'),
                    $this->request->input('pickup_to'),
                    $this->request->input('order_date'),
                    $this->request->input('order_time'),
                    false
                )
            );

            \Log::info('Advert created successfully', [
                'advert_id' => $advert->getUuid(),
                'user_id' => $user->getUuid(),
                'submission_hash' => $submissionHash,
            ]);

            if ($createCook && $publish) {
                $this->request->session()->keep(['profile' => 'required']);

                return redirect()->route('verification.first', ['advert_uuid' => $advert->getUuid()]);
            }

            if ($createBanking && $publish) {
                return redirect()->route('verification.banking');
            }

            if ($publish) {
                return $this->publishAdvert($advert->getUuid());
            }

            return redirect()->route('dashboard.adverts.active.home')
                ->with('message', 'Advertentie succesvol aangemaakt.');

        } catch (\Exception $e) {
            // Remove session key if creation fails
            session()->forget($submissionKey);

            \Log::error('Advert creation failed', [
                'user_id' => $user->getUuid(),
                'error' => $e->getMessage(),
                'submission_hash' => $submissionHash,
            ]);

            return redirect()->back()
                ->with('error', 'Er is een fout opgetreden bij het aanmaken van de advertentie. Probeer het opnieuw.');
        }
    }

    /**
     * @throws \Exception
     */
    public function publishAdvert(string $uuid): RedirectResponse
    {

        $advert = $this->advertRepository->find($uuid);

        if (! $advert) {
            return redirect()->route('dashboard.adverts.active.home')->with('error', 'Direct access not allowed.');
        }
        $advert->preparation_email_sent = false;
        $advert->save();
        if (
            $advert->getParsedOrderTo()->isPast()
        ) {
            throw new \Exception('Pas advertentie aan.');
        }

        /** @var User $user */
        $user = $this->request->user();

        /** @var Banking $banking */
        $banking = $user->banking;

        if (
            ! is_null($user->userProfile) &&
            ! is_null($user->cook) &&
            ! is_null($banking) &&
            $banking->isValidated()
        ) {
            $advert = $this->advertRepository->publishAdvert($uuid);

            $user = $this->request->user();
            $subscribers = $this->cookRepository->findCookSubscribersByCookUuid($user->cook->getUuid());
            $cook = $user->cook;

            /** @var Client $subscriber */
            foreach ($subscribers as $subscriber) {
                Mail::to(
                    $subscriber->getEmail(),
                    $subscriber->getName()
                )->send(new SubscriberAdvertInform(
                    $cook,
                    $advert,
                    $subscriber
                ));
            }
        }

        $this->request->session()->keep(['profile' => $this->request->user()->getUuid()]);

        $accepted = cookie('accepted_cookies')->getValue();

        if ($accepted === true) {
            // return redirect()->route('dashboard.adverts.active.home')->withCookie(Cookie::create('profile', $this->request->user()->getUuid()));
            return redirect()->route('dashboard.adverts.active.home');

        } else {
            return redirect()->route('dashboard.adverts.active.home');
        }

    }

    public function updateAdvert(Request $request, string $uuid): RedirectResponse|View
    {
        if (! $request->isMethod('PATCH')) {
            return abort(404);
        }

        $advert = $this->advertRepository->find($uuid);

        if (! $advert) {
            return redirect()->route('dashboard.adverts.active.home')->with('error', 'Direct access not allowed.');
        }

        $isChangingOrderDate = $request->input('order_date') !== $advert->order_date;
        $isChangingOrderTime = $request->input('order_time') !== $advert->order_time;

        $validationRules = [];

        if ($request->has('available')) {
            $validationRules['available'] = ['required', 'numeric', 'min:1', 'max:25'];
        }

        if ($isChangingOrderDate) {
            $inputDate = $request->input('order_date');
            $today = Carbon::now()->format('Y-m-d');

            if ($inputDate && $inputDate < $today) {
                return redirect()->back()->withErrors([
                    'order_date' => 'Het uiterste bestelmoment kan niet in het verleden liggen.',
                ]);
            }

            $validationRules['order_date'] = ['required', 'date', 'after_or_equal:today', new MaxThreeMonths];
        }

        if ($isChangingOrderTime) {
            $validationRules['order_time'] = ['required', 'date_format:H:i'];
        }

        // Valideer afhaaldatum mag niet gewijzigd worden bij edit
        if ($request->has('pickup_date') && $request->input('pickup_date') !== $advert->pickup_date) {
            return redirect()->back()->withErrors(['pickup_date' => 'De afhaaldatum mag niet gewijzigd worden.']);
        }

        if ($request->has('pickup_from') && $request->input('pickup_from') !== $advert->pickup_from) {
            return redirect()->back()->withErrors(['pickup_from' => 'De afhaaltijd mag niet gewijzigd worden.']);
        }

        if ($request->has('pickup_to') && $request->input('pickup_to') !== $advert->pickup_to) {
            return redirect()->back()->withErrors(['pickup_to' => 'De afhaaltijd mag niet gewijzigd worden.']);
        }

        $validated = $request->validate($validationRules);

        // Extra validatie: controleer of het bestelmoment in de toekomst ligt
        if ($isChangingOrderDate || $isChangingOrderTime) {
            $orderDate = $request->input('order_date', $advert->order_date);
            $orderTime = $request->input('order_time', $advert->order_time);

            $orderDateTime = Carbon::parse($orderDate.' '.$orderTime);
            $now = Carbon::now();

            if ($orderDateTime->lte($now)) {
                return redirect()->back()->withErrors(['order_date' => 'De datum/tijd moet in de toekomst liggen.']);
            }

            $pickupDate = $advert->pickup_date;
            $pickupFrom = $advert->pickup_from;
            $pickupDateTime = Carbon::parse($pickupDate.' '.$pickupFrom);

            if ($orderDateTime->gte($pickupDateTime)) {
                return redirect()->back()->withErrors(['order_date' => 'Het uiterste bestelmoment moet voor het afhaalmoment zijn.']);
            }
        }

        if ($advert->portion_amount - $advert->getLeftOverAmount() > $request->input('available')) {
            return redirect()->back()->with('message', 'Het aantal beschikbare porties kan niet lager zijn dan het aantal reeds bestelde porties.');
        }

        $wasConcept = $advert->isPublished();

        if ($advert->dish->getUserUuid() !== $request->user()->getUuid()) {
            throw new ModelNotFoundException;
        }

        $orderCount = $advert->order->count();
        $concept = true;
        $createCook = false;
        $addIban = false;

        if (is_null($request->user()->cook)) {
            $createCook = true;
        }

        if (is_null($request->user()->banking)) {
            $addIban = true;
        }

        if ($request->input('concept') === 'true' || $request->input('concept') === '1') {
            $concept = false;
        }

        if ($orderCount !== 0) {
            return view('dashboard.adverts.update-confirm', [
                'data' => $validated,
                'orderCount' => $orderCount,
                'hideMenu' => true,
                'advert' => $advert,
            ]);
        }

        $advertDto = new AdvertDto(
            $advert->dish,
            $request->input('available', $advert->portion_amount),

            $advert->pickup_date,
            $advert->pickup_from,
            $advert->pickup_to,
            $isChangingOrderDate ? $request->input('order_date') : $advert->order_date,
            $isChangingOrderTime ? $request->input('order_time') : $advert->order_time,
            ! $wasConcept
        );

        $advert = $this->advertRepository->update($advertDto, $uuid);
        $user = $request->user();

        if ($createCook && ! $concept) {
            $request->session()->keep(['profile' => 'required']);

            return redirect()->route('verification.first', ['advert_uuid' => $advert->getUuid()]);
        }

        if ($addIban && ! $concept) {
            $request->session()->keep(['iban' => 'required']);

            return redirect()->route('dashboard.wallet.iban')->with('message', 'Voeg iban toe');
        }

        if (! $concept) {
            $this->advertRepository->publishAdvert($uuid);
        }

        if ($wasConcept && $concept || $wasConcept && ! $concept) {
            return redirect()->route('dashboard.adverts.active.home')->with('message', 'Advertentie aangepast');
        }

        return view('dashboard.adverts.update-complete', [
            'advert' => $advert,
            'hideMenu' => true,
        ]);
    }

    public function submitUpdateAdvert(string $advertUuid): View
    {
        $advert = $this->advertRepository->find($advertUuid);

        if (
            ! is_null($advert) &&
            $advert->cook->getUserUuid() !== $this->request->user()->getUuid()
        ) {
            throw new ModelNotFoundException;
        }

        $changes = json_decode($this->request->input('requestItems'), true);

        // AANGEPAST: Geen prijs parameter meer
        $advert = $this->advertRepository->update(
            new AdvertDto(
                $advert->dish,
                array_key_exists('available', $changes) ? $changes['available'] : $advert->getPortionAmount(),
                // VERWIJDERD: prijs parameter - komt nu uit dish
                array_key_exists('pickup_date', $changes) ? $changes['pickup_date'] : $advert->getPickupDate(),
                array_key_exists('pickup_from', $changes) ? $changes['pickup_from'] : $advert->getPickupFrom(),
                array_key_exists('pickup_to', $changes) ? $changes['pickup_to'] : $advert->getPickupTo(),
                array_key_exists('order_date', $changes) ? $changes['order_date'] : $advert->getOrderDate(),
                array_key_exists('order_time', $changes) ? $changes['order_time'] : $advert->getOrderTime(),
                array_key_exists('concept', $changes) ? $changes['concept'] : ! is_null($advert->published())
            ),
            $advertUuid
        );
        $user = $this->request->user();

        /** @var Cook $cook */
        $cook = $user->cook;

        // if ($cook->getMailSelf()) {
        //     Mail::to(
        //         $user->getEmail(),
        //         $user->getUsername()
        //     )->send(new AdvertChangeCookMail(
        //         $user,
        //         $advert->dish->getTitle(),
        //         $advert->getUuid()
        //     ));
        // }

        /** @var Order $order */
        // foreach ($advert->order as $order) {
        //     Mail::to(
        //         $order->client->getEmail(),
        //         $order->client->getName()
        //     )->send(new AdvertChangeCustomerMail(
        //         $advert,
        //         $order,
        //         $this->request->input('editText')
        //     ));
        // }

        return view('dashboard.adverts.update-complete', [
            'advert' => $advert,
            'hideMenu' => true,
        ]);
    }

    private int $cancellationLimit = 10; // Zet op 1 voor testen

    public function cancelAdvert(Request $request, string $uuid): View|RedirectResponse
    {
        /** @var Advert $advert */
        if (! $advert = $this->advertRepository->find($uuid)) {
            return redirect()->route('dashboard.adverts.active.home')
                ->with('error', 'Direct access not allowed.');
        }

        //  Count only active orders with successful payment
        $activeOrders = $advert->order->filter(function ($order) {
            return $order->status !== Order::STATUS_GEANNULEERD
                && in_array($order->payment_state, [Order::SUCCEED, Order::PAYOUT_PENDING]);
        })->count();

        if ($activeOrders === 0) {
            return $this->submitCancelAdvert($uuid);
        }

        // Check if this would exceed the cancellation limit
        $ordersCanceledCount = $this->orderRepository->getCanceledOrdersByUser(
            $this->request->user()->getUuid(),
            'month',
            Order::CANCELLED_BY_COOK
        )->count();

        // Set warning flag if at cancellation limit
        $atCancellationLimit = $ordersCanceledCount >= $this->cancellationLimit;

        $ordersOverLimit = 0;
        $willExceedLimit = false;

        if ($ordersCanceledCount >= $this->cancellationLimit) {
            $ordersOverLimit = $activeOrders;
            $willExceedLimit = true;
        } elseif ($ordersCanceledCount + $activeOrders > $this->cancellationLimit) {
            // Sommige orders zullen transactiekosten hebben
            $ordersOverLimit = ($ordersCanceledCount + $activeOrders) - $this->cancellationLimit;
            $willExceedLimit = true;
        }

        return view('dashboard.adverts.cancel', [
            'advert' => $advert,
            'hideMenu' => true,
            'activeOrders' => $activeOrders,
            'atCancellationLimit' => $atCancellationLimit,
            'willExceedLimit' => $willExceedLimit,
            'ordersOverLimit' => $ordersOverLimit,
            'cancellationLimit' => $this->cancellationLimit,
        ]);
    }

    public function submitCancelAdvert(string $uuid): RedirectResponse
    {
        // Start database transaction with locking to prevent race conditions
        DB::beginTransaction();

        try {
            // CRITICAL FIX: Lock the advert record to prevent concurrent access
            $advert = Advert::where('uuid', $uuid)->lockForUpdate()->first();

            if (! $advert) {
                DB::rollBack();

                return redirect()->route('dashboard.adverts.active.home')
                    ->with('error', 'Direct access not allowed.');
            }

            // CRITICAL FIX: Check if already cancelled to prevent duplicate processing
            if ($advert->deleted_at !== null) {
                DB::rollBack();
                \Log::warning("Attempt to cancel already cancelled advert: {$uuid}");

                return redirect()->route('dashboard.adverts.active.home')
                    ->with('message', 'Advertentie is al geannuleerd.');
            }

            // Verify ownership
            $user = $advert->dish->user;
            if (! $user || $user->getUuid() !== $this->request->user()->getUuid()) {
                DB::rollBack();

                return redirect()->route('dashboard.adverts.active.home')
                    ->with('error', 'Geen toegang tot deze advertentie.');
            }

            $cancelText = $this->request->input('cancel_text');

            try {
                $mollie = new \Mollie\Api\MollieApiClient;
                $mollie->setApiKey(env('MOLLIE_API_KEY'));

                $ordersCanceledCount = $this->orderRepository->getCanceledOrdersByUser(
                    $user->getUuid(),
                    'month',
                    Order::CANCELLED_BY_COOK
                )->count();

                $cancelationFee = 0.60;
                $activeOrdersInAdvert = 0;
                $cancelledOrdersInThisAdvert = [];

                // First pass: count active orders
                foreach ($advert->order as $order) {
                    if ($order->status !== Order::STATUS_GEANNULEERD &&
                        in_array($order->payment_state, [Order::SUCCEED, Order::PAYOUT_PENDING])) {
                        $activeOrdersInAdvert++;
                    }
                }

                \Log::info("Processing cancellation for advert {$uuid} with {$activeOrdersInAdvert} active orders");

                // ✅ FIX: Second pass: process ONLY successfully paid orders
                foreach ($advert->order as $order) {
                    // Skip already cancelled orders
                    if ($order->status === Order::STATUS_GEANNULEERD) {
                        continue;
                    }

                    // ✅ FIX: Skip orders that were never successfully paid (IN_PROCESS, FAILED)
                    if (! in_array($order->payment_state, [Order::SUCCEED, Order::PAYOUT_PENDING])) {
                        \Log::info("Skipping order {$order->getUuid()} - payment_state: {$order->payment_state}");

                        continue;
                    }

                    $shouldProcessOrder = true; // We already know it's paid
                    $paymentId = $order->payment_id;

                    // Handle Mollie refunds
                    if (! empty($paymentId)) {
                        try {
                            $payment = $mollie->payments->get($paymentId);
                            $totalAmount = $payment->amount->value;

                            $refund = $mollie->payments->refund($payment, [
                                'amount' => [
                                    'currency' => $payment->amount->currency,
                                    'value' => $totalAmount,
                                ],
                            ]);

                            if ($refund->status == 'pending' || $refund->status == 'paid') {
                                if ($order->walletLine) {
                                    $this->walletRepository->updateWalletLine(
                                        WalletLine::REFUNDING,
                                        $order->walletLine->getUuid()
                                    );
                                }
                            }
                        } catch (\Mollie\Api\Exceptions\ApiException $e) {
                            \Log::error('Mollie Refund Error for order '.$order->getUuid().': '.$e->getMessage());
                            // Continue processing other orders even if one refund fails
                        }
                    }

                    // Now mark as cancelled (only paid orders reach this point)
                    $order->status = Order::STATUS_GEANNULEERD;
                    $order->cancelled_by = Order::CANCELLED_BY_COOK;
                    $order->save();

                    $cancelledOrdersInThisAdvert[] = $order;

                    // Queue customer notification emails to prevent blocking
                    if ($order->client) {
                        try {
                            Mail::to(
                                $order->client->getEmail(),
                                $order->client->getName()
                            )->queue(new CancelAdvertCustomerMail(
                                $advert,
                                $order,
                                $order->client,
                                $cancelText
                            ));
                        } catch (\Exception $e) {
                            \Log::error('Failed to queue customer cancellation email for order '.$order->getUuid().': '.$e->getMessage());
                        }
                    }

                    // Queue review emails
                    if ($order->user && $order->user->cook && $order->client) {
                        try {
                            Mail::to(
                                $order->client->getEmail(),
                                $order->client->getName()
                            )->queue(new CancellationReviewMail(
                                $order->client,
                                $order->getUuid(),
                                $order->user->cook
                            ));

                            $order->review_send = now();
                            $order->save();
                        } catch (\Exception $e) {
                            \Log::error('Failed to queue review email for order '.$order->getUuid().': '.$e->getMessage());
                        }
                    }
                }

                // Handle transaction costs calculation
                $wasUnderLimit = $ordersCanceledCount < $this->cancellationLimit;
                $isNowOverLimit = ($ordersCanceledCount + $activeOrdersInAdvert) > $this->cancellationLimit;

                if ($isNowOverLimit) {
                    $ordersProcessed = 0;

                    foreach ($cancelledOrdersInThisAdvert as $order) {
                        $ordersProcessed++;

                        if (($ordersCanceledCount + $ordersProcessed) > $this->cancellationLimit) {
                            $this->walletRepository->applyTransactionCost(
                                $user->getUuid(),
                                $order->getUuid(),
                                $cancelationFee
                            );
                        }
                    }

                    // Send alert email if crossing the limit for first time
                    if ($wasUnderLimit) {
                        try {
                            Mail::to(
                                $user->getEmail(),
                                $user->getUsername()
                            )->queue(new AlertNextCancellationCost($user, $cancelationFee, 'advertentie'));
                        } catch (\Exception $e) {
                            \Log::error('Failed to queue alert email: '.$e->getMessage());
                        }
                    }
                } elseif (($ordersCanceledCount + $activeOrdersInAdvert) == $this->cancellationLimit) {
                    // Send warning when exactly at the limit
                    try {
                        Mail::to(
                            $user->getEmail(),
                            $user->getUsername()
                        )->queue(new AlertNextCancellationCost($user, $cancelationFee, 'advertentie'));
                    } catch (\Exception $e) {
                        \Log::error('Failed to queue limit warning email: '.$e->getMessage());
                    }
                }

                // Send cook notification email
                if ($user) {
                    try {
                        Mail::to(
                            $user->getUsername(),
                            $user->getEmail()
                        )->queue(new CancelAdvertCookMail(
                            $user->getUsername(),
                            $advert->dish->getTitle(),
                            $advert->getUuid()
                        ));
                    } catch (\Exception $e) {
                        \Log::error('Failed to queue cook notification email: '.$e->getMessage());
                    }
                }

                $advert->deleted_at = now();
                $advert->save();

                // Commit the entire transaction
                DB::commit();

                \Log::info("Successfully cancelled advert {$uuid} with {$activeOrdersInAdvert} orders");

                return redirect()->route('dashboard.adverts.active.home')
                    ->with('message', 'Advertentie succesvol geannuleerd');

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Cancel Advert Error: '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine());

                return redirect()->route('dashboard.adverts.active.home')
                    ->with('error', 'Er is een probleem opgetreden bij het annuleren van de advertentie.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Outer Cancel Advert Error: '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine());

            return redirect()->route('dashboard.adverts.active.home')
                ->with('error', 'Er is een probleem opgetreden bij het annuleren van de advertentie. Probeer het later opnieuw.');
        }
    }
}
