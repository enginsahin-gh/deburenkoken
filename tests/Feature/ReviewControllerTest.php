<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createOrderWithReview(): array
    {
        $order = Order::factory()->paid()->create();
        $client = Client::factory()->create();

        $review = Review::create([
            'user_uuid' => $order->getUserUuid(),
            'order_uuid' => $order->getUuid(),
            'client_uuid' => $client->uuid,
            'anonymous' => false,
            'rating' => 4,
            'review' => '',
        ]);

        return ['order' => $order, 'review' => $review];
    }

    #[Test]
    public function review_with_standard_punctuation_is_accepted(): void
    {
        $data = $this->createOrderWithReview();

        $response = $this->post(
            route('review.order.store', $data['order']->getUuid()),
            [
                'reviewText' => 'Heerlijk eten! Goed gekruid, op tijd klaar. Aanrader!',
                'reviewUuid' => $data['review']->getUuid(),
            ]
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('reviews', [
            'uuid' => $data['review']->getUuid(),
            'review' => 'Heerlijk eten! Goed gekruid, op tijd klaar. Aanrader!',
        ]);
    }

    #[Test]
    public function review_with_accented_characters_is_accepted(): void
    {
        $data = $this->createOrderWithReview();

        $response = $this->post(
            route('review.order.store', $data['order']->getUuid()),
            [
                'reviewText' => 'Très délicieux! Crème brûlée était fantastique.',
                'reviewUuid' => $data['review']->getUuid(),
            ]
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('reviews', [
            'uuid' => $data['review']->getUuid(),
            'review' => 'Très délicieux! Crème brûlée était fantastique.',
        ]);
    }

    #[Test]
    public function review_with_special_symbols_is_accepted(): void
    {
        $data = $this->createOrderWithReview();

        $response = $this->post(
            route('review.order.store', $data['order']->getUuid()),
            [
                'reviewText' => 'Prijs was €12,50 per portie. Goede prijs/kwaliteit!',
                'reviewUuid' => $data['review']->getUuid(),
            ]
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('reviews', [
            'uuid' => $data['review']->getUuid(),
            'review' => 'Prijs was €12,50 per portie. Goede prijs/kwaliteit!',
        ]);
    }

    #[Test]
    public function empty_review_is_rejected(): void
    {
        $data = $this->createOrderWithReview();

        $response = $this->post(
            route('review.order.store', $data['order']->getUuid()),
            [
                'reviewText' => '',
                'reviewUuid' => $data['review']->getUuid(),
            ]
        );

        $response->assertSessionHasErrors('reviewText');
    }

    #[Test]
    public function review_with_html_tags_is_rejected(): void
    {
        $data = $this->createOrderWithReview();

        $response = $this->post(
            route('review.order.store', $data['order']->getUuid()),
            [
                'reviewText' => '<script>alert("xss")</script>',
                'reviewUuid' => $data['review']->getUuid(),
            ]
        );

        $response->assertSessionHasErrors('reviewText');
    }

    #[Test]
    public function review_exceeding_max_length_is_rejected(): void
    {
        $data = $this->createOrderWithReview();

        $response = $this->post(
            route('review.order.store', $data['order']->getUuid()),
            [
                'reviewText' => str_repeat('a', 501),
                'reviewUuid' => $data['review']->getUuid(),
            ]
        );

        $response->assertSessionHasErrors('reviewText');
    }

    #[Test]
    public function review_at_max_length_is_accepted(): void
    {
        $data = $this->createOrderWithReview();
        $reviewText = str_repeat('a', 500);

        $response = $this->post(
            route('review.order.store', $data['order']->getUuid()),
            [
                'reviewText' => $reviewText,
                'reviewUuid' => $data['review']->getUuid(),
            ]
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('reviews', [
            'uuid' => $data['review']->getUuid(),
            'review' => $reviewText,
        ]);
    }
}
