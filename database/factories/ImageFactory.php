<?php

namespace Database\Factories;

use App\Models\Dish;
use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    protected $model = Image::class;

    public function definition(): array
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create(['user_uuid' => $user->getUuid()]);

        return [
            'user_uuid' => $user->getUuid(),
            'dish_uuid' => $dish->getUuid(),
            'type_id' => Image::DISH_IMAGE,
            'main_picture' => true,
            'path' => 'img/dishes/'.$this->faker->uuid(),
            'name' => $this->faker->uuid().'.jpg',
            'description' => $this->faker->sentence(),
            'type' => 'image/jpeg',
        ];
    }

    /**
     * Maak een afbeelding voor een specifiek gerecht.
     */
    public function forDish(Dish $dish): static
    {
        return $this->state(fn (array $attributes) => [
            'user_uuid' => $dish->getUserUuid(),
            'dish_uuid' => $dish->getUuid(),
            'type_id' => Image::DISH_IMAGE,
        ]);
    }

    /**
     * Maak een profiel afbeelding voor een gebruiker.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_uuid' => $user->getUuid(),
            'dish_uuid' => null,
            'type_id' => Image::PROFILE_IMAGE,
        ]);
    }

    /**
     * Maak een hoofdafbeelding.
     */
    public function asMain(): static
    {
        return $this->state(fn (array $attributes) => [
            'main_picture' => true,
        ]);
    }

    /**
     * Maak een niet-hoofd afbeelding.
     */
    public function notMain(): static
    {
        return $this->state(fn (array $attributes) => [
            'main_picture' => false,
        ]);
    }
}
