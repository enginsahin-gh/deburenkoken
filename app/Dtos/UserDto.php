<?php

namespace App\Dtos;

class UserDto
{
    private string $username;

    private string $email;

    private string $password;

    private bool $isBlockedByAdmin;

    private ?string $avatar;

    public function __construct(
        string $username,
        string $email,
        string $password,
        ?string $avatar = null,
        bool $isBlockedByAdmin = false
    ) {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->avatar = $avatar;
        $this->isBlockedByAdmin = $isBlockedByAdmin;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function isBlockedByAdmin(): bool
    {
        return $this->isBlockedByAdmin;
    }
}
