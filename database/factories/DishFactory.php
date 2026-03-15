<?php

namespace Database\Factories;

use App\Models\Cook;
use App\Models\Dish;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dish>
 */
class DishFactory extends Factory
{
    protected $model = Dish::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_uuid' => User::factory(),
            'cook_uuid' => Cook::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'is_vegetarian' => fake()->boolean(30),
            'is_vegan' => fake()->boolean(10),
            'is_halal' => fake()->boolean(20),
            'has_alcohol' => fake()->boolean(5),
            'has_gluten' => fake()->boolean(50),
            'has_lactose' => fake()->boolean(40),
            'spice_level' => fake()->numberBetween(0, 3),
            'portion_price' => fake()->randomFloat(2, 5.00, 25.00),
        ];
    }

    /**
     * Create a vegetarian dish.
     */
    public function vegetarian(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_vegetarian' => 1,
            'is_vegan' => 0,
        ]);
    }

    /**
     * Create a vegan dish.
     */
    public function vegan(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_vegetarian' => 1,
            'is_vegan' => 1,
        ]);
    }

    /**
     * Create a dish with specific price.
     */
    public function withPrice(float $price): static
    {
        return $this->state(fn (array $attributes) => [
            'portion_price' => $price,
        ]);
    }
}
