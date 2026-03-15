<?php

namespace App\Traits;

use Carbon\Carbon;

trait HasTimestamps
{
    public function getCreatedAt(): ?Carbon
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updated_at;
    }

    public function getCanceledAt(): ?Carbon
    {
        return $this->canceled_at;
    }

    public function getDeletedAt(): ?Carbon
    {
        return $this->deleted_at;
    }
}
