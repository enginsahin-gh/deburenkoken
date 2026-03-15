<?php

namespace App\Dtos;

class ClientDto
{
    private string $name;

    private string $email;

    private string $phoneNumber;

    public function __construct(
        string $name,
        string $email,
        string $phoneNumber
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }
}
