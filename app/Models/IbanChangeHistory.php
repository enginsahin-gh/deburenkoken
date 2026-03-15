<?php

namespace App\Models;

use App\Traits\HasPrimaryUuid;
use App\Traits\HasTimestamps;
use Illuminate\Database\Eloquent\Model;

class IbanChangeHistory extends Model
{
    use HasPrimaryUuid;
    use HasTimestamps;

    protected $fillable = [
        'user_uuid',
        'old_iban',
        'new_iban',
        'change_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
