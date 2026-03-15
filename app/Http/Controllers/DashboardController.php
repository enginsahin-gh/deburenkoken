<?php

namespace App\Http\Controllers;

use App\Dtos\CookDto;
use App\Dtos\DishDto;
use App\Dtos\UserProfileDto;
use App\Models\User;
use App\Repositories\AdvertRepository;
use App\Repositories\CookRepository;
use App\Repositories\DishRepository;
use App\Repositories\UserProfileRepository;
use Carbon\Carbon;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\Query\Coordinates;
use Geocoder\Query\GeocodeQuery;
use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Cookie;

class DashboardController extends Controller
{
    private Request $request;

    private UserProfileRepository $userProfileRepository;

    private CookRepository $cookRepository;

    private DishRepository $dishRepository;

    private AdvertRepository $advertRepository;

    private $userRepository; // Add this property

    public function __construct(
        Request $request,
        UserProfileRepository $userProfileRepository,
        CookRepository $cookRepository,
        DishRepository $dishRepository,
        AdvertRepository $advertRepository
    ) {
        $this->request = $request;
        $this->userProfileRepository = $userProfileRepository;
        $this->cookRepository = $cookRepository;
        $this->dishRepository = $dishRepository;
        $this->advertRepository = $advertRepository;
    }

    public function firstTimeUser(): Response
    {

        $this->request->session()->keep(['profile' => 'required']);
        $advertUuid = $this->request->query('advert_uuid');
        $user = $this->request->user();

        $accepted = cookie('accepted_cookies')->getValue();
        if ($accepted == 'true') {
            // return response(view('firsttime', [
            //     'firsttime' => true,
            //     'secondtime' => false,
            //     'advert' => $advertUuid,
            // ]))->cookie( cookie('profile', $this->request->user()->getUuid()));
            return response(view('firsttime', [
                'firsttime' => true,
                'secondtime' => false,
                'advert' => $advertUuid,
                'user' => $user,
            ]));
        } else {
            return response(view('firsttime', [
                'firsttime' => true,
                'secondtime' => false,
                'advert' => $advertUuid,
                'user' => $user,
            ]));
        }
    }

    public function firstTimeUserLocation(): View
    {
        $user = $this->request->user();
        $cook = $this->cookRepository->findByUserUuid($user->getUuid());

        return view('firsttime', [
            'location' => true,
            'firsttime' => false,
            'secondtime' => true,
            'advert' => $this->request->query('advert_uuid'),
            'cook' => $cook,
            'user' => $user,
        ]);
    }

    public function firstTimeUserFinish(): View
    {
        return view('firsttime', [
            'firsttime' => false,
            'secondtime' => false,
        ]);
    }

