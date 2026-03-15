<?php

namespace App\Http\Controllers;

use App\Dtos\DishDto;
use App\Dtos\ImageDto;
use App\Mail\DishChangeCookMail;
use App\Mail\DishChangeCustomerMail;
use App\Mail\DishDeleteCookMail;
use App\Mail\DishDeleteCustomerMail;
use App\Models\Advert;
use App\Models\Cook;
use App\Models\Dish;
use App\Models\Image;
use App\Models\Order;
use App\Models\Privacy;
use App\Models\User;
use App\Repositories\AdvertRepository;
use App\Repositories\DishRepository;
use App\Repositories\ImageRepository;
use App\Repositories\OrderRepository;
use App\Services\DestroyService;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class DishesController extends Controller
{
    private Request $request;

    private DishRepository $dishRepository;

    private ImageRepository $imageRepository;

    private AdvertRepository $advertRepository;

    private DestroyService $destroyService;

    public function __construct(
        Request $request,
        DishRepository $dishRepository,
        ImageRepository $imageRepository,
        AdvertRepository $advertRepository,
        DestroyService $destroyService
    ) {
        $this->request = $request;
        $this->dishRepository = $dishRepository;
        $this->imageRepository = $imageRepository;
        $this->advertRepository = $advertRepository;
        $this->destroyService = $destroyService;
    }

    public function dishes(): View
    {
        $dishes = $this->dishRepository->getDishesForUser(
            $this->request->user(),
            $this->request->query('page', 1)
        );
        $canCreateDish = $this->checkDailyDishLimit();

        return view('dashboard.dishes.index', [
            'dishes' => $dishes,
            'old' => false,
            'title' => 'Gerechten',
            'canCreateDish' => $canCreateDish,
        ]);
    }

    public function oldDishes(): View
    {
        $dishes = $this->dishRepository->getOldDishesForUser(
            $this->request->user(),
            $this->request->query('page', 1)
        );

        return view('dashboard.dishes.index', [
            'dishes' => $dishes,
            'old' => true,
            'title' => 'Gerechten',
        ]);
    }

    private function checkDailyDishLimit(): bool
    {
        $today = Carbon::now()->startOfDay();
        $dishCount = Dish::where('user_uuid', $this->request->user()->getUuid())
            ->whereDate('created_at', $today)
            ->count();

        return $dishCount < 25;
    }

    public function showSingleDish(string $uuid): View
    {
        $dish = $this->dishRepository->find($uuid);

        if (is_null($dish) || (! $this->request->user()->hasRole('admin') && $dish->getUserUuid() !== $this->request->user()->getUuid())) {
            throw new ModelNotFoundException;
        }

        return view('dashboard.dishes.show', [
            'dish' => $dish,
            'title' => 'Gerechten',
        ]);
    }

    public function createNewDish(): View|RedirectResponse
    {
        if (! $this->checkDailyDishLimit()) {
            return redirect()->route('dashboard.dishes.new')->with('error', 'Je hebt het maximale aantal gerechten (25) voor vandaag bereikt. Probeer het morgen opnieuw.');
        }

        return view('dashboard.dishes.edit', [
            'edit' => false,
            'dish' => null,
            'title' => 'Gerechten',
        ]);
    }

    public function duplicateDish(string $uuid): View
    {
        $dish = $this->dishRepository->find($uuid);

        if (is_null($dish) || $dish->getUserUuid() !== $this->request->user()->getUuid()) {
            throw new ModelNotFoundException;
        }

        return view('dashboard.dishes.edit', [
            'edit' => false,
            'dish' => $dish,
            'title' => 'Gerechten',
        ]);
    }

    public function storeNewDish(): RedirectResponse
    {
        if (! $this->checkDailyDishLimit()) {
            throw ValidationException::withMessages([
                'limit' => 'Je hebt het maximale aantal gerechten (25) voor vandaag bereikt. Probeer het morgen opnieuw.',
            ]);
        }

        $this->request->validate([
            'title' => [
                'required',
                'string',
                'max:150',
                Rule::unique('dishes')->where(function ($query) {
                    return $query->where('user_uuid', $this->request->user()->getUuid());
                }),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'profileImage' => 'nullable|image|min:1|max:20000',
            'spicy' => ['nullable'],
            'price' => ['required', 'numeric', 'min:1.00', 'max:25'],
            'specs' => ['nullable', 'array'],
        ], [
            'title' => 'De naam van het gerecht moet uniek zijn en niet langer dan 150 tekens',
            'price.required' => 'Prijs is verplicht',
            'price.min' => 'Prijs moet minimaal €1,00 zijn',
            'price.max' => 'Prijs mag maximaal €25,00 zijn',
        ]);

        /** @var User $user */
        $user = $this->request->user();
        $cook = $user->cook;

        // Parse specs array
        $specs = $this->request->input('specs', []);

        $dish = $this->dishRepository->create(
            new DishDto(
                $user,
                $this->request->input('title'),
                $this->request->input('description') ?? ' ',
                in_array('vegetarisch', $specs),      // GEFIXED
                in_array('veganistisch', $specs), // GEFIXED
                in_array('halal', $specs),            // GEFIXED
                in_array('bevat alcohol', $specs),    // GEFIXED
                in_array('glutenvrij', $specs),       // GEFIXED
                in_array('lactosevrij', $specs),      // GEFIXED
                $this->request->input('spicy'),
                $cook,
                $this->request->input('price')
            )
        );

        if ($oldUUid = $this->request->input('old_uuid')) {
            $oldDish = $this->dishRepository->find($oldUUid);

            /** @var Image $image */
            if ($image = $oldDish->image) {
                $this->imageRepository->create(
                    new ImageDto(
                        $user->getUuid(),
                        $image->getPath(),
                        $image->getName(),
                        $image->getDescription(),
                        $image->getType(),
                        $dish,
                        Image::DISH_IMAGE,
                        true
                    )
                );
            }
        }

        if ($this->request->hasFile('profileImage')) {
            /** @var UploadedFile $image */
            $image = $this->request->file('profileImage');
            $fileName = time().'.'.$image->getClientOriginalExtension();

            $moved = $image->move('img/'.$user->getUuid().'/dishes', $fileName);

            $this->imageRepository->create(
                new ImageDto(
                    $user->getUuid(),
                    $moved->getPath(),
                    $fileName,
                    $this->request->input('title'),
                    $image->getClientMimeType(),
                    $dish,
                    Image::DISH_IMAGE,
                    true
                )
            );
        }

        return redirect()->route('dashboard.dishes.new')->with('success', 'Gerecht succesvol aangemaakt!');
    }

    public function editDish(string $uuid): View|RedirectResponse
    {
        $dish = $this->dishRepository->find($uuid);

        if (is_null($dish) || $dish->getUserUuid() !== $this->request->user()->getUuid()) {
            throw new ModelNotFoundException;
        }

        if ($dish->hasActiveAdvert()) {
            return redirect()->route('dashboard.dishes.show', $uuid)
                ->with('error', 'Je kunt dit gerecht niet aanpassen omdat het in een actieve advertentie staat.');
        }

        return view('dashboard.dishes.edit', [
            'dish' => $dish,
            'edit' => true,
            'title' => 'Gerechten',
        ]);
    }

    public function updateDish(string $uuid): View|RedirectResponse
    {
        $dish = $this->dishRepository->find($uuid);

        if (is_null($dish) || $dish->getUserUuid() !== $this->request->user()->getUuid()) {
            throw new ModelNotFoundException;
        }

        if ($dish->hasActiveAdvert()) {
            return redirect()->route('dashboard.dishes.show', $uuid)
                ->with('error', 'Je kunt dit gerecht niet aanpassen omdat het in een actieve advertentie staat.');
        }

        $validated = $this->request->validate([
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

        $adverts = $this->advertRepository->findAdvertsForDish($dish->getUuid());
        $count = 0;

        foreach ($adverts as $advert) {
            $count += $advert->order->count();
        }

        if ($count === 0) {
            $dish = $this->dishRepository->update(
                new DishDto(
                    $this->request->user(),
                    $dish->getTitle(),
                    $this->request->input('description') ?? $dish->getDescription(),
                    $this->request->input('vegetarian'),
                    $this->request->input('vegan'),
                    $this->request->input('halal'),
                    $this->request->input('alcohol'),
                    $this->request->input('gluten'),
                    $this->request->input('lactose'),
                    $this->request->input('spicy'),
                    $this->request->user()->cook,
                    $dish->getPortionPrice()
                ),
                $uuid
            );

            return view('dashboard.dishes.update-complete', [
                'dish' => $dish,
                'hideMenu' => true,
            ]);
        }

        return view('dashboard.dishes.update-confirm', [
            'data' => $validated,
            'orderCount' => $count,
            'hideMenu' => true,
            'dish' => $dish,
        ]);
    }

    public function comfirmUpdateDish(
        string $uuid
    ): View|RedirectResponse {
        $dish = $this->dishRepository->find($uuid);

        if (is_null($dish) || $dish->getUserUuid() !== $this->request->user()->getUuid()) {
            throw new ModelNotFoundException;
        }

        $changes = json_decode($this->request->input('requestItems'), true);

        $dish = $this->dishRepository->update(
            new DishDto(
                $this->request->user(),
                $dish->getTitle(),
                array_key_exists('description', $changes) ? $changes['description'] : $dish->getDescription(),
                array_key_exists('vegetarian', $changes) ? $changes['vegetarian'] : $dish->isVegetarian(),
                array_key_exists('vegan', $changes) ? $changes['vegan'] : $dish->isVegan(),
                array_key_exists('halal', $changes) ? $changes['halal'] : $dish->isHalal(),
                array_key_exists('alcohol', $changes) ? $changes['alcohol'] : $dish->hasAlcohol(),
                array_key_exists('gluten', $changes) ? $changes['gluten'] : $dish->hasGluten(),
                array_key_exists('lactose', $changes) ? $changes['lactose'] : $dish->hasLactose(),
                array_key_exists('spicy', $changes) ? $changes['spicy'] : $dish->getSpiceLevel(),
                $this->request->user()->cook,
                $dish->getPortionPrice()
            ),
            $uuid
        );

        /** @var User $user */
        $user = $this->request->user();

        /** @var Cook $cook */
        $cook = $user->cook;

        if ($cook->getMailSelf()) {
            Mail::to(
                $user->getEmail(),
                $user->getUsername()
            )->send(new DishChangeCookMail(
                $user->getUsername(),
                $dish->getTitle(),
                $dish->getUuid()
            ));
        }

        /** @var Advert $advert */
        foreach ($this->advertRepository->findAdvertsForDish($dish->getUuid()) as $advert) {
            /** @var Order $order */
            foreach ($advert->order as $order) {
                Mail::to(
                    $order->client->getEmail(),
                    $order->client->getName()
                )->send(new DishChangeCustomerMail(
                    $this->request->input('editText'),
                    $dish,
                    $order
                ));
            }
        }

        return view('dashboard.dishes.update-complete', [
            'dish' => $dish,
            'hideMenu' => true,
        ]);
    }

    public function destroyDish(string $uuid): Factory|\Illuminate\Contracts\View\View|Application
    {
        $dish = $this->dishRepository->find($uuid);

        if (is_null($dish) || $dish->getUserUuid() !== $this->request->user()->getUuid()) {
            throw new ModelNotFoundException;
        }

        $adverts = $this->advertRepository->findAdvertsForDish($dish->getUuid());
        $count = 0;

        foreach ($adverts as $advert) {
            $count += $advert->order->count();
        }

        return view('dashboard.dishes.delete-confirm', [
            'dish' => $dish,
            'orderCount' => $count,
            'hideMenu' => true,
        ]);
    }

    public function confirmDestroyDish(string $uuid): View
    {
        $dish = $this->dishRepository->find($uuid);

        $this->destroyService->deleteDish(
            $uuid,
            $this->request->user(),
            $this->request->input('editText')
        );

        return view('dashboard.dishes.delete-complete', [
            'dish' => $dish,
            'hideMenu' => true,
        ]);
    }
}
