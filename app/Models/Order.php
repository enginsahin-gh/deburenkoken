<?php

namespace App\Models;

use App\Traits\HasPrimaryUuid;
use App\Traits\HasTimestamps;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use HasPrimaryUuid;
    use HasTimestamps;
    use SoftDeletes;

    protected $table = 'orders';

    public const STATUS_ACTIEF = 'Actief';

    public const STATUS_VERLOPEN = 'Verlopen';

    public const STATUS_GEANNULEERD = 'Geannuleerd';

    public const CANCELLED_BY_CLIENT = 'client';

    public const CANCELLED_BY_COOK = 'cook';

    protected $fillable = [
        'dish_uuid',
        'client_uuid',
        'user_uuid',
        'advert_uuid',
        'portion_amount',
        'expected_pickup_time',
        'remarks',
        'payment_state',
        'review_send',
        'profile_deleted',
        'payment_id',
        'preparation_email_sent',
    ];

    protected $casts = [
        'expected_pickup_time' => 'datetime',
        'review_send' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public const IN_PROCESS = 1;

    public const SUCCEED = 2;

    public const FAILED = 3;

    public const CANCELLED = 4;

    public const PAID_OUT = 5;

    public const PAYOUT_PENDING = 6;

    public function getDishUuid(): string
    {
        return $this->dish_uuid;
    }

    public function getClientUuid(): string
    {
        return $this->client_uuid;
    }

    public function getUserUuid(): string
    {
        return $this->user_uuid;
    }

    public function getParsedAdvertUuid(): string
    {
        return substr($this->advert_uuid, -6);
    }

    public function getAdvertUuid(): string
    {
        return $this->advert_uuid;
    }

    public function getPortionAmount(): int
    {
        return $this->portion_amount;
    }

    public function getExpectedPickupTime(): Carbon
    {
        return $this->expected_pickup_time;
    }

    public function getRemarks(): ?string
    {
        return $this->remarks;
    }

    public function getPaymentState(): int
    {
        return $this->payment_state;
    }

    public function getReviewSendAt(): ?Carbon
    {
        return $this->review_send;
    }

    public function getPaymentId()
    {
        return $this->payment_id;
    }

    public function getParsedOrderUuid(): string
    {
        return 'Bestelnummer: '.$this->getParsedUuid();
    }

    public function dish(): BelongsTo
    {
        return $this->belongsTo(Dish::class, 'dish_uuid', 'uuid');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_uuid', 'uuid');
    }

    public function clients(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_uuid', 'user_uuid');
    }

    public function advert(): BelongsTo
    {
        return $this->belongsTo(Advert::class, 'advert_uuid', 'uuid')
            ->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function walletLine(): HasOne
    {
        return $this->hasOne(WalletLine::class, 'order_uuid', 'uuid');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class, 'order_uuid', 'uuid');
    }

    public function profileDeleted(): bool
    {
        return $this->profile_deleted;
    }

    public function getStatus(): string
    {
        if ($this->status === self::STATUS_GEANNULEERD) {
            return self::STATUS_GEANNULEERD;
        }

        if ($this->status === self::STATUS_VERLOPEN) {
            return self::STATUS_VERLOPEN;
        }

        return self::STATUS_ACTIEF;
    }
}
