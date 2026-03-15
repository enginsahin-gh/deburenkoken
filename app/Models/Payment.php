<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;

class Payment extends Model
{
    public const INITIATED = 1;      // Initial state when payout is requested

    public const PROCESSING = 2;     // Admin has started processing

    public const COMPLETED = 3;      // Admin has approved and completed payout

    public const REJECTED = 4;       // Admin has rejected the payout

    public const FAILED = 5;         // Payout failed for technical reasons

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'user_uuid',
        'banking_uuid',
        'amount',
        'state',
        'payment_date',
        'payment_type',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Uuid::uuid4()->toString();
        });
    }

    // Add user relationship
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    // Add relationship to wallet lines
    public function walletLines(): HasMany
    {
        return $this->hasMany(WalletLine::class, 'payment_uuid', 'uuid');
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getUserUuid(): string
    {
        return $this->user_uuid;
    }

    public function getBankingUuid(): string
    {
        return $this->banking_uuid;
    }

    public function getAmount(): string
    {
        return number_format($this->amount, 2);
    }

    public function getFeeAmount(): string
    {
        return number_format($this->amount * 0.05, 2);
    }

    public function getPayoutAmount(): string
    {
        return number_format($this->amount * 0.95, 2);
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function getPaymentDate(): ?Carbon
    {
        return $this->payment_date;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->created_at;
    }

    public function banking(): BelongsTo
    {
        return $this->belongsTo(Banking::class, 'banking_uuid', 'uuid');
    }

    public function getPaymentType(): string
    {
        return $this->payment_type ?? 'manual'; // Default to 'manual' if not set
    }
}
