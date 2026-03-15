<?php

namespace App\Dtos;

use App\Models\Cook;
use App\Models\User;
use Carbon\Carbon;

class DishDto
{
    private User $user;

    private string $title;

    private ?string $description;

    private ?bool $vegetarian;

    private ?bool $vegan;

    private ?bool $halal;

    private ?bool $alcohol;

    private ?bool $gluten;

    private ?bool $lactose;

    private ?int $spiceLevel;

    private ?Cook $cook;

    private float $portionPrice;

    public function __construct(
        User $user,
        string $title,
        ?string $description = ' ',
        ?bool $vegetarian = null,
        ?bool $vegan = null,
        ?bool $halal = null,
        ?bool $alcohol = null,
        ?bool $gluten = null,
        ?bool $lactose = null,
        ?int $spiceLevel = null,
        ?Cook $cook = null,
        ?float $portionPrice = null  // AANGEPAST: Accepteert null
    ) {
        $this->user = $user;
        $this->title = $title;
        $this->description = $description;
        $this->vegetarian = $vegetarian;
        $this->vegan = $vegan;
        $this->halal = $halal;
        $this->alcohol = $alcohol;
        $this->gluten = $gluten;
        $this->lactose = $lactose;
        $this->spiceLevel = $spiceLevel;
        $this->cook = $cook;
        $this->portionPrice = $portionPrice ?? 0.0; // AANGEPAST: Standaard 0.0 als null
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function isVegetarian(): bool
    {
        return is_null($this->vegetarian) ? false : $this->vegetarian;
    }

    public function isVegan(): bool
    {
        return is_null($this->vegan) ? false : $this->vegan;
    }

    public function isHalal(): bool
    {
        return is_null($this->halal) ? false : $this->halal;
    }

    public function hasAlcohol(): bool
    {
        return is_null($this->alcohol) ? false : $this->alcohol;
    }

    public function hasGluten(): bool
    {
        return is_null($this->gluten) ? false : $this->gluten;
    }

    public function hasLactose(): bool
    {
        return is_null($this->lactose) ? false : $this->lactose;
    }

    public function getSpiceLevel(): int
    {
        return is_null($this->spiceLevel) ? 0 : $this->spiceLevel;
    }

    public function getCook(): ?Cook
    {
        return $this->cook;
    }

    public function getPortionPrice(): float
    {
        return $this->portionPrice;
    }
}
