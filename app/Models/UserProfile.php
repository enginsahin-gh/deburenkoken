<?php

namespace App\Models;

use App\Traits\HasPrimaryUuid;
use App\Traits\HasTimestamps;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{
    use HasFactory;
    use HasPrimaryUuid;
    use HasTimestamps;
    use SoftDeletes;

    protected $table = 'user_profile';

    protected $fillable = [
        'firstname',
        'insertion',
        'lastname',
        'phone_number',
        'mobile_number',
        'birthday',
        'user_uuid',
    ];

    protected $casts = [
        'birthday' => 'datetime',
    ];

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getInsertion(): ?string
    {
        return $this->insertion;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    public function getBirthday(): Carbon
    {
        return $this->birthday;
    }

    public function getUserUuid(): string
    {
        return $this->user_uuid;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_uuid',
            'uuid'
        );
    }
}
