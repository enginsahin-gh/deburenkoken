<?php

namespace App\Models;

use App\Traits\HasPrimaryUuid;
use App\Traits\HasTimestamps;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dish extends Model
{
    use HasFactory;
    use HasPrimaryUuid;
    use HasTimestamps;
    use SoftDeletes;

    protected $table = 'dishes';

    protected $fillable = [
        'user_uuid',
        'cook_uuid',
        'title',
        'description',
        'is_vegetarian',
        'is_vegan',
        'is_halal',
        'has_alcohol',
        'has_gluten',
        'has_lactose',
        'spice_level',
        'portion_price',  // NIEUW TOEGEVOEGD
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getImagePath(): string
    {
        $imageRepository = app(\App\Repositories\ImageRepository::class);

        return $imageRepository->getDishImagePath($this->getUuid());
    }

    public function getCookUuid(): ?string
    {
        return $this->cook_uuid;
    }

    public function getUserUuid(): string
    {
        return $this->user_uuid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function isVegetarian(): bool
    {
        return $this->is_vegetarian;
    }

    public function isVegan(): bool
    {
        return $this->is_vegan;
    }

    public function isHalal(): bool
    {
        return $this->is_halal;
    }

    public function hasAlcohol(): bool
    {
        return $this->has_alcohol;
    }

    public function hasGluten(): bool
    {
        return $this->has_gluten;
    }

    public function hasLactose(): bool
    {
        return $this->has_lactose;
    }

    public function getSpiceLevel(): int
    {
        return $this->spice_level;
    }

    // NIEUWE METHODE VOOR PRIJS
    public function getPortionPrice(): float
    {
        return $this->portion_price ?? 0.0;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function cook(): BelongsTo
    {
        return $this->belongsTo(Cook::class, 'cook_uuid', 'uuid');
    }

    public function image(): HasOne
    {
        return $this->hasOne(Image::class, 'dish_uuid', 'uuid');
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'dish_uuid', 'uuid');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'dish_uuid', 'uuid');
    }

    public function hasActiveAdvert(): bool
    {
        return $this->adverts()
            ->whereNotNull('published')
            ->where(function ($query) {
                $query->where('pickup_date', '>', Carbon::now()->toDateString())
                    ->orWhere(function ($q) {
                        $q->where('pickup_date', '=', Carbon::now()->toDateString())
                            ->where('pickup_to', '>', Carbon::now()->toTimeString());
                    });
            })
            ->exists();
    }

    public function adverts(): HasMany
    {
        return $this->hasMany(Advert::class, 'dish_uuid', 'uuid');
    }

    public function trashedAdverts(): HasMany
    {
        return $this->hasMany(Advert::class, 'dish_uuid', 'uuid')->withTrashed();
    }
}
