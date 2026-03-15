<?php

namespace App\Models;

use App\Traits\HasPrimaryUuid;
use App\Traits\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Privacy extends Model
{
    use HasFactory;
    use HasPrimaryUuid;
    use HasTimestamps;
    use SoftDeletes;

    protected $table = 'privacy';

    protected $fillable = [
        'user_uuid',
        'place',
        'street',
        'house_number',
        'phone',
        'email',
        'sold_portions',
    ];

    public const showPrivacy = 3;

    public const orderPrivacy = 2;

    public const neverPrivacy = 1;

    public function getUserUuid(): string
    {
        return $this->user_uuid;
    }

    public function showPlace(): int
    {
        return $this->place;
    }

    public function showStreet(): int
    {
        return $this->street;
    }

    public function showHouseNumber(): int
    {
        return $this->house_number;
    }

    public function showPhone(): int
    {
        return $this->phone;
    }

    public function showSoldPortions(): int
    {
        return $this->sold_portions;
    }

    public function showEmail(): int
    {
        return $this->email;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
