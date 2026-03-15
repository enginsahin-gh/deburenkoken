<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dac7Information extends Model
{
    use HasFactory;

    protected $table = 'dac7_informations';

    protected $fillable = [
        'user_id',
        'information_provided',
        'dac7_threshold_reached_at',
        'dac7_form_link',
    ];

    protected $attributes = [
        'information_provided' => false, // GEWIJZIGD: default op false
    ];

    protected $casts = [
        'dac7_threshold_reached_at' => 'datetime',
        'information_provided' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'uuid');
    }
}
