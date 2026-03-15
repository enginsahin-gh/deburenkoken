<?php

namespace App\Dtos;

use Carbon\Carbon;

class UserProfileDto
{
    private string $firstname;

    private ?string $insertion;

    private string $lastname;

    private ?string $phoneNumber;

    private ?Carbon $birthday;

    public function __construct(
        string $firstname,
        string $lastname,
        ?string $insertion = null,
        ?string $phoneNumber = null,
        ?Carbon $birthday = null
    ) {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->insertion = $insertion;
        $this->phoneNumber = $phoneNumber;
        $this->birthday = $birthday;
    }

    public function getFirstName(): string
    {
        return $this->firstname;
    }

    public function getInsertion(): ?string
    {
        return $this->insertion;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function getBirthDay(): ?Carbon
    {
        return $this->birthday;
    }
}
