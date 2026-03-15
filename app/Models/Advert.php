<?php

namespace App\Models;

use App\Traits\HasPrimaryUuid;
use App\Traits\HasTimestamps;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class Advert extends Model
{
    use HasFactory;
    use HasPrimaryUuid;
    use HasTimestamps;
    use SoftDeletes;

    protected $table = 'adverts';

    protected $fillable = [
        'dish_uuid',
        'portion_amount',
        // VERWIJDERD: 'portion_price' - staat nu in dishes
        'pickup_date',
        'pickup_from',
        'pickup_to',
        'order_date',
        'order_time',
        'published',
        'profile_deleted',
        'preparation_email_sent',
    ];

    protected $dates = [
        'published',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getDishUuid(): string
    {
        return $this->dish_uuid;
    }

    public function getPortionAmount(): float
    {
        return $this->portion_amount;
    }

    public function getLeftOverAmount(): int
    {
        $this->order()
            ->where('status', Order::STATUS_ACTIEF)
            ->where('payment_state', Order::IN_PROCESS)
            ->where('created_at', '<', Carbon::now()->subMinutes(15))
            ->update(['payment_state' => Order::FAILED]);

        if ($this->getParsedPickupTo()->isPast()) {
            $this->order()
                ->where('status', Order::STATUS_ACTIEF)
                ->where(function ($query) {
                    $query->where('payment_state', Order::SUCCEED)
                        ->orWhere('payment_state', Order::PAYOUT_PENDING);
                })
                ->update(['status' => Order::STATUS_VERLOPEN]);
        }

        $orderedPortions = $this->order()
            ->where('status', Order::STATUS_ACTIEF)
            ->where(function ($query) {
                $query->where('payment_state', Order::SUCCEED)
                    ->orWhere('payment_state', Order::PAYOUT_PENDING);
            })
            ->sum('portion_amount');

        return $this->portion_amount - $orderedPortions;
    }

    public function getFailedAmount(): int
    {
        return $this->order()->where('status', Order::STATUS_ACTIEF)->where('payment_state', Order::FAILED)->sum('portion_amount');
    }

    public function getSucceedAmount(): int
    {
        $advertUuid = $this->getUuid();
        \Log::debug("getSucceedAmount for advert: {$advertUuid}");

        if ($this->getParsedPickupTo()->isPast()) {
            \Log::debug("Advert {$advertUuid} pickup time is past, updating orders");
            try {
                DB::beginTransaction();

                $updateCount = $this->order()
                    ->where(function ($query) {
                        $query->where('payment_state', Order::SUCCEED)
                            ->orWhere('payment_state', Order::PAYOUT_PENDING);
                    })
                    ->whereNotIn('status', [Order::STATUS_GEANNULEERD, Order::STATUS_VERLOPEN])
                    ->update(['status' => Order::STATUS_VERLOPEN]);

                \Log::debug("Updated {$updateCount} orders to VERLOPEN for advert {$advertUuid}");

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error updating advert orders: '.$e->getMessage());
            }
        }

        $result = DB::table('orders')
            ->where('advert_uuid', $advertUuid)
            ->where(function ($query) {
                $query->where('payment_state', Order::SUCCEED)
                    ->orWhere('payment_state', Order::PAYOUT_PENDING);
            })
            ->where('status', '!=', Order::STATUS_GEANNULEERD)
            ->sum('portion_amount');

        \Log::debug("getSucceedAmount for advert {$advertUuid} returning: {$result}");

        return $result;
    }

    public function getSucceedOrderAmount(): int
    {
        $advertUuid = $this->getUuid();
        \Log::debug("getSucceedOrderAmount for advert: {$advertUuid}");

        $result = DB::table('orders')
            ->where('advert_uuid', $advertUuid)
            ->where(function ($query) {
                $query->where('payment_state', Order::SUCCEED)
                    ->orWhere('payment_state', Order::PAYOUT_PENDING);
            })
            ->where('status', '!=', Order::STATUS_GEANNULEERD)
            ->count();

        \Log::debug("getSucceedOrderAmount for advert {$advertUuid} returning: {$result}");

        return $result;
    }

    public function getInProcessAmount(): int
    {
        return $this->order()->where('status', Order::STATUS_ACTIEF)->where('payment_state', Order::IN_PROCESS)->sum('portion_amount');
    }

    public function countOrders(): int
    {
        return $this->order->count();
    }

    public function getPortionPrice(): float
    {
        return $this->dish ? $this->dish->getPortionPrice() : 0.0;
    }

    public function getPickupDate(): string
    {
        return $this->pickup_date;
    }

    public function getPickupFrom(): string
    {
        return $this->pickup_from;
    }

    public function getPickupTo(): string
    {
        return $this->pickup_to;
    }

    public function getOrderDate(): string
    {
        return $this->order_date;
    }

    public function getOrderTime(): string
    {
        return $this->order_time;
    }

    public function setDistance(?float $distance): self
    {
        $this->setAttribute('distance', $distance);

        return $this;
    }

    public function getDistance(): ?float
    {
        return $this->distance;
    }

    public function published()
    {
        return $this->published;
    }

    public function isPublished(): bool
    {
        return is_null($this->published());
    }

    public function isCancelled(): bool
    {
        return ! is_null($this->deleted_at);
    }

    public function getParsedPickupFrom(): Carbon
    {
        return Carbon::parse($this->getPickupDate().' '.$this->getPickupFrom(), config('app.timezone'));
    }

    public function getParsedPickupTo(): Carbon
    {
        return Carbon::parse($this->getPickupDate().' '.$this->getPickupTo(), config('app.timezone'));
    }

    public function orderTimeLeft(): string
    {
        return $this->getParsedOrderTo()->diffForHumans(Carbon::now(), true);
    }

    public function getParsedOrderTo(): Carbon
    {
        return Carbon::parse($this->getOrderDate().' '.$this->getOrderTime(), config('app.timezone'));
    }

    public function getParsedAdvertUuid(): string
    {
        return 'Advertentienummer: '.$this->getParsedUuid();
    }

    public function deletedAt(): Builder
    {
        return $this->whereNotNull('deleted_at');
    }

    public function profileDeleted(): ?bool
    {
        return $this->profile_deleted;
    }

    public function dish(): BelongsTo
    {
        return $this->belongsTo(
            Dish::class,
            'dish_uuid',
            'uuid'
        );
    }

    public function order(): HasMany
    {
        return $this->hasMany(Order::class, 'advert_uuid', 'uuid')
            ->orderBy('status', 'asc')
            ->orderBy('expected_pickup_time');
    }

    public function cook(): HasOneThrough
    {
        return $this->hasOneThrough(
            Cook::class,
            Dish::class,
            'uuid',
            'uuid',
            'dish_uuid',
            'cook_uuid'
        );
    }

    public function getExpiredAdverts(): Builder
    {
        return $this->where('order_date', '<', Carbon::now())
            ->where('order_time', '<', Carbon::now())
            ->whereNull('deleted_at')
            ->where('pickup_date', '>', Carbon::now());
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'advert_uuid', 'uuid');
    }
}
