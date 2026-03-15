<?php

namespace App\Http\Controllers;

use App\Dtos\UserDto;
use App\Mail\DailyAccountsLimitMail;
use App\Mail\OrderCookCancelled;
use App\Mail\OrderCreateCook;
use App\Mail\OrderCustomerCancelled;
use App\Mail\ReactivatedAccountVerifyMail;
use App\Mail\SubscriberCookInfoUpdateInform;
use App\Models\Order;
use App\Models\User;
use App\Repositories\AdvertRepository;
use App\Repositories\ClientRepository;
use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use App\Services\DestroyService;
use App\Services\MailService;
use App\Traits\DailyAccountLimit;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Cookie;

class RegisterController extends Controller
{
    private Request $request;

    private UserRepository $userRepository;

    private MailService $mailService;

    private WalletRepository $walletRepository;

    private DestroyService $destroyService;

    private AdvertRepository $advertRepository;

    private ClientRepository $clientRepository;

    private OrderRepository $orderRepository;

    public function __construct(
        Request $request,
        UserRepository $userRepository,
        MailService $mailService,
        WalletRepository $walletRepository,
        DestroyService $destroyService,
        AdvertRepository $advertRepository,
        ClientRepository $clientRepository,
        OrderRepository $orderRepository

    ) {
        $this->request = $request;
        $this->userRepository = $userRepository;
        $this->mailService = $mailService;
        $this->walletRepository = $walletRepository;
        $this->destroyService = $destroyService;
        $this->advertRepository = $advertRepository;
        $this->clientRepository = $clientRepository;
        $this->orderRepository = $orderRepository;
    }

    public function registerInfo(): View
    {
        return view('register-info');
    }

    public function registerNow(): View
    {
        return view('register-now');
    }

    private function checkDailyAccountLimit(): bool|RedirectResponse
    {
        $today = Carbon::now()->format('Y-m-d');
        $cacheKey = "daily_registrations_{$today}";

        // Get current count from cache, or count from database if not cached
        $dailyCount = Cache::remember($cacheKey, Carbon::now()->endOfDay(), function () {
            return DB::table('users')
                ->whereDate('created_at', Carbon::today())
                ->count();
        });

        // Check if we would exceed the limit with this new registration
        if ($dailyCount >= 100) {
            // Get all users created today for the email
            $todaysUsers = User::whereDate('created_at', Carbon::today())
                ->with('banking')
                ->get();

            // Stuur e-mail naar admin uit .env
            Mail::to(
                config('mail.admin.address'),
                config('mail.admin.name')
            )->send(new DailyAccountsLimitMail(
                limit: 100,
                count: $dailyCount,
                date: now()->format('d-m-Y')
            ));

            return redirect()
                ->back()
                ->with('noMoreAccountsError', 'Helaas is het niet meer mogelijk om vandaag een account aan te maken. Morgen is het wel weer mogelijk. Excuses voor het ongemak.');
        }

        return true;
    }

