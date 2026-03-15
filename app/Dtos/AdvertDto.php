<?php

namespace App\Dtos;

use App\Models\Cook;
use App\Models\Dish;
use Carbon\Carbon;

class AdvertDto
{
    private Dish $dish;

    private int $portionAmount;

    // VERWIJDERD: private float $portionPrice;
    private string $pickupDate;

    private string $pickupFrom;

    private string $pickupTo;

    private string $orderDate;

    private string $orderTime;

    private bool $published;

    public function __construct(
        Dish $dish,
        int $portionAmount,
        // VERWIJDERD: float $portionPrice,
        string $pickupDate,
        string $pickupFrom,
        string $pickupTo,
        string $orderDate,
        string $orderTime,
        bool $published
    ) {
        $this->dish = $dish;
        $this->portionAmount = $portionAmount;
        // VERWIJDERD: $this->portionPrice = $portionPrice;
        $this->pickupDate = $pickupDate;
        $this->pickupFrom = $pickupFrom;
        $this->pickupTo = $pickupTo;
        $this->orderDate = $orderDate;
        $this->orderTime = $orderTime;
        $this->published = $published;
    }

    public function getDish(): Dish
    {
        return $this->dish;
    }

    public function getPortionAmount(): int
    {
        return $this->portionAmount;
    }

    // VERWIJDERD: getPortionPrice() - nu via $dish->getPortionPrice()

    public function getPickupDate(): string
    {
        return $this->pickupDate;
    }

    public function getPickupFrom(): string
    {
        return $this->pickupFrom;
    }

    public function getPickupTo(): string
    {
        return $this->pickupTo;
    }

    public function getOrderDate(): string
    {
        return $this->orderDate;
    }

    public function getOrderTime(): string
    {
        return $this->orderTime;
    }

    public function publish(): bool
    {
        return $this->published;
    }
}
