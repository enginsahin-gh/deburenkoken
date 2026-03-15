<?php

namespace App\Models;

use App\Traits\HasPrimaryUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailingList extends Model
{
    use HasFactory;
    use HasPrimaryUuid;

    protected $table = 'mailing_lists';

    protected $fillable = [
        'cook_uuid',
        'client_uuid',
    ];
}