    public function postProfileAndCookInformation(): RedirectResponse
    {
        $validator = Validator::make($this->request->all(), [
            'firstname' => 'required',
            'insertion' => 'nullable',
            'lastname' => 'required',
            'phone' => ['required'],
            'birthday' => ['required', 'date', 'before:-18 years'],
            'kvk_naam' => ['nullable', 'max:100', 'regex:/^[A-Za-zÀ-ÖØ-öø-ÿ\s\-\&\.]*$/'],
            'btw_nummer' => ['nullable', 'max:50', 'regex:/^NL[0-9]+[AB][0-9]{2}$/'],
            'nvwa_nummer' => 'nullable|alpha_num|max:20',
        ], [
            'firstname.required' => 'Voornaam is verplicht',
            'lastname.required' => 'Achternaam is verplicht',
            'phone.required' => 'Telefoonnummer is verplicht',
            'birthday.required' => 'Geboortedatum is verplicht',
            'birthday.before' => 'Geboortedatum moet voor '.Carbon::now()->subYears(18)->translatedFormat('d-m-Y').' zijn',
            'kvk_naam.max' => 'KVK Naam mag maximaal 100 tekens bevatten',
            'kvk_naam.regex' => 'KVK Naam bevat ongeldige tekens',
            'btw_nummer.max' => 'BTW Nummer mag maximaal 50 tekens bevatten',
            'btw_nummer.regex' => 'BTW Nummer moet het formaat NL123456789B01 hebben',
            'nvwa_nummer.alpha_num' => 'NVWA Nummer mag alleen letters en cijfers bevatten',
            'nvwa_nummer.max' => 'NVWA Nummer mag maximaal 20 tekens bevatten',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        $userProfile = $this->userProfileRepository->createOrUpdate(
            new UserProfileDto(
                $validated['firstname'],
                $validated['lastname'],
                $validated['insertion'] ?? null,
                $validated['phone'],
                Carbon::parse($validated['birthday'])
            ),
            $this->request->user()
        );

        $user = $this->request->user();

        // Trim and normalize business fields
        $kvkNaam = ! empty(trim($validated['kvk_naam'] ?? '')) ? trim($validated['kvk_naam']) : null;
        $btwNummer = ! empty(trim($validated['btw_nummer'] ?? '')) ? trim($validated['btw_nummer']) : null;
        $nvwaNummer = ! empty(trim($validated['nvwa_nummer'] ?? '')) ? trim($validated['nvwa_nummer']) : null;

        // Bepaal type thuiskok op basis van KVK Naam (conform originele opdracht)
        // Als KVK Naam ingevuld is -> Zakelijke Thuiskok, anders null
        $userData = [
            'type_thuiskok' => $kvkNaam !== null ? 'Zakelijke Thuiskok' : null,
            'kvk_naam' => $kvkNaam,
            'btw_nummer' => $btwNummer,
            'nvwa_nummer' => $nvwaNummer,
        ];

        $user->update($userData);

        return redirect()->route('verification.location', [
            $userProfile,
            'advert_uuid' => $this->request->query('advert_uuid'),
        ]);
    }

    public function postLocation(): RedirectResponse
    {
        /** @var User $user */
        $user = $this->request->user();

        try {
            $validated = $this->request->validate([
                'postal' => ['required', 'regex:/^\d{4}\s?[A-Za-z]{2}$/'],
                'housenumber' => 'required|string|max:15',
                'addition' => 'nullable|string|max:5',
                'street' => 'required|string|max:100',
                'place' => 'required|string|max:100',
                'country' => 'required|string|max:10', // Vergroot naar 10 voor korte codes
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        }

        // Use validated values
        $latitude = $validated['latitude'];
        $longitude = $validated['longitude'];

        // Create the cook
        try {
            // Check if cook already exists and delete it first
            $existingCook = $this->cookRepository->findByUserUuid($user->getUuid());
            if ($existingCook) {
                $existingCook->delete();
            }

            $cook = $this->cookRepository->create(new CookDto(
                $user,
                $validated['street'],
                $validated['housenumber'],
                $validated['postal'],
                $validated['place'],
                $validated['country'],
                $latitude,
                $longitude,
                null,
                true,
                true,
                true,
                $validated['addition'] ?? null
            ));

            // Update dishes
            $dishes = $this->dishRepository->getDishesByUserUuid($user);
            foreach ($dishes as $dish) {
                $this->dishRepository->updateCookUuid($dish, $cook);
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['creation' => 'Failed to save location. Please try again.'])
                ->withInput();
        }

        // Determine redirect
        $advertUuid = $this->request->query('advert_uuid');

        if ($advertUuid) {
            $this->publishAfterProfile();

            return redirect()->route('dashboard.adverts.active.home');
        }

        return redirect()->route('dashboard.settings.details.home');
    }

    /**
     * Flexibelere adres validatie
     */
    private function validateAddressMatch(array $apiAddress, array $submittedAddress, string $houseNumber): bool
    {
        // Normaliseer postcode (verwijder spaties)
        $apiPostal = str_replace(' ', '', strtoupper($apiAddress['postal_code'] ?? ''));
        $submittedPostal = str_replace(' ', '', strtoupper($submittedAddress['postal']));

        // Normaliseer straatnamen (lowercase, trim)
        $apiStreet = strtolower(trim($apiAddress['street'] ?? ''));
        $submittedStreet = strtolower(trim($submittedAddress['street']));

        // Normaliseer plaatsnamen
        $apiPlace = strtolower(trim($apiAddress['locality'] ?? ''));
        $submittedPlace = strtolower(trim($submittedAddress['place']));

        // Land check (flexibel voor NL/Netherlands)
        $apiCountry = strtoupper($apiAddress['country'] ?? '');
        $submittedCountry = strtoupper($submittedAddress['country']);
        $countryMatch = ($apiCountry === 'NL' && $submittedCountry === 'NETHERLANDS') ||
                       ($apiCountry === 'NETHERLANDS' && $submittedCountry === 'NL') ||
                       ($apiCountry === $submittedCountry);

        // Huisnummer check (flexibel)
        $apiHouseNumber = strtolower(trim($apiAddress['street_number'] ?? ''));
        $submittedHouseNumber = strtolower(trim($houseNumber));

        $validationResults = [
            'postal_match' => $apiPostal === $submittedPostal,
            'street_match' => $apiStreet === $submittedStreet,
            'place_match' => $apiPlace === $submittedPlace,
            'country_match' => $countryMatch,
            'house_number_match' => $apiHouseNumber === $submittedHouseNumber,
        ];

        // Moet alle velden matchen
        return array_reduce($validationResults, function ($carry, $match) {
            return $carry && $match;
        }, true);
    }

    public function verifyIban(): RedirectResponse
    {
        $this->request->session()->flash('ibanVerification', true);

        return redirect()->route('dashboard.wallet.iban');
    }

    public function publishAfterProfile(): void
    {
        if ($this->request->query('advert_uuid')) {
            $this->advertRepository->publishAdvert($this->request->query('advert_uuid'));
        }
    }

    public function updateDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'phone' => 'nullable',
            'kvk_naam' => 'required',
            'btw_nummer' => 'required',
        ], [
            'email.required' => 'E-mailadres is verplicht',
            'email.email' => 'Vul een geldig e-mailadres in',
            'kvk_naam.required' => 'KVK Naam is verplicht',
            'btw_nummer.required' => 'BTW Nummer is verplicht',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validatedData = $validator->validated();

        $user = $request->user();

        if ($validatedData['email'] !== $user->getEmail()) {
            if ($this->userRepository->findUserByEmail($validatedData['email']) ||
                User::where('email', $validatedData['email'])->where('uuid', '!=', $request->user()->uuid)->first() ||
                $this->userRepository->findDeletedUserByEmail($validatedData['email'])) {
                return redirect()->back()->with('userAlreadyExist', 'Deze email is al in gebruik')->withInput();
            }
        }

        $userData = [
            'email' => $validatedData['email'],
            'type_thuiskok' => 'Zakelijke Thuiskok',
            'kvk_naam' => $validatedData['kvk_naam'],
            'btw_nummer' => $validatedData['btw_nummer'],
        ];

        $user->update($userData);

        if (isset($validatedData['phone']) && $validatedData['phone'] !== null) {
            $profile = $this->userProfileRepository->findByUserUuid($user->getUuid());
            if ($profile && $profile->getPhoneNumber() !== $validatedData['phone']) {
                $this->userProfileRepository->updatePhone($user, $validatedData['phone']);
            }
        }

        return redirect()->back()->with('success', 'Gegevens zijn succesvol bijgewerkt.');
    }

    public function showUserDetails(): view
    {
        /** @var User $user */
        $user = $this->request->user();
        $profile = $this->userProfileRepository->findByUserUuid($user->getUuid());
        $cook = $this->cookRepository->findByUserUuid($user->getUuid());

        $adverts = $this->advertRepository->getActiveAndPublishedAdverts($user->getUuid());

        $showAdress = true;
        $showAdesssCount = 0;

        foreach ($adverts as $advert) {
            if ($advert->published && $advert->deleted_at == null && $advert->order_date > Carbon::now()) {
                $showAdress = false;
            }
        }

        return view('dashboard.settings.details', [
            'user' => $user,
            'profile' => $profile,
            'cook' => $cook,
            'title' => 'Gegevens',
            'showAdress' => $showAdress,
            'orderDatePastCount' => $showAdesssCount,
        ]);
    }

    private function isValidCoordinates($latitude, $longitude)
    {
        $validator = Validator::make([
            'latitude' => $latitude,
            'longitude' => $longitude,
        ], [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        return ! $validator->fails();
    }

    // Function to get address from coordinates
    public function getAddressFromCoordinates($latitude, $longitude)
    {
        try {
            $googleGeocodingApiKey = config('services.google_cloud.api_key');
            $googlePlacesApiKey = config('services.google_cloud.api_key');

            if (! $googleGeocodingApiKey || ! $googlePlacesApiKey) {
                throw new \Exception('Google API keys not configured');
            }

            // Initialize HTTP client with timeout
            $client = new Client([
                'timeout' => 10,
                'connect_timeout' => 5,
            ]);

            // Step 1: Geocoding API request
            $geocodingResponse = $client->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'query' => [
                    'key' => $googleGeocodingApiKey,
                    'latlng' => "{$latitude},{$longitude}",
                    'result_type' => 'street_address|premise',  // Focus on street addresses
                ],
            ]);

            $geocodingResult = json_decode($geocodingResponse->getBody(), true);

            // Check if geocoding was successful
            if (! isset($geocodingResult['status']) || $geocodingResult['status'] !== 'OK') {
                throw new \Exception('Geocoding API returned error: '.($geocodingResult['status'] ?? 'unknown'));
            }

            if (empty($geocodingResult['results'])) {
                throw new \Exception('No geocoding results found for coordinates');
            }

            // Get the most precise result (usually the first one)
            $bestResult = $geocodingResult['results'][0];
            $placeId = $bestResult['place_id'] ?? null;

            if (! $placeId) {
                throw new \Exception('No place ID found in geocoding result');
            }

            // Step 2: Places API request
            $placesResponse = $client->get('https://maps.googleapis.com/maps/api/place/details/json', [
                'query' => [
                    'key' => $googlePlacesApiKey,
                    'placeid' => $placeId,
                    'fields' => 'address_components',  // Only get what we need
                ],
            ]);

            $placesResult = json_decode($placesResponse->getBody(), true);

            // Check if Places API was successful
            if (! isset($placesResult['status']) || $placesResult['status'] !== 'OK') {
                throw new \Exception('Places API returned error: '.($placesResult['status'] ?? 'unknown'));
            }

            // Extract address components
            if (! isset($placesResult['result']['address_components'])) {
                throw new \Exception('No address components found in Places API result');
            }

            $addressComponents = $placesResult['result']['address_components'];

            // Initialize variables
            $addressData = [
                'postal_code' => null,
                'street' => null,
                'locality' => null,
                'country' => null,
                'street_number' => null,
            ];

            // Process address components with fallbacks
            foreach ($addressComponents as $component) {
                $types = $component['types'];
                $longName = $component['long_name'];
                $shortName = $component['short_name'];

                foreach ($types as $type) {
                    switch ($type) {
                        case 'postal_code':
                            $addressData['postal_code'] = $longName;
                            break;
                        case 'route':
                            $addressData['street'] = $longName;
                            break;
                        case 'locality':
                            $addressData['locality'] = $longName;
                            break;
                        case 'administrative_area_level_2':
                            // Fallback for locality if not found
                            if (! $addressData['locality']) {
                                $addressData['locality'] = $longName;
                            }
                            break;
                        case 'country':
                            $addressData['country'] = $shortName; // Use short name (NL instead of Netherlands)
                            break;
                        case 'street_number':
                            $addressData['street_number'] = $longName;
                            break;
                    }
                }
            }

            // Validate that we have the required components
            $requiredFields = ['postal_code', 'street', 'locality', 'country', 'street_number'];
            $missingFields = [];

            foreach ($requiredFields as $field) {
                if (empty($addressData[$field])) {
                    $missingFields[] = $field;
                }
            }

            if (! empty($missingFields)) {
                throw new \Exception('Missing required address components: '.implode(', ', $missingFields));
            }

            return $addressData;

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            throw new \Exception('Network error during address validation: '.$e->getMessage());
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
