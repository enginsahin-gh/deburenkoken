<?php

namespace Database\Factories;

use App\Models\Cook;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cook>
 */
class CookFactory extends Factory
{
    protected $model = Cook::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_uuid' => User::factory(),
            'lat' => fake()->latitude(51.0, 53.0), // Nederland range
            'long' => fake()->longitude(3.0, 7.0),
            'street' => fake()->streetName(),
            'house_number' => fake()->numberBetween(1, 999),
            'addition' => null,
            'postal_code' => fake()->numerify('####').fake()->randomLetter().fake()->randomLetter(), // Dutch format: 1234AB
            'city' => fake()->city(),
            'country' => 'NL',
            'description' => fake()->paragraph(),
            'mail_order' => true,
            'mail_cancel' => true,
            'mail_self' => false,
        ];
    }

    /**
     * Create a cook in Sliedrecht (test location).
     */
    public function inSliedrecht(): static
    {
        return $this->state(fn (array $attributes) => [
            'lat' => 51.8248681,
            'long' => 4.773162399999999,
            'street' => 'Kerkbuurt',
            'house_number' => '1',
            'postal_code' => '3361AB',
            'city' => 'Sliedrecht',
        ]);
    }
}
