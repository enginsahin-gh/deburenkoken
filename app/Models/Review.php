<?php

namespace App\Models;

use App\Traits\HasPrimaryUuid;
use App\Traits\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory;
    use HasPrimaryUuid;
    use HasTimestamps;
    use SoftDeletes;

    protected $table = 'reviews';

    protected $fillable = [
        'user_uuid',
        'order_uuid',
        'client_uuid',
        'anonymous',
        'rating',
        'review',
        'image',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getUserUuid(): string
    {
        return $this->user_uuid;
    }

    public function getOrderUuid(): string
    {
        return $this->order_uuid;
    }

    public function getClientUuid(): string
    {
        return $this->client_uuid;
    }

    public function isAnonymous(): bool
    {
        return $this->anonymous;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function getReview(): string
    {
        return $this->review;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(
            Order::class,
            'order_uuid',
            'uuid'
        );
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(
            Client::class,
            'client_uuid',
            'uuid'
        );
    }
}
