<?php

namespace App\Http\Controllers;

use App\Dtos\CommonDto;
use App\Dtos\ImageDto;
use App\Dtos\PrivacyDto;
use App\Mail\SubscriberCookInfoUpdateInform;
use App\Models\Advert;
use App\Models\Cook;
use App\Models\Dish;
use App\Models\Image;
use App\Models\Order;
use App\Models\User;
use App\Repositories\AdvertRepository;
use App\Repositories\CookRepository;
use App\Repositories\DishRepository;
use App\Repositories\ImageRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PrivacyRepository;
use App\Repositories\UserProfileRepository;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use App\Services\DestroyService;
use App\Services\MailService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Spatie\Image\Image as SpatieImage;

class SettingController extends Controller
{
    private Request $request;

    private UserProfileRepository $userProfileRepository;

    private CookRepository $cookRepository;

    private ImageRepository $imageRepository;

    private UserRepository $userRepository;

    private MailService $mailService;

    private PrivacyRepository $privacyRepository;

    private DishRepository $dishRepository;

    private DestroyService $destroyService;

    private AdvertRepository $advertRepository;

    private OrderRepository $orderRepository;

    private WalletRepository $walletRepository;

    public function __construct(
        Request $request,
        UserProfileRepository $userProfileRepository,
        CookRepository $cookRepository,
        ImageRepository $imageRepository,
        UserRepository $userRepository,
        MailService $mailService,
        PrivacyRepository $privacyRepository,
        DishRepository $dishRepository,
        DestroyService $destroyService,
        AdvertRepository $advertRepository,
        OrderRepository $orderRepository,
        WalletRepository $walletRepository

    ) {
        $this->request = $request;
        $this->userProfileRepository = $userProfileRepository;
        $this->cookRepository = $cookRepository;
        $this->imageRepository = $imageRepository;
        $this->userRepository = $userRepository;
        $this->mailService = $mailService;
        $this->privacyRepository = $privacyRepository;
        $this->dishRepository = $dishRepository;
        $this->destroyService = $destroyService;
        $this->advertRepository = $advertRepository;
        $this->orderRepository = $orderRepository;
        $this->walletRepository = $walletRepository;
    }

    public function settings(): View
    {
        /** @var User $user */
        $user = $this->request->user();

        $profile = $this->userProfileRepository->findByUserUuid($user->getUuid());
        $cookProfile = $this->cookRepository->findByUserUuid($user->getUuid());
        $images = $this->imageRepository->getUserProfileImages($user->getUuid());
        $privacy = $this->privacyRepository->findByUserUuid($user->getUuid());

        $availableSaldo = $this->walletRepository->countAvailableWalletLinesForUser($user->getUuid());

        $deletedAccount = false;
        if ($availableSaldo == 0) {
            $deletedAccount = true;
        }

        $showSoldPortions = $privacy ? $privacy->showSoldPortions() === 3 : false;

        // Always get fresh profile image path
        $mainProfileImage = $this->imageRepository->findMainProfileImage($user->getUuid());
        $profileImagePath = $mainProfileImage ? $mainProfileImage->getCompletePath() : url('/img/kok.png');

        // Current processing logic
        $profileDescription = '';
        if ($user->profileDescription) {
            $desc = trim($user->profileDescription->getDescription() ?? '');
            $profileDescription = ($desc === 'none' || $desc === '') ? '' : $desc;
        }

        return view('dashboard.settings.index', [
            'title' => 'Profiel',
            'user' => $user,
            'userProfile' => $profile,
            'cookProfile' => $cookProfile,
            'profileDescription' => $profileDescription,
            'profileImages' => $images,
            'profileImagePath' => $profileImagePath,
            'mainProfileImage' => $mainProfileImage,
            'deleteAccount' => $deletedAccount,
            'showSoldPortions' => $showSoldPortions,
            'privacy' => $privacy,
        ]);
    }

