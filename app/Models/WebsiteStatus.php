<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteStatus extends Model
{
    use HasFactory;

    protected $table = 'website_status';

    protected $fillable = ['is_online'];

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        // Prevent creating a second row in the table
        static::creating(function ($model) {
            // Check if there's already a row
            if (self::count() > 0) {
                throw new \Exception("Only one entry is allowed in the 'website_status' table.");
            }
        });
    }
}
