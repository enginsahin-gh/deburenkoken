<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dac7Establishment extends Model
{
    use HasFactory;

    // EU Activity adres credentials
    protected $fillable = [
        'user_id',
        'has_establishment',
        'is_residential_address',
        'country',
        'postal_code',
        'street',
        'house_number',
    ];

    protected $casts = [
        'has_establishment' => 'boolean',
        'is_residential_address' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'uuid');
    }
}