    public function addProfileImage()
    {
        $validated = $this->request->validate([
            'profileImage' => ['required', 'image', 'max:10240'],
            'description' => ['nullable'],
            'upload_type' => ['required', 'in:main,additional'],
        ]);

        $uploadType = $this->request->input('upload_type');
        $userUuid = $this->request->user()->getUuid();

        // Check limits voor aanvullende afbeeldingen
        if ($uploadType === 'additional') {
            $additionalImagesCount = $this->imageRepository->getAdditionalProfileImagesCount($userUuid);
            if ($additionalImagesCount >= 3) {
                return response()->json(['error' => 'Je kunt maximaal 3 aanvullende profielafbeeldingen hebben'], 400);
            }
        }

        // Maak directory aan
        $profileDirectory = 'img/'.$userUuid.'/profile';
        if (! file_exists($profileDirectory)) {
            mkdir($profileDirectory, 0755, true);
        }

        // Process image
        /** @var UploadedFile $image */
        $image = $this->request->file('profileImage');
        $fileName = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
        $imageType = strtolower($image->getClientOriginalExtension());

        // Handle image orientation
        $orientation = 1;
        if (function_exists('exif_read_data')) {
            $exif = @exif_read_data($image->getPathname());
            if ($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
            }
        }

        // Create image resource
        switch ($imageType) {
            case 'jfif':
            case 'jpeg':
            case 'jpg':
                $source = imagecreatefromjpeg($image->getPathname());
                break;
            case 'png':
                $source = imagecreatefrompng($image->getPathname());
                break;
            case 'gif':
                $source = imagecreatefromgif($image->getPathname());
                break;
            case 'wbmp':
                $source = imagecreatefromwbmp($image->getPathname());
                break;
            case 'gd':
                $source = imagecreatefromgd($image->getPathname());
                break;
            default:
                return response()->json(['error' => 'Ongeldig afbeeldingstype'], 400);
        }

        // Correct orientation
        switch ($orientation) {
            case 3:
                $source = imagerotate($source, 180, 0);
                break;
            case 6:
                $source = imagerotate($source, -90, 0);
                break;
            case 8:
                $source = imagerotate($source, 90, 0);
                break;
        }

        // Save image
        $resizePercentage = 25;
        imagejpeg($source, $profileDirectory.'/'.$fileName, $resizePercentage);
        imagedestroy($source);

        // HOOFDAFBEELDING LOGICA
        if ($uploadType === 'main') {
            // Verwijder oude hoofdafbeelding
            $oldMainImage = $this->imageRepository->findMainProfileImage($userUuid);
            if ($oldMainImage) {
                $oldFilePath = public_path($oldMainImage->getCompletePath());
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
                $this->imageRepository->delete($oldMainImage->getUuid());
            }
        }

        // Create database record
        $newImage = $this->imageRepository->create(
            new ImageDto(
                $userUuid,
                $profileDirectory,
                $fileName,
                $this->request->input('description') ?? 'Profile image',
                '',
                null,
                Image::PROFILE_IMAGE,
                $uploadType === 'main' // main_picture boolean
            )
        );

        return response()->json([
            'success' => true,
            'image' => $newImage,
            'upload_type' => $uploadType,
        ]);
    }

    public function removeProfileImage()
    {
        $remove = $this->request->input('old_uuid');

        if (empty($remove)) {
            return response()->json(['error' => 'Geen afbeelding UUID opgegeven'], 400);
        }

        try {
            $image = $this->imageRepository->find($remove);

            if (! $image) {
                return response()->json(['message' => 'Afbeelding niet gevonden'], 200);
            }

            // Security check
            if ($image->getUserUuid() !== $this->request->user()->getUuid()) {
                return response()->json(['error' => 'Geen toegang tot deze afbeelding'], 403);
            }

            // Delete physical file
            $filePath = public_path($image->getCompletePath());
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete database record
            $this->imageRepository->delete($remove);

            return response()->json(['message' => 'Afbeelding succesvol verwijderd']);

        } catch (\Exception $e) {
            \Log::error('Error removing profile image: '.$e->getMessage());

            return response()->json(['error' => 'Er is een fout opgetreden bij het verwijderen'], 500);
        }
    }

    public function updateProfileDescription(): \Illuminate\Http\JsonResponse
    {
        $profileDescription = $this->request->input('profile-description');

        $this->request->validate([
            'profile-description' => ['nullable', 'string', 'max:1000'],
        ]);

        $profileDescription = trim($profileDescription ?? '');
        $profileDescription = ($profileDescription === '') ? 'none' : $profileDescription;

        $this->userRepository->updateProfileDescription(
            new CommonDto($profileDescription),
            $this->request->user()->getUuid()
        );

        return response()->json(['success' => true]);
    }

    public function removeCompleteAccount(): view
    {
        $adverts = $this->advertRepository->getActiveAndPublishedAdverts($this->request->user()->getUuid());

        /**
         * @var Collection $adverts
         * @var Advert $advert
         */
        foreach ($adverts as $key => $advert) {
            if (
                $advert->isPublished() ||
                $advert->getParsedOrderTo()->isPast() &&
                $advert->getParsedPickupTo()->isPast()
            ) {
                $adverts->forget($key);
            }
        }

        if ($adverts->isEmpty()) {
            return view('dashboard.settings.remove', ['hideMenu' => true]);
        } else {
            return view('dashboard.settings.not_removed', ['hideMenu' => true]);
        }
    }

