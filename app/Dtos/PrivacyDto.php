<?php

namespace App\Dtos;

class PrivacyDto
{
    private int $place;

    private int $street;

    private int $houseNumber;

    private int $phone;

    private int $email;

    private int $sold_portions;

    public function __construct(
        int $place,
        int $street,
        int $houseNumber,
        int $phone,
        int $email,
        int $sold_portions
    ) {
        $this->place = $place;
        $this->street = $street;
        $this->houseNumber = $houseNumber;
        $this->phone = $phone;
        $this->email = $email;
        $this->sold_portions = $sold_portions;
    }

    public function getPlace(): int
    {
        return $this->place;
    }

    public function getStreet(): int
    {
        return $this->street;
    }

    public function getHouseNumber(): int
    {
        return $this->houseNumber;
    }

    public function getPhone(): int
    {
        return $this->phone;
    }

    public function getSoldPortions(): int
    {
        return $this->sold_portions;
    }

    public function getEmail(): int
    {
        return $this->email;
    }
}
