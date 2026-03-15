<?php

namespace App\Models;

use App\Traits\HasPrimaryUuid;
use App\Traits\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banking extends Model
{
    use HasFactory;
    use HasPrimaryUuid;
    use HasTimestamps;
    use SoftDeletes;

    protected $table = 'banking';

    protected $fillable = [
        'user_uuid',
        'account_holder',
        'iban',
        'validated',
        'verified',
        'id_location',
        'bic',
        'payment_id',
        'id_front',
        'id_back',
        'bsn',
    ];

    public function getUserUuid(): string
    {
        return $this->user_uuid;
    }

    public function getAccountHolder(): string
    {
        return $this->account_holder;
    }

    public function getIban(): string
    {
        return $this->iban;
    }

    public function isValidated(): bool
    {
        return $this->validated;
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }

    public function getIdLocation(): ?string
    {
        return $this->id_location;
    }

    public function getIdFront(): ?string
    {
        return $this->id_front;
    }

    public function getIdBack(): ?string
    {
        return $this->id_back;
    }

    public function getBsn(): ?string
    {
        return $this->bsn;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function getBic(): ?string
    {
        return $this->bic;
    }

    public function getPaymentId(): ?string
    {
        return $this->payment_id;
    }
}