    public function deleteUserAccount(): RedirectResponse
    {
        /** @var User $user */
        $user = $this->request->user();
        Auth::logout();

        $dishes = $this->dishRepository->getDishesByUserUuid($user);

        /** @var Dish $dish */
        foreach ($dishes as $dish) {
            $this->destroyService->deleteDish(
                $dish->getUuid(),
                $user,
                '',
                true
            );
        }

        $this->userRepository->delete($user->getUuid());
        $this->mailService->sendDeleteNotification($user);

        return redirect()->route('home');
    }

    public function showUserDetails(): view
    {
        /** @var User $user */
        $user = $this->request->user();
        $profile = $this->userProfileRepository->findByUserUuid($user->getUuid());
        $cook = $this->cookRepository->findByUserUuid($user->getUuid());

        // Check if there are any active adverts
        $hasActiveAdverts = $this->advertRepository->getActiveAndPublishedAdverts($user->getUuid())->isNotEmpty();

        return view('dashboard.settings.details', [
            'user' => $user,
            'profile' => $profile,
            'cook' => $cook,
            'title' => 'Gegevens',
            'showAdress' => ! $hasActiveAdverts,
        ]);
    }

    public function getCurrentSettings(): view
    {
        return view('dashboard.settings.report', [
            'cook' => $this->cookRepository->findByUserUuid($this->request->user()->getUuid()),
            'title' => 'Meldingen',
        ]);
    }

    public function createOrUpdateReports(): RedirectResponse
    {
        /** @var User $user */
        $user = $this->request->user();
        /** @var Cook $cook */
        $cook = $this->cookRepository->findByUserUuid($user->getUuid());

        if (is_null($cook)) {
            return redirect()->route('verification.first');
        }

        $this->cookRepository->updateReportOrDescription(
            new CommonDto(
                'empty',
                ($this->request->input('new-order') === 'yes'),
                ($this->request->input('cancel-order') === 'yes'),
                ($this->request->input('self-cancel') === 'yes'),
            ),
            $cook->getUuid()
        );

        return redirect()->route('dashboard.settings.reports.home')->with('message', 'Instellingen aangepast');
    }

    public function getPrivacySettings(): view
    {
        return view('dashboard.settings.privacy', [
            'privacy' => $this->privacyRepository->findByUserUuid($this->request->user()->getUuid()),
            'title' => 'Privacy',
        ]);
    }

    public function createFirstTimeData(): RedirectResponse
    {
        return redirect()->route('verification.first');
    }

    public function updateLocation(): RedirectResponse
    {
        session(['editing_address_from_settings' => true]);

        return redirect()->route('verification.location');
    }

    public function updatePrivacySettings(): RedirectResponse
    {
        $this->request->validate([
            'email' => 'required',
            'street' => 'required',
            'number' => 'required',
            'phone' => 'required',
        ]);

        /** @var User $user */
        $user = $this->request->user();
        $privacy = $this->privacyRepository->findByUserUuid($user->getUuid());

        $fields = [
            'email' => [2, 3],
            'street' => [2, 3],
            'phone' => [2, 3],
            'number' => [2, 3],
            'sold_portions' => [1, 3],
        ];

        $errors = [];
        $returnErrors = false;

        foreach ($fields as $field => $allowedValues) {
            $inputValue = (int) $this->request->input($field);

            if (! in_array($inputValue, $allowedValues)) {
                $errors[$field] = "Invalid value for $field";
                $returnErrors = true;
            }
        }

        if ($returnErrors) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        $soldPortions = (int) $this->request->input('sold_portions', 1);
        $soldPortions = in_array($soldPortions, [1, 3]) ? $soldPortions : 1;

        session()->put('selected_sold_portions', $soldPortions);

        $privacyDto = new PrivacyDto(
            3,
            (int) $this->request->input('street'),
            (int) $this->request->input('number'),
            (int) $this->request->input('phone'),
            (int) $this->request->input('email'),
            $soldPortions
        );

        if (is_null($privacy)) {
            $this->privacyRepository->create($privacyDto, $user->getUuid());
        } else {
            $this->privacyRepository->update($privacyDto, $privacy->getUuid());
        }

        if (method_exists(\Cache::class, 'forget')) {
            \Cache::forget('privacy_settings_'.$user->getUuid());
        }

        return redirect()
            ->route('dashboard.settings.privacy.home')
            ->with('message', 'Instellingen aangepast');
    }

    public function submitLocationUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'postal' => ['required', 'regex:/^\d{4}\s?[A-Za-z]{2}$/'],
            'housenumber' => 'required|string|max:15',
            'addition' => 'nullable|string|max:5',
            'street' => 'required|string|max:100',
            'place' => 'required|string|max:100',
            'country' => 'required|string|max:10',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        /** @var User $user */
        $user = $request->user();
        $cook = $this->cookRepository->findByUserUuid($user->getUuid());

