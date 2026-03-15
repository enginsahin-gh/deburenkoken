<?php

namespace App\Dtos;

use App\Models\Order;

class ReviewDto
{
    private Order $order;

    private string $clientUuid;

    private bool $anonymous;

    private int $rating;

    private string $review;

    private ?string $image;

    public function __construct(
        Order $order,
        string $clientUuid,
        bool $anonymous,
        int $rating,
        string $review,
        ?string $image = null
    ) {
        $this->order = $order;
        $this->clientUuid = $clientUuid;
        $this->anonymous = $anonymous;
        $this->rating = $rating;
        $this->review = $review;
        $this->image = $image;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getClientUuid(): string
    {
        return $this->clientUuid;
    }

    public function isAnonymous(): bool
    {
        return $this->anonymous;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function getReview(): string
    {
        return $this->review;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }
}
