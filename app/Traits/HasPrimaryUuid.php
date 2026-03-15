<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

trait HasPrimaryUuid
{
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Model $model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
            }
        });
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getParsedUuid(): string
    {
        return substr($this->uuid, -6);
    }

    public function getKeyName(): string
    {
        if ($this->defaultPrimaryKey) {
            return 'id';
        }

        return 'uuid';
    }

    public function getIncrementing(): bool
    {
        return false;
    }

    public function getKeyType(): string
    {
        return 'string';
    }
}
