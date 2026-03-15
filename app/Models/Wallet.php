<?php

namespace App\Models;

use App\Traits\HasPrimaryUuid;
use App\Traits\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use HasFactory;
    use HasPrimaryUuid;
    use HasTimestamps;
    use SoftDeletes;

    protected $table = 'wallets';

    protected $fillable = [
        'user_uuid',
        'state',
        'total_available',
        'total_processing',
        'total_paid',
    ];

    public const LIMITED = 1;

    public const FULL = 2;

    public const BLOCKED = 3;

    public function getUserUuid(): string
    {
        return $this->user_uuid;
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function getTotalAvailable(): float
    {
        return $this->total_available;
    }

    public function getTotalProcessing(): float
    {
        return $this->total_processing;
    }

    public function getTotalPaid(): float
    {
        return $this->total_paid;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_uuid',
            'uuid'
        );
    }

    public function walletLines(): HasMany
    {
        return $this->hasMany(
            WalletLine::class,
            'wallet_uuid',
            'uuid'
        );
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_uuid', 'user_uuid');
    }
}
