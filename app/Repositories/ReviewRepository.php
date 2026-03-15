<?php

namespace App\Repositories;

use App\Dtos\ReviewDto;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReviewRepository
{
    private Review $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    public function find(string $reviewUuid): ?Review
    {
        return $this->review->find($reviewUuid);
    }

    public function get(?string $search = null): Collection
    {
        $query = $this->review->where('created_at', '<', Carbon::now());

        if (! is_null($search)) {
            $query->where('review', 'LIKE', '%'.$search.'%');
        }

        return $query->get();
    }

    public function create(ReviewDto $reviewDto): Review
    {
        return $this->review->create([
            'user_uuid' => $reviewDto->getOrder()->getUserUuid(),
            'order_uuid' => $reviewDto->getOrder()->getUuid(),
            'client_uuid' => $reviewDto->getClientUuid(),
            'anonymous' => $reviewDto->isAnonymous(),
            'rating' => $reviewDto->getRating(),
            'review' => $reviewDto->getReview(),
            'image' => $reviewDto->getImage(),
        ]);
    }

    public function update(
        ReviewDto $reviewDto,
        string $reviewUuid
    ): ?Review {
        $review = $this->find($reviewUuid);

        if (is_null($review)) {
            return null;
        }

        $review->update([
            'anonymous' => $reviewDto->isAnonymous(),
            'rating' => $reviewDto->getRating(),
            'review' => $reviewDto->getReview(),
        ]);

        return $review;
    }

    public function delete(string $reviewUuid): ?bool
    {
        return $this->find($reviewUuid)?->delete();
    }
}
