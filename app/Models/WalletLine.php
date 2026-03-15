<?php

namespace App\Models;

use App\Traits\HasPrimaryUuid;
use App\Traits\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletLine extends Model
{
    use HasFactory;
    use HasPrimaryUuid;
    use HasTimestamps;
    use SoftDeletes;

    protected $table = 'wallet_lines';

    protected $fillable = [
        'wallet_uuid',
        'order_uuid',
        'amount',
        'state',
    ];

    public const ON_HOLD = 1;

    public const PROCESSING = 2;

    public const AVAILABLE = 3;

    public const PAID_OUT = 4;

    public const PAYOUT_INITIATED = 8;

    public const REFUNDING = 5;

    public const REFUNDED = 6;

    public const COMPLETED = 7;

    public const CANCELLATION_COST = 9;

    public const CANCELLATION_COST_PAID = 10;

    public const CANCELLATION_COST_PAID_OUT = 11;

    public const RESERVED = 12;

    public function getWalletUuid(): string
    {
        return $this->wallet_uuid;
    }

    public function getOrderUuid(): string
    {
        return $this->order_uuid;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(
            Wallet::class,
            'wallet_uuid',
            'uuid'
        );
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(
            Order::class,
            'order_uuid',
            'uuid'
        );
    }
}
