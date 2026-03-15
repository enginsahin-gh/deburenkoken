<?php

namespace App\Models;

use App\Traits\HasPrimaryUuid;
use App\Traits\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Cook extends Model
{
    use HasFactory;
    use HasPrimaryUuid;
    use HasTimestamps;
    use SoftDeletes;

    protected $table = 'cooks';

    protected $fillable = [
        'user_uuid',
        'lat',
        'long',
        'street',
        'house_number',
        'addition',
        'postal_code',
        'city',
        'country',
        'description',
        'mail_order',
        'mail_cancel',
        'mail_self',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getUserUuid(): string
    {
        return $this->user_uuid;
    }

    public function getLatitude(): float
    {
        return $this->lat;
    }

    public function getLongitude(): float
    {
        return $this->long;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getHouseNumber(): int
    {
        return $this->house_number;
    }

    public function getAddition(): ?string
    {
        return $this->addition;
    }

    public function getPostalCode(): string
    {
        return $this->postal_code;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getMailOrder(): ?bool
    {
        return $this->mail_order;
    }

    public function getMailCancel(): ?bool
    {
        return $this->mail_cancel;
    }

    public function getMailSelf(): ?bool
    {
        return $this->mail_self;
    }

    public function setDistance(float $distance): self
    {
        $this->setAttribute('distance', $distance);

        return $this;
    }

    public function getDistance(): ?float
    {
        return $this->distance;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function dishes(): HasMany
    {
        return $this->hasMany(Dish::class, 'cook_uuid', 'uuid');
    }

    public function adverts(): HasManyThrough
    {
        return $this->hasManyThrough(
            Advert::class,
            Dish::class,
            'cook_uuid',
            'dish_uuid',
            'uuid',
            'uuid'
        );
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(
            Client::class,
            'mailing_lists',
            'cook_uuid',
            'client_uuid',
            'uuid',
            'uuid'
        );
    }

    public function mailingList(): HasMany
    {
        return $this->hasMany(
            MailingList::class,
            'cook_uuid',
            'uuid'
        );
    }

    public function getFilteredAdverts(string $from, string $to, bool $filters, bool $includeZeroLeftover = false): Collection
    {
        // Define the base query to retrieve adverts
        $query = $this->adverts()
            ->whereNotNull('published')
            ->where(function ($query) use ($from, $to, $filters) {
                if ($filters) {
                    $query->where('pickup_date', '>=', $from)
                        ->where('pickup_date', '<=', $to);
                } else {
                    $query->where('order_date', '>=', $from)
                        ->where('order_date', '<=', $to)
                        ->orWhere(function ($query) use ($from, $to) {
                            $query->where('pickup_date', '>=', $from)
                                ->where('pickup_date', '<=', $to);
                        });
                }
            });

        // Include or exclude adverts with zero leftover amount based on the parameter
        if (! $includeZeroLeftover) {
            $query->whereExists(function ($subQuery) {
                $subQuery->selectRaw('1')
                    ->from('orders')
                    ->whereRaw('orders.advert_uuid = adverts.uuid')
                    ->havingRaw('adverts.portion_amount - COALESCE(SUM(orders.portion_amount), 0) <> 0');
            });
        }

        // Retrieve the adverts with the associated relationships
        return $query->with([
            'cook.user.reviews',
            'dish.image',
            'order',
            'dish.images',
        ])->get();
    }

    public function getAdress()
    {
        return $this->getStreet().' '.$this->getHouseNumber().', '.$this->getPostalCode().', '.$this->getCity();
    }

    /**
     * MASTER METHODE: Correct verkochte porties berekening
     *
     * Deze methode telt alle porties die daadwerkelijk verkocht zijn:
     * - Orders met payment_state SUCCEED, PAYOUT_PENDING of PAID_OUT (succesvol betaald)
     * - Geannuleerde orders (STATUS_GEANNULEERD) worden uitgesloten
     * - Dit telt vanaf het aanmaken van het account (all time)
     *
     * Formule: Aantal succesvol verkochte porties - geannuleerde porties
     * (geannuleerde orders hebben STATUS_GEANNULEERD en worden automatisch uitgesloten)
     *
     * Dit is de autoritatieve bron voor verkochte porties berekeningen.
     * User model delegeert naar deze methode voor consistentie.
     */
    public function getSoldPortions(): int
    {
        return (int) $this->adverts()
            ->join('orders', 'orders.advert_uuid', '=', 'adverts.uuid')
            ->whereIn('orders.payment_state', [
                Order::SUCCEED,
                Order::PAYOUT_PENDING,
                Order::PAID_OUT,
            ])
            ->where('orders.status', '!=', Order::STATUS_GEANNULEERD)
            ->sum('orders.portion_amount');
    }

    /**
     * Alias voor getSoldPortions() voor backwards compatibility
     * Update code om getSoldPortions() te gebruiken waar mogelijk
     */
    public function countPortions(): int
    {
        return $this->getSoldPortions();
    }
}
