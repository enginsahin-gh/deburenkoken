<?php

namespace App\Models;

use App\Traits\HasPrimaryUuid;
use App\Traits\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensitiveDataAccessLog extends Model
{
    use HasPrimaryUuid;
    use HasTimestamps;

    protected $fillable = [
        'admin_user_uuid',
        'target_user_uuid',
        'field_type',
        'ip_address',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_uuid', 'uuid');
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_uuid', 'uuid');
    }
}
