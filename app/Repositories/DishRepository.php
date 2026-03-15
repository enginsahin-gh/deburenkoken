<?php

namespace App\Repositories;

use App\Dtos\DishDto;
use App\Models\Dish;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class DishRepository
{
    private Dish $dish;

    public function __construct(Dish $dish)
    {
        $this->dish = $dish;
    }

    public function find(string $uuid): ?Dish
    {
        return $this->dish->with('image')->find($uuid);
    }

    public function get(): Collection
    {
        return $this->dish->with('image')->get();
    }

    /**
     * FIXED: Show ALL dishes for user, regardless of advertisement status
     * The filtering between "active" and "old" dishes should be handled at the UI level
     * or with separate methods, not by hiding dishes entirely from the overview
     */
    public function getDishesForUser(User $user, ?int $page = 1): LengthAwarePaginator
    {
        $query = $this->dish
            ->where('user_uuid', $user->getUuid())
            ->with('image')
            ->orderBy('created_at', 'desc');

        if (is_null($page)) {
            return $query->get();
        }

        return $query->paginate(10, ['*'], 'page', $page);
    }

    /**
     * Alternative method if you want to keep the filtering logic for "active" dishes
     * You can create a separate method for dishes with future advertisements
     */
    public function getActiveDishesForUser(User $user, ?int $page = 1): LengthAwarePaginator
    {
        $query = $this->dish
            ->where('user_uuid', $user->getUuid())
            ->where(function ($query) {
                $query->whereHas('adverts', function ($advertQuery) {
                    $advertQuery->where('pickup_date', '>', Carbon::now()->format('Y-m-d'))
                        ->orWhere(function ($q) {
                            $q->where('pickup_date', '=', Carbon::now()->format('Y-m-d'))
                                ->where('pickup_to', '>', Carbon::now()->format('H:i:s'));
                        });
                })
                    ->orWhereDoesntHave('adverts');
            })
            ->with('image')
            ->orderBy('created_at', 'desc');

        if (is_null($page)) {
            return $query->get();
        }

        return $query->paginate(10, ['*'], 'page', $page);
    }

    public function getDishesByUserUuid(User $user): Collection
    {
        return $this->dish
            ->where('user_uuid', $user->getUuid())
            ->with('image')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getOldDishesForUser(User $user, ?int $page = 1): LengthAwarePaginator
    {
        $query = $this->dish
            ->where('user_uuid', $user->getUuid())
            ->where(function ($query) {
                $query->whereHas('adverts', function ($advertQuery) {
                    $advertQuery->where('pickup_date', '<', Carbon::now()->format('Y-m-d'))
                        ->orWhere(function ($q) {
                            $q->where('pickup_date', '=', Carbon::now()->format('Y-m-d'))
                                ->where('pickup_to', '<', Carbon::now()->format('H:i:s'));
                        });
                });
            })
            ->with('image')
            ->orderBy('created_at', 'desc');

        return $query->paginate(10, ['*'], 'page', $page);
    }

    // ... rest of the methods remain the same

    public function create(DishDto $dishDto): Dish
    {
        return $this->dish->create([
            'user_uuid' => $dishDto->getUser()->getUuid(),
            'cook_uuid' => $dishDto->getCook()?->getUuid(),
            'title' => $dishDto->getTitle(),
            'description' => $dishDto->getDescription(),
            'is_vegetarian' => $dishDto->isVegetarian(),
            'is_vegan' => $dishDto->isVegan(),
            'is_halal' => $dishDto->isHalal(),
            'has_alcohol' => $dishDto->hasAlcohol(),
            'has_gluten' => $dishDto->hasGluten(),
            'has_lactose' => $dishDto->hasLactose(),
            'spice_level' => $dishDto->getSpiceLevel(),
            'portion_price' => $dishDto->getPortionPrice(),
        ]);
    }

    public function update(
        DishDto $dishDto,
        string $dishUuid
    ): ?Dish {
        $dish = $this->find($dishUuid);

        if (is_null($dish)) {
            return null;
        }

        $dish->update([
            'title' => $dishDto->getTitle(),
            'description' => $dishDto->getDescription(),
            'is_vegetarian' => $dishDto->isVegetarian(),
            'is_vegan' => $dishDto->isVegan(),
            'is_halal' => $dishDto->isHalal(),
            'has_alcohol' => $dishDto->hasAlcohol(),
            'has_gluten' => $dishDto->hasGluten(),
            'has_lactose' => $dishDto->hasLactose(),
            'spice_level' => $dishDto->getSpiceLevel(),
            'portion_price' => $dishDto->getPortionPrice(),
        ]);

        return $dish;
    }

    public function delete(string $dishUuid): ?bool
    {
        return $this->find($dishUuid)?->delete();
    }

    public function updateCookUuid(Dish $dish, $cook): bool
    {
        return $dish->update([
            'cook_uuid' => $cook->getUuid(),
        ]);
    }
}
