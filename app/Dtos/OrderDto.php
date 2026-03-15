<?php

namespace App\Dtos;

use App\Models\Advert;
use App\Models\Client;
use App\Models\Dish;
use App\Models\User;
use Carbon\Carbon;

class OrderDto
{
    private Dish $dish;

    private Client $client;

    private User $user;

    private Advert $advert;

    private int $portionAmount;

    private Carbon $expectedPickupTime;

    private int $paymentState;

    private ?string $remarks;

    public function __construct(
        Dish $dish,
        Client $client,
        User $user,
        Advert $advert,
        int $portionAmount,
        Carbon $expectedPickupTime,
        int $paymentState,
        ?string $remarks = null
    ) {
        $this->dish = $dish;
        $this->client = $client;
        $this->user = $user;
        $this->advert = $advert;
        $this->portionAmount = $portionAmount;
        $this->expectedPickupTime = $expectedPickupTime;
        $this->paymentState = $paymentState;
        $this->remarks = $remarks;
    }

    public function getDish(): Dish
    {
        return $this->dish;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getAdvert(): Advert
    {
        return $this->advert;
    }

    public function getPortionAmount(): int
    {
        return $this->portionAmount;
    }

    public function getExpectedPickupTime(): Carbon
    {
        return $this->expectedPickupTime;
    }

    public function getPaymentState(): int
    {
        return $this->paymentState;
    }

    public function getRemarks(): ?string
    {
        return $this->remarks;
    }
}
