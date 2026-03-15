<?php

namespace App\Dtos;

use App\Models\User;

class CookDto
{
    private User $user;

    private string $street;

    private int $houseNumber;

    private ?string $addition;

    private string $postalCode;

    private string $city;

    private string $country;

    private float $latitude;

    private float $longitude;

    private ?string $description;

    private ?bool $mailOrder;

    private ?bool $mailCancel;

    private ?bool $mailSelf;

    public function __construct(
        User $user,
        string $street,
        int $houseNumber,
        string $postalCode,
        string $city,
        string $country,
        float $latitude,
        float $longitude,
        ?string $description = null,
        ?bool $mailOrder = null,
        ?bool $mailCancel = null,
        ?bool $mailSelf = null,
        ?string $addition = null
    ) {
        $this->user = $user;
        $this->street = $street;
        $this->houseNumber = $houseNumber;
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->country = $country;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->description = $description;
        $this->mailOrder = $mailOrder;
        $this->mailCancel = $mailCancel;
        $this->mailSelf = $mailSelf;
        $this->addition = $addition;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getHouseNumber(): int
    {
        return $this->houseNumber;
    }

    public function getAddition(): ?string
    {
        return $this->addition;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getMailOrder(): ?bool
    {
        return $this->mailOrder;
    }

    public function getMailCancel(): ?bool
    {
        return $this->mailCancel;
    }

    public function getMailSelf(): ?bool
    {
        return $this->mailSelf;
    }
}
