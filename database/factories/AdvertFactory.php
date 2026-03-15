<?php

namespace Database\Factories;

use App\Models\Advert;
use App\Models\Dish;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Advert>
 */
class AdvertFactory extends Factory
{
    protected $model = Advert::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pickupDate = Carbon::tomorrow();

        return [
            'dish_uuid' => Dish::factory(),
            'portion_amount' => fake()->numberBetween(5, 25),
            'pickup_date' => $pickupDate->format('Y-m-d'),
            'pickup_from' => '17:00',
            'pickup_to' => '19:00',
            'order_date' => $pickupDate->format('Y-m-d'),
            'order_time' => '14:00',
            'published' => null, // Not published by default
            'profile_deleted' => false,
            'preparation_email_sent' => false,
        ];
    }

    /**
     * Create a published advert.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => now(),
        ]);
    }

    /**
     * Create an advert for a specific date.
     */
    public function forDate(Carbon $date): static
    {
        return $this->state(fn (array $attributes) => [
            'pickup_date' => $date->format('Y-m-d'),
            'order_date' => $date->format('Y-m-d'),
        ]);
    }

    /**
     * Create an advert with specific portion amount.
     */
    public function withPortions(int $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'portion_amount' => $amount,
        ]);
    }

    /**
     * Create an expired advert (past pickup date).
     */
    public function expired(): static
    {
        $pastDate = Carbon::yesterday();

        return $this->state(fn (array $attributes) => [
            'pickup_date' => $pastDate->format('Y-m-d'),
            'order_date' => $pastDate->format('Y-m-d'),
            'published' => $pastDate->subDay(),
        ]);
    }
}
