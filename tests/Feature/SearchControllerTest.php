<?php

namespace Tests\Feature;

use App\Models\Advert;
use App\Models\Cook;
use App\Models\Dish;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function search_page_requires_plaats_parameter(): void
    {
        $response = $this->get(route('search.coordinates'));

        $response->assertStatus(302); // Redirects due to validation
    }

    #[Test]
    public function search_by_coordinates_returns_results(): void
    {
        // Create a cook with a published advert
        $user = User::factory()->create();
        $cook = Cook::factory()->inSliedrecht()->create(['user_uuid' => $user->uuid]);
        $dish = Dish::factory()->create([
            'user_uuid' => $user->uuid,
            'cook_uuid' => $cook->uuid,
            'portion_price' => 10.00,
        ]);
        $advert = Advert::factory()->published()->create([
            'dish_uuid' => $dish->uuid,
        ]);

        $response = $this->get(route('search.coordinates', [
            'plaats' => 'Sliedrecht',
            'latitude' => 51.8248681,
            'longitude' => 4.773162399999999,
            'distance' => 10,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('customer.available');
        $response->assertViewHas('adverts');
    }

    #[Test]
    public function search_by_cook_name_returns_results(): void
    {
        $user = User::factory()->create(['username' => 'thuiskok_test']);
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        $response = $this->get(route('search.cooks', [
            'username' => 'thuiskok',
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('search-cooks');
        $response->assertViewHas('cooks');
    }

    #[Test]
    public function search_cook_by_distance_returns_view(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->inSliedrecht()->create(['user_uuid' => $user->uuid]);

        $response = $this->get(route('search.cooks.distance', [
            'latitude' => 51.8248681,
            'longitude' => 4.773162399999999,
            'distance' => 100,
            'city' => 'Sliedrecht',
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('search-cooks-distance');
        $response->assertViewHas('cooks');
    }

    #[Test]
    public function cook_details_page_shows_cook_information(): void
    {
        $user = User::factory()->create(['username' => 'testkook']);
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        $response = $this->get(route('search.cooks.detail', ['uuid' => $cook->uuid]));

        $response->assertStatus(200);
        $response->assertViewIs('detail-cook');
        $response->assertViewHas('cook');
    }

    #[Test]
    public function cook_reviews_page_shows_reviews(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        $response = $this->get(route('search.cooks.detail.review', ['uuid' => $cook->uuid]));

        $response->assertStatus(200);
        $response->assertViewIs('review-cook');
        $response->assertViewHas('cook');
    }

    #[Test]
    public function search_filters_by_price_range(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->inSliedrecht()->create(['user_uuid' => $user->uuid]);

        // Create a cheap dish
        $cheapDish = Dish::factory()->create([
            'user_uuid' => $user->uuid,
            'cook_uuid' => $cook->uuid,
            'portion_price' => 5.00,
        ]);
        Advert::factory()->published()->create(['dish_uuid' => $cheapDish->uuid]);

        // Create an expensive dish
        $expensiveDish = Dish::factory()->create([
            'user_uuid' => $user->uuid,
            'cook_uuid' => $cook->uuid,
            'portion_price' => 20.00,
        ]);
        Advert::factory()->published()->create(['dish_uuid' => $expensiveDish->uuid]);

        $response = $this->get(route('search.coordinates', [
            'plaats' => 'Sliedrecht',
            'latitude' => 51.8248681,
            'longitude' => 4.773162399999999,
            'distance' => 10,
            'price_from' => 0,
            'price_to' => 10,
        ]));

        $response->assertStatus(200);
    }

    #[Test]
    public function search_can_filter_vegetarian_dishes(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->inSliedrecht()->create(['user_uuid' => $user->uuid]);

        $vegetarianDish = Dish::factory()->vegetarian()->create([
            'user_uuid' => $user->uuid,
            'cook_uuid' => $cook->uuid,
        ]);
        Advert::factory()->published()->create(['dish_uuid' => $vegetarianDish->uuid]);

        $response = $this->get(route('search.coordinates', [
            'plaats' => 'Sliedrecht',
            'latitude' => 51.8248681,
            'longitude' => 4.773162399999999,
            'distance' => 10,
            'specs' => ['vegetarian'],
        ]));

        $response->assertStatus(200);
    }

    #[Test]
    public function search_reset_clears_filters(): void
    {
        $response = $this->get(route('search.coordinates', [
            'plaats' => 'Sliedrecht',
            'latitude' => 51.8248681,
            'longitude' => 4.773162399999999,
            'reset' => true,
        ]));

        $response->assertStatus(200);
    }
}
