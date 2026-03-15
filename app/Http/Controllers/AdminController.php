<?php

namespace App\Http\Controllers;

use App\Dtos\DishDto;
use App\Dtos\ImageDto;
use App\Http\Requests\RevealSensitiveDataRequest;
use App\Http\Requests\UpdateKvkDetailsRequest;
use App\Mail\Dac7RequiredMail;
use App\Mail\Dac7WarningMail;
use App\Mail\PaymentMail;
use App\Models\Banking;
use App\Models\Dac7Establishment;
use App\Models\Dac7Information;
use App\Models\Image;
use App\Models\Order;
use App\Models\Payment;
use App\Models\SensitiveDataAccessLog;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletLine;
use App\Models\WebsiteStatus;
use App\Repositories\AdvertRepository;
use App\Repositories\BankingRepository;
use App\Repositories\DishRepository;
use App\Repositories\ImageRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ReviewRepository;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use App\Services\Dac7Service;
use App\Services\DestroyService;
use App\Support\SensitiveDataMasker;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AdminController extends Controller
{
    private Request $request;

    private UserRepository $userRepository;

    private DishRepository $dishRepository;

    private AdvertRepository $advertRepository;

    private ReviewRepository $reviewRepository;

    private OrderRepository $orderRepository;

    private ImageRepository $imageRepository;

    private BankingRepository $bankingRepository;

    private WalletRepository $walletRepository;

    private Dac7Service $dac7Service;

    private DestroyService $destroyService;

    public function __construct(
        Request $request,
        UserRepository $userRepository,
        DishRepository $dishRepository,
        AdvertRepository $advertRepository,
        ReviewRepository $reviewRepository,
        OrderRepository $orderRepository,
        ImageRepository $imageRepository,
        BankingRepository $bankingRepository,
        WalletRepository $walletRepository,
        Dac7Service $dac7Service,
        DestroyService $destroyService
    ) {
        $this->request = $request;
        $this->userRepository = $userRepository;
        $this->dishRepository = $dishRepository;
        $this->advertRepository = $advertRepository;
        $this->reviewRepository = $reviewRepository;
        $this->orderRepository = $orderRepository;
        $this->imageRepository = $imageRepository;
        $this->bankingRepository = $bankingRepository;
        $this->walletRepository = $walletRepository;
        $this->dac7Service = $dac7Service;
        $this->destroyService = $destroyService;
    }

    public function getAccounts(): View
    {
        $searchTerm = $this->request->query('name');
        $users = $this->userRepository->getAllUsersForAdmin($searchTerm);

        // Enhance users with IBAN verification status
        foreach ($users as $user) {
            $banking = $user->banking;
            // Check if banking exists and has been validated
            $user->iban_verified = $banking && $banking->isValidated();
        }

        return view('dashboard.admin.accounts', [
            'users' => $users,
            'search' => $searchTerm,
        ]);
    }

    public function showSingleAccount(string $uuid): View
    {
        $user = $this->userRepository->findWithTrashed($uuid);

        if (! $user) {
            abort(404); // or handle the case where the user is not found
        }

        return view('dashboard.admin.single-account', [
            'user' => $user,
        ]);
    }

    public function adminLogin()
    {
        // If already logged in as admin, redirect to admin dashboard
        if (Auth::check() && Auth::user()->hasRole('admin')) {
            return redirect()->route('dashboard.admin.accounts');
        }

        return view('login', [
            'isAdminLogin' => true,
            'acceptedCookies' => request()->cookie('accepted_cookies', false),
            'essentialCookies' => request()->cookie('essential_cookies', false),
        ]);
    }

    public function handleAdminLogin(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email:rfc,dns'],
            'password' => ['required'],
        ]);

        $user = $this->userRepository->findUserByEmail($validated['email']);

        // Check if user exists and has admin role
        if (! $user || ! $user->hasRole('admin') ||
            ! Hash::check($validated['password'], $user->getAuthPassword())) {
            return back()->withErrors(['email' => 'Invalid credentials or insufficient permissions.']);
        }

        Auth::login($user, true);

        return redirect()->route('dashboard.admin.accounts');
    }

    public function loginAsUser(string $uuid): RedirectResponse
    {
        $user = $this->userRepository->find($uuid);

        if (is_null($user)) {
            throw new ModelNotFoundException;
        }

        Auth::loginUsingId($user->getUuid());

        return redirect()->route('dashboard.adverts.active.home');
    }

    public function getUsersDac7(Request $request)
    {
        $query = $request->input('query', '');

        $users = User::when($query, function ($q) use ($query) {
            return $q->where('username', 'LIKE', "%{$query}%");
        })
            ->with([
                'wallet',
                'wallet.walletLines',
                'dac7Information',
                'orders',
                'userProfile',
                'cook',
                'banking',
                'dac7Establishment',
            ])
            ->get();

        $usersWithDac7 = [];
        foreach ($users as $user) {
            $dac7Status = $this->dac7Service->getDac7Status($user);

            // FORCE: Zorg ervoor dat dac7Information record bestaat
            $dac7Info = Dac7Information::firstOrCreate(['user_id' => $user->uuid], [
                'information_provided' => false,
            ]);

            // FORCE: Update datum als grens is overschreden en datum nog leeg is
            if ($dac7Status['dac7_exceeded'] && ! $dac7Info->dac7_threshold_reached_at) {
                $dac7Info->update(['dac7_threshold_reached_at' => now()]);
            }

            // Na deze regel:
            $link = $this->dac7Service->generateDac7Link($user).'?source=admin';

            $dac7Info->update(['dac7_form_link' => $link]);

            $address = [];
            if ($user->cook) {
                $addressString = $user->cook->getStreet();

                if ($user->cook->getHouseNumber()) {
                    $addressString .= ' '.$user->cook->getHouseNumber();

                    if ($user->cook->getAddition()) {
                        $addressString .= ' '.$user->cook->getAddition();
                    }
                }

                $address = [
                    'street' => $addressString,
                    'housenumber' => '',
                    'postal' => $user->cook->getPostalCode() ?: '',
                    'place' => $user->cook->getCity() ?: '',
                    'country' => $user->cook->getCountry() ?: '',
                ];
            } else {
                $address = [
                    'street' => '',
                    'housenumber' => '',
                    'postal' => '',
                    'place' => '',
                    'country' => '',
                ];
            }

            $hasEstablishment = $user->dac7Establishment ? $user->dac7Establishment->has_establishment : false;
            $isResidentialAddress = $user->dac7Establishment ? $user->dac7Establishment->is_residential_address : null;

            $establishment = [
                'country' => $hasEstablishment && $user->dac7Establishment && $user->dac7Establishment->country ? $user->dac7Establishment->country : '',
                'postal_code' => $hasEstablishment && $user->dac7Establishment && $user->dac7Establishment->postal_code ? $user->dac7Establishment->postal_code : '',
                'street' => $hasEstablishment && $user->dac7Establishment && $user->dac7Establishment->street ? $user->dac7Establishment->street : '',
                'house_number' => $hasEstablishment && $user->dac7Establishment && $user->dac7Establishment->house_number ? $user->dac7Establishment->house_number : '',
            ];

            // Haal bijgewerkte data op
            $dac7Info->refresh();

            $dac7ThresholdDate = $dac7Info->dac7_threshold_reached_at
                ? \Carbon\Carbon::parse($dac7Info->dac7_threshold_reached_at)->format('d-m-Y')
                : null;

            $dac7FormLink = $dac7Info->dac7_form_link;

            $usersWithDac7[] = [
                'username' => $user->username,
                'uuid' => $user->uuid,
                'roles' => $user->getRoleNames()->implode(', '),
                'order_count' => $dac7Status['order_count'],
                'total_revenue' => $dac7Status['total_revenue'],
                'dac7_exceeded' => $dac7Status['dac7_exceeded'],
                'dac7_threshold_date' => $dac7ThresholdDate,
                'dac7_form_link' => $dac7FormLink,
                'dac7_information_provided' => $dac7Status['dac7_information_provided'],

                'type_thuiskok' => $user->type_thuiskok ?? 'Particulier',
                'kvk_naam' => $user->kvk_naam ?: '',
                'btw_nummer' => $user->btw_nummer ?: '',
                'kvk_nummer' => $user->kvk_nummer ?: '',
                'rsin' => $user->rsin ?: '',
                'vestigingsnummer' => $user->vestigingsnummer ?: '',
                'nvwa_nummer' => $user->nvwa_nummer ?: '',

                'firstname' => $user->userProfile->firstname ?? '',
                'lastname' => $user->userProfile->lastname ?? '',
                'insertion' => $user->userProfile->insertion ?? '',
                'birthday' => $user->userProfile && $user->userProfile->birthday ? $user->userProfile->birthday->format('d-m-Y') : '',

                'address' => $address,
                'is_residential_address' => $isResidentialAddress,

                'iban' => SensitiveDataMasker::mask($user->banking->iban ?? ''),
                'bsn' => SensitiveDataMasker::mask($user->bsn ?? ''),

                'id_front' => $user->banking && $user->banking->id_front ? true : false,
                'id_back' => $user->banking && $user->banking->id_back ? true : false,

                'has_establishment' => $hasEstablishment,
                'establishment' => $establishment,
            ];
        }

        return view('dashboard.admin.dashboard.dac7', [
            'usersWithDac7' => $usersWithDac7,
            'query' => $query,
            'title' => 'Controle DAC7',
        ]);
    }

    public function resetDac7Information($uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        // Verwijder BSN van user
        $user->update(['bsn' => null]);

        // Verwijder ID documenten van banking
        if ($user->banking) {
            $user->banking->update([
                'id_front' => null,
                'id_back' => null,
                'bsn' => null,
            ]);
        }

        // Reset DAC7 information status
        if ($user->dac7Information) {
            $user->dac7Information->update([
                'information_provided' => false,
            ]);
        }

        // Reset establishment information
        if ($user->dac7Establishment) {
            $user->dac7Establishment->update([
                'has_establishment' => false,
                'is_residential_address' => null,
                'country' => null,
                'postal_code' => null,
                'street' => null,
                'house_number' => null,
            ]);
        }

        // Reset business information for zakelijke thuiskok
        if ($user->type_thuiskok === 'Zakelijke Thuiskok') {
            $user->update([
                'kvk_naam' => null,
                'btw_nummer' => null,
                'kvk_nummer' => null,
                'rsin' => null,
                'vestigingsnummer' => null,
            ]);
        }

        // Sla reset status op in session
        session(["dac7_reset_user_{$uuid}" => true]);

        // Clear any existing session data voor deze user
        if (session('dac7_user_uuid') === $uuid) {
            session()->forget(['dac7_redirect_url', 'dac7_user_uuid', 'dac7_info_exists', 'dac7_timestamp']);
        }

        return redirect()->back()->with('message', 'DAC7 informatie gereset. Gebruiker kan nu opnieuw gegevens aanleveren.');
    }

    public function downloadDac7Id(string $uuid, string $type)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();
        $banking = $user->banking;

        if (! $banking) {
            return redirect()->back()->with('error', 'Geen bankgegevens gevonden voor deze gebruiker.');
        }

        $field = $type === 'front' ? 'id_front' : 'id_back';
        $path = $banking->$field;

        if (! $path) {
            return redirect()->back()->with('error', 'Geen identificatiedocument gevonden.');
        }

        $fullPath = storage_path('app/'.$path);
        if (! file_exists($fullPath)) {
            return redirect()->back()->with('error', 'Bestand bestaat niet op de opgegeven locatie.');
        }

        return response()->download($fullPath);
    }

    /**
     * Show the DAC7 information form
     */
    public function showDac7Form(string $uuid, string $token)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        if (hash('sha256', $user->email.$user->uuid) !== $token) {
            return redirect()->route('login.home')->with('error', 'Ongeldige token. Log in om verder te gaan.');
        }

        // Check of er een reset is gebeurd voor deze user
        $userResetAllowed = session("dac7_reset_user_{$uuid}", false);

        // Check of formulier al is ingevuld
        $formAlreadySubmitted = $user->bsn && $user->banking && $user->banking->id_front && $user->banking->id_back;

        session([
            'dac7_redirect_url' => url()->current(),
            'dac7_user_uuid' => $uuid,
            'dac7_timestamp' => now()->timestamp,
            'dac7_info_exists' => $formAlreadySubmitted && ! $userResetAllowed,
        ]);

        // NIEUWE LOGICA: Als er een reset is toegestaan, toon altijd het formulier
        if ($userResetAllowed) {
            // Vergeet de reset session na gebruik
            session()->forget("dac7_reset_user_{$uuid}");

            if (! Auth::check()) {
                return redirect()->route('login.home', ['from_dac7' => 1]);
            }

            if (Auth::user()->uuid !== $uuid) {
                Auth::logout();

                return redirect()->route('login.home', ['from_dac7' => 1])
                    ->with('error', 'De gebruikte DAC7 link is niet bestemd voor dit account.');
            }

            return view('dac7.form', [
                'user' => $user,
                'token' => $token,
            ]);
        }

        // Als formulier al is ingevuld EN geen reset toegestaan
        if ($formAlreadySubmitted) {
            if (! Auth::check()) {
                return redirect()->route('login.home', ['from_dac7' => 1]);
            }

            if (Auth::user()->uuid !== $uuid) {
                Auth::logout();

                return redirect()->route('login.home', ['from_dac7' => 1])
                    ->with('error', 'De gebruikte DAC7 link is niet bestemd voor dit account.');
            }

            return redirect()->route('dac7.success');
        }

        // Formulier nog niet ingevuld, toon het formulier
        if (! Auth::check()) {
            return redirect()->route('login.home', ['from_dac7' => 1]);
        }

        if (Auth::user()->uuid !== $uuid) {
            Auth::logout();

            return redirect()->route('login.home', ['from_dac7' => 1])
                ->with('error', 'De gebruikte DAC7 link is niet bestemd voor dit account.');
        }

        return view('dac7.form', [
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Process the DAC7 information form submission
     */
    public function submitDac7Form(Request $request, string $uuid, string $token)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        if (hash('sha256', $user->email.$user->uuid) !== $token) {
            return redirect()->route('login.home')->with('error', 'Ongeldige token. Log in om verder te gaan.');
        }

        $dac7Info = Dac7Information::where('user_id', $uuid)->first();

        if (! Auth::check()) {
            session(['dac7_redirect_url' => url()->current()]);
            session(['dac7_user_uuid' => $uuid]);

            return redirect()->route('login.home');
        }

        if (Auth::user()->uuid !== $uuid) {
            Auth::logout();
            session()->forget('dac7_redirect_url');
            session()->forget('dac7_user_uuid');

            return redirect()->route('login.home')
                ->with('error', 'De DAC7 link is niet bestemd voor dit account.');
        }

        if ($user->type_thuiskok === 'Zakelijke Thuiskok') {
            $validator = $this->validateBusinessForm($request);
        } else {
            $validator = $this->validatePersonalForm($request);
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $this->storeDac7Information($request, $user);

        $correctFormLink = $this->dac7Service->generateDac7Link($user);

        if ($dac7Info) {
            $dac7Info->update([
                'dac7_form_link' => $correctFormLink,
            ]);
        } else {
            $dac7Info = new Dac7Information;
            $dac7Info->user_id = $uuid;
            $dac7Info->information_provided = false;
            $dac7Info->dac7_form_link = $correctFormLink;
            $dac7Info->save();
        }

        $dac7Status = $this->dac7Service->getDac7Status($user);
        $this->dac7Service->updateDac7ThresholdDate($user, $dac7Status['dac7_exceeded']);

        // BELANGRIJK: Session cleanup na succesvolle submit
        session()->forget("dac7_reset_user_{$uuid}");

        return redirect()->route('dac7.success', ['fresh' => true]);
    }

    /**
     * Show the success page
     */
    public function showDac7Success(Request $request)
    {
        if (! Auth::check()) {
            return redirect()->route('login.home');
        }

        $dac7Info = Dac7Information::where('user_id', Auth::user()->uuid)->first();

        if (! $dac7Info) {
            return redirect()->route('dashboard.adverts.active.home')
                ->with('error', 'De gebruikte DAC7 link is niet bestemd voor dit account.');
        }

        $freshSubmission = $request->has('fresh');

        return view('dac7.success', [
            'freshSubmission' => $freshSubmission,
        ]);
    }

    /**
     * Validate personal form fields
     */
    private function validatePersonalForm(Request $request)
    {
        $rules = [
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'birthday' => 'required|date|before:-18 years',
            'account_number' => 'required|string|max:34',
            'bsn' => 'required|string|min:8|max:9',
            'id_front' => 'required|file|mimes:pdf,png,jpeg,jpg,JPG|max:10240',
            'id_back' => 'required|file|mimes:pdf,png,jpeg,jpg,JPG|max:10240',
            'has_establishment' => 'required|boolean',
            'is_residential_address' => 'required|boolean', // NEW: Added residential address validation
        ];

        // Als er een vaste inrichting is, valideer de adresgegevens
        if ($request->boolean('has_establishment')) {
            $rules['establishment_country'] = 'required|string|max:50';
            $rules['establishment_postal_code'] = 'required|string|max:20';
            $rules['establishment_street'] = 'required|string|max:100';
            $rules['establishment_house_number'] = 'required|string|max:20';
        }

        return Validator::make($request->all(), $rules, [
            'id_front.mimes' => 'Enkel een paspoort, ID-kaart of Rijbewijs is toegestaan in PDF, PNG, JPEG of JPG formaat.',
            'id_back.mimes' => 'Enkel een paspoort, ID-kaart of Rijbewijs is toegestaan in PDF, PNG, JPEG of JPG formaat.',
            'is_residential_address.required' => 'Geef aan of dit je woonadres is.',
        ]);
    }

    /**
     * Validate business form fields
     */
    private function validateBusinessForm(Request $request)
    {
        $rules = [
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'birthday' => 'required|date|before:-18 years',
            'account_number' => 'required|string|max:34',
            'bsn' => 'required|string|min:8|max:9',
            'id_front' => 'required|file|mimes:pdf,png,jpeg,jpg,JPG|max:10240',
            'id_back' => 'required|file|mimes:pdf,png,jpeg,jpg,JPG|max:10240',
            'has_establishment' => 'required|boolean',
            'is_residential_address' => 'required|boolean', // NEW: Added residential address validation
            'kvk_naam' => 'required|string|max:100',
            'btw_nummer' => 'required|string|max:20',
            'kvk_nummer' => 'required|string|max:20',
            'rsin' => 'required|string|max:20',
            'vestigingsnummer' => 'required|string|max:20',
        ];

        // Als er een vaste inrichting is, valideer de adresgegevens
        if ($request->boolean('has_establishment')) {
            $rules['establishment_country'] = 'required|string|max:50';
            $rules['establishment_postal_code'] = 'required|string|max:20';
            $rules['establishment_street'] = 'required|string|max:100';
            $rules['establishment_house_number'] = 'required|string|max:20';
        }

        return Validator::make($request->all(), $rules, [
            'id_front.mimes' => 'Enkel een paspoort, ID-kaart of Rijbewijs is toegestaan in PDF, PNG, JPEG of JPG formaat.',
            'id_back.mimes' => 'Enkel een paspoort, ID-kaart of Rijbewijs is toegestaan in PDF, PNG, JPEG of JPG formaat.',
            'is_residential_address.required' => 'Geef aan of dit je woonadres is.',
        ]);
    }

    /**
     * Store the submitted information
     */
    private function storeDac7Information(Request $request, User $user)
    {
        // Update BSN in both User and Banking model to ensure compatibility
        $user->update([
            'bsn' => $request->input('bsn'),
        ]);

        // Ensure banking record exists
        $banking = Banking::firstOrNew(['user_uuid' => $user->uuid]);
        $banking->save();

        // Update BSN in Banking model if the column exists
        try {
            $banking->update([
                'bsn' => $request->input('bsn'),
            ]);
        } catch (\Exception $e) {
            // Handle silently if bsn column doesn't exist in banking table
        }

        // Save ID documents
        if ($request->hasFile('id_front')) {
            $frontPath = $request->file('id_front')->store('id_documents/'.$user->uuid);
            $banking->update(['id_front' => $frontPath]);
        }

        if ($request->hasFile('id_back')) {
            $backPath = $request->file('id_back')->store('id_documents/'.$user->uuid);
            $banking->update(['id_back' => $backPath]);
        }

        // Update user profile information if it exists
        if ($user->userProfile) {
            $user->userProfile->update([
                'firstname' => $request->input('firstname'),
                'lastname' => $request->input('lastname'),
                'birthday' => $request->input('birthday'),
            ]);
        }

        // Update banking information
        $banking->update([
            'iban' => $request->input('account_number'),
        ]);

        // Update business information for business cooks
        if ($user->type_thuiskok === 'Zakelijke Thuiskok') {
            $user->update([
                'kvk_naam' => $request->input('kvk_naam'),
                'btw_nummer' => $request->input('btw_nummer'),
                'kvk_nummer' => $request->input('kvk_nummer'),
                'rsin' => $request->input('rsin'),
                'vestigingsnummer' => $request->input('vestigingsnummer'),
            ]);
        }

        // Update or create establishment information
        $establishment = Dac7Establishment::firstOrNew(['user_id' => $user->uuid]);
        $establishment->has_establishment = $request->boolean('has_establishment');
        $establishment->is_residential_address = $request->boolean('is_residential_address'); // NEW: Save residential address status

        if ($establishment->has_establishment) {
            $establishment->country = $request->input('establishment_country');
            $establishment->postal_code = $request->input('establishment_postal_code');
            $establishment->street = $request->input('establishment_street');
            $establishment->house_number = $request->input('establishment_house_number');
        } else {
            $establishment->country = null;
            $establishment->postal_code = null;
            $establishment->street = null;
            $establishment->house_number = null;
        }

        $establishment->save();
    }

    public function updateDac7Information(Request $request, $uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        // Find or create the dac7Information record
        $dac7Info = Dac7Information::firstOrNew(['user_id' => $uuid]);
        $dac7Info->information_provided = $request->boolean('information_provided');
        $dac7Info->save();

        return redirect()->back()->with('message', 'DAC7 status bijgewerkt');
    }

    public function blockUser(string $uuid): RedirectResponse
    {
        $user = $this->userRepository->find($uuid);

        if (is_null($user)) {
            throw new ModelNotFoundException;
        }

        $this->userRepository->blockUser($user);

        return redirect()->route('dashboard.admin.accounts.single', $user->getUuid());
    }

    public function unblockUser(string $uuid): RedirectResponse
    {
        $user = $this->userRepository->find($uuid);

        if (is_null($user)) {
            throw new ModelNotFoundException;
        }

        $this->userRepository->deblockUser($user);

        return redirect()->route('dashboard.admin.accounts.single', $user->getUuid());
    }

    public function deleteUser(string $uuid): RedirectResponse
    {
        $user = $this->userRepository->find($uuid);

        if (is_null($user)) {
            throw new ModelNotFoundException;
        }

        $this->userRepository->delete($user->getUuid());

        return redirect()->route('dashboard.admin.accounts.single', $user->getUuid());
    }

    /**
     * Herstel een verwijderd account inclusief alle gerelateerde data.
     */
    public function restoreUser(string $uuid): RedirectResponse
    {
        $user = $this->userRepository->findWithTrashed($uuid);

        if (is_null($user)) {
            throw new ModelNotFoundException;
        }

        if (is_null($user->getDeletedAt())) {
            return redirect()->route('dashboard.admin.accounts.single', $user->getUuid());
        }

        $user->restore();
        $this->destroyService->reactivateUser($user);

        return redirect()->route('dashboard.admin.accounts.single', $user->getUuid())
            ->with('message', 'Account is succesvol hersteld.');
    }

    /**
     * Update KVK gegevens voor een gebruiker via admin dashboard.
     * Als KVK gegevens worden ingevuld, wordt type_thuiskok automatisch
     * gewijzigd naar 'Zakelijke Thuiskok'.
     */
    public function updateKvkDetails(UpdateKvkDetailsRequest $request, string $uuid): RedirectResponse
    {
        $user = $this->userRepository->find($uuid);

        if (is_null($user)) {
            throw new ModelNotFoundException;
        }

        $validated = $request->validated();

        $hasKvkData = ! empty($validated['kvk_naam']) || ! empty($validated['kvk_nummer']);

        $updateData = [
            'kvk_naam' => $validated['kvk_naam'] ?? null,
            'kvk_nummer' => $validated['kvk_nummer'] ?? null,
            'btw_nummer' => $validated['btw_nummer'] ?? null,
            'rsin' => $validated['rsin'] ?? null,
            'vestigingsnummer' => $validated['vestigingsnummer'] ?? null,
            'nvwa_nummer' => $validated['nvwa_nummer'] ?? null,
        ];

        if ($hasKvkData) {
            $updateData['type_thuiskok'] = 'Zakelijke Thuiskok';
        }

        $user->update($updateData);

        return redirect()
            ->route('dashboard.admin.accounts.single', $user->getUuid())
            ->with('message', 'KVK gegevens zijn succesvol bijgewerkt.');
    }

    public function getDishes(): View
    {
        $search = $this->request->query('search');

        return view('dashboard.admin.dishes', [
            'dishes' => $this->dishRepository->get($search),
            'search' => $search,
        ]);
    }

    public function getAdverts(): View
    {
        $search = $this->request->query('search');

        return view('dashboard.admin.adverts', [
            'adverts' => $this->advertRepository->get($search),
            'search' => $search,
        ]);
    }

    public function getReviews(): View
    {
        $search = $this->request->query('search');

        return view('dashboard.admin.reviews', [
            'reviews' => $this->reviewRepository->get($search),
            'search' => $search,
        ]);
    }

    public function showSingleDish(string $uuid): View
    {
        $dish = $this->dishRepository->find($uuid);

        return view('dashboard.dishes.show', ['dish' => $dish]);
    }

    public function editSingleDish(string $uuid): View
    {
        return view('dashboard.dishes.edit', ['dish' => $this->dishRepository->find($uuid), 'edit' => true]);
    }

    public function updateDish(string $uuid): RedirectResponse
    {
        $this->request->validate([
            'title' => 'nullable',
            'description' => ['nullable', 'max:1000'],
            'spicy' => ['nullable'],
            'profileImage' => 'nullable|image|mimes:jpeg,jpg,gif,bmp|min:1|max:20000',
            'vegetarian' => 'nullable',
            'vegan' => 'nullable',
            'halal' => 'nullable',
            'alcohol' => 'nullable',
            'gluten' => 'nullable',
            'lactose' => 'nullable',
        ]);

        $dish = $this->dishRepository->find($uuid);

        if (is_null($dish)) {
            throw new ModelNotFoundException;
        }

        if ($this->request->hasFile('profileImage')) {
            if ($dish->image) {
                $this->imageRepository->delete($dish->image->getUuid());
            }

            /** @var UploadedFile $image */
            $image = $this->request->file('profileImage');
            $fileName = time().'.'.$image->getClientOriginalExtension();

            $moved = $image->move('img/dishes/'.$dish->cook->getUuid(), $fileName);

            $this->imageRepository->create(
                new ImageDto(
                    $dish->user->getUuid(),
                    $moved->getPath(),
                    $fileName,
                    $this->request->input('name') ?? '',
                    $image->getClientMimeType(),
                    $dish,
                    Image::DISH_IMAGE,
                    true
                )
            );
        }

        $this->dishRepository->update(
            new DishDto(
                $this->request->user(),
                $this->request->input('title') ?? $dish->getTitle(),
                $this->request->input('description') ?? $dish->getDescription(),
                $this->request->input('vegetarian'),
                $this->request->input('vegan'),
                $this->request->input('halal'),
                $this->request->input('alcohol'),
                $this->request->input('gluten'),
                $this->request->input('lactose'),
                $this->request->input('spicy'),
                $this->request->user()->cook
            ),
            $uuid
        );

        return redirect()->route('dashboard.admin.dishes');
    }

    public function showSingleAdvert(string $uuid): View
    {
        $advert = $this->advertRepository->find($uuid);

        if (is_null($advert)) {
            throw new ModelNotFoundException;
        }

        // Ophalen van orders voor deze advert
        $orders = $advert->orders()->with('user.userProfile')->get();

        return view('dashboard.adverts.detail', [
            'user' => $advert->cook->user,
            'advert' => $advert,
            'orders' => $orders,
            'past' => false,
        ]);
    }

    public function updateAdvert(string $uuid): RedirectResponse
    {
        $advert = $this->advertRepository->find($uuid);

        if (is_null($advert)) {
            throw new ModelNotFoundException;
        }

        Auth::loginUsingId($advert->cook->getUserUuid());

        return redirect()->route('dashboard.adverts.update', ['uuid' => $uuid]);
    }

    public function showSingleReview(string $reviewUuid): View
    {
        return view('dashboard.admin.single-review', [
            'review' => $this->reviewRepository->find($reviewUuid),
        ]);
    }

    public function deleteReview(string $reviewUuid): RedirectResponse
    {
        $this->reviewRepository->delete($reviewUuid);

        return redirect()->route('dashboard.admin.reviews');
    }

    public function getDashboard(): View
    {
        return view('dashboard.admin.dashboard.index', [
            'created' => $this->userRepository->get()->count(),
            'deleted' => $this->userRepository->findDeletedUsers()->count(),
        ]);
    }

    public function getDashboardDishes(): View
    {
        return view('dashboard.admin.dashboard.dishes', [
            'dishes' => $this->dishRepository->get()->count(),
            'adverts' => $this->advertRepository->get()->count(),
            'advertsOnline' => $this->advertRepository->getActiveAdvertsCount(),
            'orderCount' => $this->orderRepository->get()->sum('portion_amount'),
        ]);
    }

    public function preparePayoutData()
    {
        $unpaidPayouts = $this->bankingRepository->findUnpaidPayoutsForAllUsers();

        // The findUnpaidPayoutsForAllUsers() method in BankingRepository is already set up to include payment_type
        return view('payouts', [
            'users' => $unpaidPayouts,
        ]);
    }

    public function getDashboardOrders(): View
    {
        $orders = $this->orderRepository->get()->load('advert.dish');

        $amount = 0.00;

        /** @var Order $order */
        foreach ($orders as $order) {
            $amount += $order->advert->dish->getPortionPrice() * $order->getPortionAmount();
        }

        return view('dashboard.admin.dashboard.orders', [
            'orderCount' => $orders->count(),
            'orderTotal' => $amount,
        ]);
    }

    public function getDashboardRevenue(): View
    {
        $orders = $this->orderRepository->get()->load('advert.dish');
        $orderCount = $orders->count();
        $revenue = 0.00;

        foreach ($orders as $order) {
            $revenue += $order->advert->dish->getPortionPrice() * $order->getPortionAmount();
        }

        $months = $this->orderRepository->collectByMonth();

        return view('dashboard.admin.dashboard.revenue', [
            'orderCount' => $orderCount,
            'revenue' => $revenue,
            'months' => $months['collection'],
            'highest' => $months['max'],
        ]);
    }

    public function getUsersBanking(Request $request)
    {
        $query = $request->input('query');

        if (empty($query)) {
            $usersWithBanking = $this->userRepository->getUsersWithBankingDetails();
        } else {
            $usersWithBanking = $this->userRepository->getUsersWithBankingDetailsLike($query);
        }

        return view('dashboard.admin.banking', [
            'usersWithBanking' => $usersWithBanking,
            'query' => $query,
        ]);
    }

    public function websiteStatus(): View
    {
        // Check the website status from the database
        $websiteStatus = WebsiteStatus::first();

        // Pass the website status to the view
        return view('dashboard.admin.website-status', ['websiteStatus' => $websiteStatus]);
    }

    public function updateWebsiteStatusOnline(): RedirectResponse
    {
        $websiteStatus = WebsiteStatus::first();

        if (is_null($websiteStatus)) {
            WebsiteStatus::create(['is_online' => true]);
        } else {
            $websiteStatus->update(['is_online' => true]);
        }

        return redirect()->route('dashboard.admin.website.status');
    }

    public function updateWebsiteStatusOffline(): RedirectResponse
    {
        $websiteStatus = WebsiteStatus::first();

        if (is_null($websiteStatus)) {
            WebsiteStatus::create(['is_online' => false]);
        } else {
            $websiteStatus->update(['is_online' => false]);
        }

        return redirect()->route('dashboard.admin.website.status');
    }

    public function getSettings(): View
    {
        $websiteStatus = WebsiteStatus::first();
        // haal de website status op uit de database en geef deze mee aan de view

        return view('dashboard.admin.dashboard.settings', ['websiteStatus' => $websiteStatus]);
    }

    public function getPayouts(): View
    {
        // Haal eerst alle betalingen op
        $payments = Payment::with(['user.userProfile', 'user.banking', 'user.dac7Information', 'user.orders.advert'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function ($payment) {
                return $payment->user !== null;
            });

        $processedPayouts = [];

        foreach ($payments as $payment) {
            $user = $payment->user;

            // Gebruik dezelfde Dac7Service als in getUsersDac7
            $dac7Status = $this->dac7Service->getDac7Status($user);

            $processedPayouts[] = [
                'uuid' => $payment->uuid,
                'user_uuid' => $user->uuid,
                'username' => $user->username,
                'firstname' => $user->userProfile->firstname ?? 'N/A',
                'lastname' => $user->userProfile->lastname ?? 'N/A',
                'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                'payment_date' => $payment->payment_date,
                'amount' => $payment->amount,
                'iban' => SensitiveDataMasker::mask($user->banking->iban ?? 'N/A'),
                'payment_type' => $payment->payment_type ?? 'manual',
                'iban_verified' => $user->banking && $user->banking->isValidated(),

                // DAC7 gegevens rechtstreeks van de service - op EXACT dezelfde manier als in getUsersDac7()
                'order_count' => $dac7Status['order_count'],
                'total_revenue' => $dac7Status['total_revenue'],
                'dac7_exceeded' => $dac7Status['dac7_exceeded'],
                'dac7_information_provided' => $dac7Status['dac7_information_provided'],

            ];
        }

        // Sorteer op aanvraagdatum (nieuwste eerst)
        $processedPayouts = collect($processedPayouts)->sortByDesc('created_at')->values()->all();

        return view('dashboard.admin.dashboard.payouts', [
            'users' => $processedPayouts,
        ]);
    }

    public function revealSensitiveData(RevealSensitiveDataRequest $request): JsonResponse
    {
        $user = User::where('uuid', $request->input('user_uuid'))->firstOrFail();
        $fieldType = $request->input('field_type');

        if ($fieldType === 'iban') {
            $value = $user->banking?->iban ?? '';
        } else {
            $value = $user->bsn ?? '';
        }

        SensitiveDataAccessLog::create([
            'admin_user_uuid' => Auth::user()->uuid,
            'target_user_uuid' => $user->uuid,
            'field_type' => $fieldType,
            'ip_address' => $request->ip(),
        ]);

        return response()->json(['value' => $value]);
    }

    public function getAuditLogs(): View
    {
        $search = $this->request->query('search');

        $logs = SensitiveDataAccessLog::with(['admin', 'targetUser'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('admin', fn ($q) => $q->where('username', 'LIKE', '%'.$search.'%'))
                    ->orWhereHas('targetUser', fn ($q) => $q->where('username', 'LIKE', '%'.$search.'%'));
            })
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('dashboard.admin.audit-logs', [
            'logs' => $logs,
            'search' => $search,
        ]);
    }

    public function aprovePayouts(Request $request)
    {
        $validate = $request->validate([
            'selected_users' => 'required|array',
        ]);

        foreach ($validate['selected_users'] as $paymentUuid) {
            DB::beginTransaction();

            try {
                $payment = $this->bankingRepository->findPaymentByUuid($paymentUuid);

                if ($payment) {
                    // Get user for email notification
                    $user = User::where('uuid', $payment->getUserUuid())->first();
                    $userUuid = $payment->getUserUuid();

                    // Update payment state and set payment date
                    $payment->update([
                        'payment_date' => now(),
                        'state' => Payment::COMPLETED,
                    ]);

                    // Update wallet lines - CRITICAL: ONLY update wallet lines that were initiated for THIS payment
                    // Fetch wallet lines that were marked for payout when this payment was created
                    $wallet = Wallet::where('user_uuid', $userUuid)->first();

                    if ($wallet) {
                        // Update wallet lines to PAID_OUT that were marked for this payout
                        // Use the payment creation timestamp to identify the correct wallet lines
                        WalletLine::where('wallet_uuid', $wallet->uuid)
                            ->where('state', WalletLine::PAYOUT_INITIATED)
                            ->where('updated_at', '<=', $payment->created_at->addMinutes(5)) // 5 min buffer
                            ->update(['state' => WalletLine::PAID_OUT]);

                        // Also properly handle transaction cost lines if any
                        $transactionCostLines = WalletLine::where('wallet_uuid', $wallet->uuid)
                            ->where('state', WalletLine::CANCELLATION_COST_PAID)
                            ->where('updated_at', '<=', $payment->created_at->addMinutes(5))
                            ->get();

                        foreach ($transactionCostLines as $line) {
                            $line->update(['state' => WalletLine::CANCELLATION_COST_PAID_OUT]);
                        }

                        // Recalculate available balance to ensure accuracy
                        $this->walletRepository->countAvailableWalletLinesForUser($userUuid);
                    }

                    // Send email to user
                    if ($user) {
                        Mail::to(
                            $user->getEmail(),
                            $user->getUsername()
                        )->send(new PaymentMail($user->getUsername(), $payment->getAmount() * 0.95));
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Failed to approve payout: '.$e->getMessage());
            }
        }

        return redirect()->route('dashboard.admin.payouts')->with('message', 'Betalingen zijn succesvol verwerkt');
    }
}
