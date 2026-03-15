<?php

namespace App\Dtos;

use App\Models\Dish;
use App\Models\Image;

class ImageDto
{
    private string $userUuid;

    private ?Dish $dish;

    private string $path;

    private string $name;

    private string $description;

    private string $type;

    private int $typeId;

    private bool $mainPicture;

    public function __construct(
        string $userUuid,
        string $path,
        string $name,
        string $description,
        string $type,
        ?Dish $dish = null,
        int $typeId = Image::DISH_IMAGE,
        bool $mainPicture = false
    ) {
        $this->userUuid = $userUuid;
        $this->dish = $dish;
        $this->path = $path;
        $this->name = $name;
        $this->description = $description;
        $this->type = $type;
        $this->typeId = $typeId;
        $this->mainPicture = $mainPicture;
    }

    public function getUserUuid(): string
    {
        return $this->userUuid;
    }

    public function getDish(): ?Dish
    {
        return $this->dish;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTypeId(): int
    {
        return $this->typeId;
    }

    public function isMainPicture(): bool
    {
        return $this->mainPicture;
    }
}
