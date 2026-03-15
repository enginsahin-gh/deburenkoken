<?php

namespace App\Dtos;

class CommonDto
{
    private ?string $item;

    private bool $one;

    private bool $two;

    private bool $three;

    public function __construct(
        ?string $item = null,
        ?bool $one = false,
        ?bool $two = false,
        ?bool $three = false
    ) {
        $this->item = $item;
        $this->one = $one;
        $this->two = $two;
        $this->three = $three;
    }

    public function getItem(): ?string
    {
        return $this->item;
    }

    public function getOne(): bool
    {
        return $this->one;
    }

    public function getTwo(): bool
    {
        return $this->two;
    }

    public function getThree(): bool
    {
        return $this->three;
    }
}