        if ($cook) {
            $cook->update([
                'postal_code' => $request->postal,
                'house_number' => $request->housenumber,
                'addition' => $request->addition,
                'street' => $request->street,
                'city' => $request->place,
                'country' => $request->country,
                'lat' => $request->latitude,
                'long' => $request->longitude,
            ]);
        }

        session()->forget('editing_address_from_settings');

        return redirect()->route('dashboard.settings.details.home')
            ->with('message', 'Adres succesvol bijgewerkt');
    }

    public function getImage()
    {
        $uuid = $this->request->query('uuid');

        if (! $uuid) {
            return response()->json(['error' => 'UUID niet opgegeven'], 400);
        }

        $image = $this->imageRepository->find($uuid);

        if ($image) {
            return response()->file($image->getPath().'/'.$image->getFileName());
        } else {
            return response()->json(['error' => 'Afbeelding niet gevonden'], 404);
        }
    }

    public function showPasswordChange(): View
    {
        return view('dashboard.settings.password-change');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).+$/',
                function ($attribute, $value, $fail) use ($request) {
                    if (Hash::check($value, $request->user()->password)) {
                        $fail('Het nieuwe wachtwoord mag niet hetzelfde zijn als je huidige wachtwoord.');
                    }
                },
            ],
        ], [
            'password.regex' => 'Het wachtwoord moet minimaal 8 tekens lang zijn en minimaal één hoofdletter, één kleine letter en één cijfer bevatten.',
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Het huidige wachtwoord is niet correct.');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        $this->mailService->sendPasswordChangedNotification($user);

        return redirect()
            ->route('dashboard.settings.details.home')
            ->with('message', 'Je wachtwoord is succesvol gewijzigd.');
    }

    public function updateDetails(): RedirectResponse|View
    {
        $validator = \Illuminate\Support\Facades\Validator::make($this->request->all(), [
            'email' => ['nullable', 'email:rfc,dns', 'regex:/^[^@]+(\.[^@]+)*@[^@]+\.[^@]+$/'],
            'phone' => 'nullable',
        ], [
            'email.email' => 'Vul een geldig e-mailadres in',
            'email.regex' => 'Vul een geldig e-mailadres in',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validatedData = $validator->validated();

        /** @var User $user */
        $user = $this->request->user();
        $userProfile = $this->userProfileRepository->findByUserUuid($user->getUuid());

        $emailChanged = isset($validatedData['email']) && $validatedData['email'] !== $user->getEmail();
        $phoneChanged = isset($validatedData['phone']) && $validatedData['phone'] !== $userProfile->getPhoneNumber();

        if ($emailChanged) {
            if ($this->userRepository->findUserByEmail($validatedData['email']) ||
               User::where('email', $validatedData['email'])->where('uuid', '!=', $this->request->user()->uuid)->first() ||
               $this->userRepository->findDeletedUserByEmail($validatedData['email'])) {
                return redirect()->back()->with('userAlreadyExist', 'Deze email is al in gebruik');
            }
            $user = $this->userRepository->newEmail(
                $validatedData['email'],
                $this->request->user()->getUuid()
            );
            $this->mailService->sendChangedMailVerification($user);
            User::where('email', $validatedData['email'])->update(['not_verified_at' => now()]);

            return view('verification', [
                'verificationSend' => true,
                'verificationFailed' => false,
                'resend' => true,
            ]);
        }

        if ($phoneChanged) {
            $this->userProfileRepository->updatePhone(
                $this->request->user(),
                $validatedData['phone']
            );

            $cook = $this->request->user();
            $emailArray = [];
            $customers = $this->orderRepository->getClientsOrders();

            foreach ($customers as $order) {
                $customer = $order->client;

                if (in_array($customer->email, $emailArray)) {
                } else {
                    array_push($emailArray, $customer->email);
                    Mail::to($customer->email)->send(new SubscriberCookInfoUpdateInform(
                        $cook,
                        $customer
                    ));
                }
            }
        }

        session()->flash('message', 'Gegevens zijn aangepast');

        return redirect()->route('dashboard.settings.details.home');
    }

    public function toggleEditMode(): RedirectResponse
    {
        /** @var User $user */
        $user = $this->request->user();
        $profile = $this->userProfileRepository->findByUserUuid($user->getUuid());
        $cook = $this->cookRepository->findByUserUuid($user->getUuid());

        $isFirstTime = ! $profile || ! $cook;

        if ($isFirstTime) {
            return redirect()->route('verification.first');
        } else {
            return redirect()->route('dashboard.settings.details.home')->with('editMode', true);
        }
    }
}