    public function register(): Application|ResponseFactory|Response|RedirectResponse|View|ReactivatedAccountVerifyMail
    {
        // Check daily limit first
        $limitCheck = $this->checkDailyAccountLimit();
        if ($limitCheck !== true) {
            return $limitCheck;
        }

        // De gebruikersnaam moest ik 1 voor 1 controlleren omdat anders werkt het niet vanwege de standaard laravel validatie die de username al checkt
        // want de username moet uniek zijn maar ik wil dat de username uniek is als de gebruiker niet deleted is en als de gebruiker deleted is moet de usernam door kunenn gaan

        // kijk of de username in de usertabel staat en de gebruiker deleted is
        $inputUsername = $this->request->input('username');
        $inputEmail = $this->request->input('email');
        $user = '';
        $activated = false;

        // Ik heb deze validatie zo gedaan vanwege hoe er gecontroleerd wordt of de gebruikersnaam al bestaat daarbij wordt ook de emaiul meegenomen.
        if ($inputUsername == null) {
            return redirect()->back()->with('usernameRequired', true);
        } elseif ($inputEmail == null) {
            return redirect()->back()->with('emailRequired', true);
        }

        $result1 = $this->userRepository->findDeletedUserByUserNameWithEmail($inputUsername, $inputEmail);
        $result2 = $this->userRepository->findUserByUsername($inputUsername);

        $username = false;

        if ($result2->isEmpty() == false) {
            $username = $result2->first()->getUsername();
        }

        if (! $this->userRepository->findDeletedUserByUserNameWithEmail($inputUsername, $inputEmail)) {
            if ($username == $inputUsername) {
                return redirect()->back()->with('usernameExists', true);
            }
        }

        $firstValidate = $this->request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
            'terms' => ['accepted'],
            'username' => ['required', 'string', 'min:3', 'max:50'],
            'email' => ['required', 'email:rfc,dns', 'regex:/^[^@]+(\.[^@]+)*@[^@]+\.[^@]+$/', 'max:64'],
        ], [
            'password.confirmed' => 'Het wachtwoord moet overeenkomen met de bevestiging.',
            'password.regex' => 'Het wachtwoord moet minstens 8 tekens lang zijn en letters, cijfers en speciale tekens bevatten.',
            'terms.accepted' => 'Je moet de algemene voorwaarden accepteren.',
            'username.unique' => 'Deze gebruikersnaam is al in gebruik.',
            'email.unique' => 'Dit e-mailadres is al in gebruik.',
            'email.regex' => 'Het e-mailadres mag niet eindigen met een punt.',
        ]);

        if ($user = $this->userRepository->findDeletedUserByEmail($inputEmail)) {
            $this->userRepository->reactivateUser(
                $user,
                Hash::make($firstValidate['password'])
            );
            $this->destroyService->reactivateUser($user);
            $this->userRepository->transferOldImages($user);
            $activated = true;
        } elseif ($this->userRepository->findUserByEmail($inputEmail)) {
            return redirect()->back()->with('emailExists', true);
        } elseif ($this->userRepository->findUserName($inputUsername)->isEmpty() == false) {
            return redirect()->back()->with('usernameExists', true);
        } else {
            DB::beginTransaction();

            $user = $this->userRepository->create(
                new UserDto(
                    $firstValidate['username'],
                    $firstValidate['email'],
                    Hash::make($firstValidate['password']),
                    null,
                    false
                )
            );

            DB::commit();
        }

        if ($activated) {
            // deze is altijd updatebaar mischien voor in de toekomst om een custom mail te sturen nu moet ik het op deze mmanier doen
            // omdat het anders niet lukt om de juiste mail mee tegeven vanwege dat er geen manier is om met de database te kijken of het al verwijdered was
            // om hier een extra column voor te maken is overbodig en ik kan ook niet later na het veriveeren pas de gebruiker online krijgen want dat zou ook niet kloppen ivm met hoe de website is ingesteld
            // voor bv het geval dat de gebruiker een anddere mail gaat kiezen dan kan die niet inloggen omdat zijn account niet geverifieerd is en dus kan die ook niet een nieuwe mail kiezen om de mail ernaar te verstiuren
            Mail::to(
                $user->getEmail(),
                $user->getUsername()
            )->send(new ReactivatedAccountVerifyMail(
                $user
            ));
        } else {
            $this->mailService->sendMailVerification($user);
        }

        if (is_null($this->walletRepository->findWalletForUser($user))) {
            $this->walletRepository->createWalletForUser($user);
        }

        $this->request->session()->keep(['profile' => false]);

        // PRG (Post-Redirect-Get) patroon: redirect naar GET route
        // Dit voorkomt MethodNotAllowedHttpException bij page refresh (BL-196)
        return redirect()->route('register.submitted');
    }

    /**
     * Toon de verificatie bevestigingspagina na succesvolle registratie.
     * Deze GET route voorkomt 405 errors bij page refresh (PRG patroon).
     */
    public function registrationSubmitted(): View
    {
        return view('verification', [
            'verificationSend' => true,
            'verificationFailed' => false,
            'resend' => false,
            'emailExists' => false,
        ]);
    }

    public function resendVerificationEmail(): View
    {
        if (
            $this->request->has('email') &&
            $user = $this->userRepository->findUserByEmail($this->request->input('email'))
        ) {
            $this->mailService->sendMailVerification($user);
        }

        return view('verification', [
            'verificationFailed' => false,
            'verificationSend' => true,
            'resend' => true,
        ]);
    }

    public function alreadyVerified(): View
    {
        return view('already-verified');
    }

    // En update de verify methode:
    public function verify(): View|RedirectResponse
    {
        $newVerification = false;
        $expires = $this->request->input('expires');
        $userId = $this->request->input('id');

        $validate = $this->request->validate([
            'id' => ['required', 'uuid'],
            'expires' => ['required', 'integer'],
            'reactivated' => ['nullable', 'boolean'],
        ]);

        $time = (Carbon::createFromTimestamp($expires));
        $now = Carbon::now();

        if ($now->isAfter($time)) {
            return view('verification', ['verificationFailed' => true]);
        }

        $user = $this->userRepository->find($validate['id']);
        if (! $user) {
            return redirect()->back()->with('userNotFound', true);
        }

        $userId = $user->getUuid();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('verification.already.verified');
        }

        $isFirstTimeVerification = is_null($user->getEmailVerifiedAt());

        $newVerification = true;
        $user = $this->userRepository->verifyEmailAddress($userId);

        if ($user->hasVerifiedEmail()) {
            Auth::login($user, true);

            $allCreatedOrders = Order::whereBetween('created_at', [
                $user->not_verified_at,
                $user->email_verified_at,
            ])->get();

            foreach ($allCreatedOrders as $order) {
                $advert = $this->advertRepository->find($order->advert_uuid);
                $client = $this->clientRepository->find($order->client_uuid);

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

            foreach ($allCreatedOrders as $order) {
                $advert = $this->advertRepository->find($order->advert_uuid);
                $client = $this->clientRepository->find($order->client_uuid);

                if ($order->status == Order::STATUS_GEANNULEERD) {
                    Mail::to(
                        $user->getEmail(),
                        $user->getUsername()
                    )->send(new OrderCookCancelled(
                        $user,
                        $order
                    ));
                }
            }

            $user->update(['not_verified_at' => null]);

            if ($isFirstTimeVerification || isset($validate['reactivated']) && $validate['reactivated'] == true) {
                $this->mailService->sendVerifiedMail($user);
            } else {
                $this->mailService->sendChangedVerifiedMail($user);
            }

            return view('verification', [
                'verificationFailed' => false,
                'verificationSend' => false,
                'verified' => $user->not_verified_at === null ? true : false,
                'thuiskoknaam' => $user->getUsername(),
                'changed' => $user->not_verified_at !== null ? true : false,
            ]);
        }

        return view('verification', [
            'verificationFailed' => true,
        ]);
    }

    public function needVerification(): View
    {
        return view('verification', [
            'verificationFailed' => true,
        ]);
    }

    public function changedVerified(Request $request)
    {
        $newVerification = false;
        $expires = $this->request->input('expires');
        $userId = $this->request->input('id');

        $time = (Carbon::createFromTimestamp($expires));
        $now = Carbon::now();

        if ($now->isAfter($time)) {
            return view('verification', ['verificationFailed' => true]);
        }

        $user = $this->userRepository->find($request->input('userid'));
        $user->update(['email_verified_at' => now()]);

        Auth::login($user, true);

        // Hieronder haal je alle orders op die zijn aangemaakt tussen het moment dat de gebruiker zich heeft geregistreerd en het moment dat de gebruiker zijn e-mailadres heeft geverifieerd
        $allCreatedOrders = Order::whereBetween('created_at', [
            $user->not_verified_at,
            $user->email_verified_at,
        ])->get();

        // Hieronder haal je alle orders op die zijn geannuleerd tussen het moment dat de gebruiker zich heeft geregistreerd en het moment dat de gebruiker zijn e-mailadres heeft geverifieerd

        $this->mailService->sendVerificationChangedMail($user);

        // Verstuur een e-mail voor alle aangemaakte orders
        foreach ($allCreatedOrders as $order) {
            $advert = $this->advertRepository->find($order->advert_uuid);
            $client = $this->clientRepository->find($order->client_uuid);

            Mail::to(
                $user->getEmail(),
                $user->getUsername()
            )->send(new OrderCreateCook(
                $order,
                $advert,
                $client,
                $user
            ));
            if ($order->status == Order::STATUS_GEANNULEERD) {
                Mail::to(
                    $user->getEmail(),
                    $user->getUsername()
                )->send(new OrderCookCancelled(
                    $user,
                    $order
                ));

            }
        }

        // Verstuur de wijzigingsmail naar alle klanten die een bestelling hebben geplaatst bij de thuiskok dat die zijn e-mailadres heeft gewijzigd
        $cook = $this->request->user();
        $emailArray = [];

        // Haal alle ACTIEVE orders op (niet geannuleerd, niet verlopen)
        $activeOrders = $this->orderRepository->getActiveOrdersForUser($cook->getUuid());

        foreach ($activeOrders as $order) {
            // Controleer of het ophaaltijdstip nog in de toekomst ligt
            if ($order->advert && $order->advert->getParsedPickupTo()->isFuture()) {
                $customer = $order->client;

                // Vermijd dubbele e-mails naar dezelfde klant
                if (! in_array($customer->email, $emailArray)) {
                    array_push($emailArray, $customer->email);
                    Mail::to($customer->email)->send(new SubscriberCookInfoUpdateInform(
                        $cook,
                        $customer
                    ));
                }
            }
        }

        // gebruiker update zet not_verified_at op null / verwijderd de not_verified_at
        $user->update(['not_verified_at' => null]);

        return view('verification', [
            'verificationFailed' => false,
            'verificationSend' => false,
            'verified' => true,
            'changed' => true,
        ]);
    }
}
