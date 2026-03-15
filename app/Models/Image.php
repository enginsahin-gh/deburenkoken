<?php

namespace App\Models;

use App\Traits\HasPrimaryUuid;
use App\Traits\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use HasFactory;
    use HasPrimaryUuid;
    use HasTimestamps;
    use SoftDeletes;

    protected $table = 'images';

    protected $fillable = [
        'user_uuid',
        'dish_uuid',
        'type_id',
        'main_picture',
        'path',
        'name',
        'description',
        'type',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const DISH_IMAGE = 1;

    public const PROFILE_IMAGE = 2;

    public function getUserUuid(): string
    {
        return $this->user_uuid;
    }

    public function getDishUuid(): ?string
    {
        return $this->dish_uuid;
    }

    public function getTypeId(): int
    {
        return $this->type_id;
    }

    public function isMainPicture(): bool
    {
        return $this->main_picture;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getCompletePath(): string
    {
        return DIRECTORY_SEPARATOR.$this->getPath().DIRECTORY_SEPARATOR.$this->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function dish(): BelongsTo
    {
        return $this->belongsTo(Dish::class, 'dish_uuid', 'uuid');
    }
}
