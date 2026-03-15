<?php

namespace Database\Factories;

use App\Models\Advert;
use App\Models\Client;
use App\Models\Dish;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dish_uuid' => Dish::factory(),
            'client_uuid' => Client::factory(),
            'user_uuid' => User::factory(),
            'advert_uuid' => Advert::factory(),
            'portion_amount' => fake()->numberBetween(1, 5),
            'expected_pickup_time' => Carbon::tomorrow()->setTime(18, 0),
            'remarks' => fake()->optional()->sentence(),
            'payment_state' => Order::IN_PROCESS,
            'review_send' => null,
            'profile_deleted' => false,
            'payment_id' => 'tr_'.fake()->regexify('[A-Za-z0-9]{10}'),
        ];
    }

    /**
     * Create a paid order.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_state' => Order::SUCCEED,
            'status' => Order::STATUS_ACTIEF,
        ]);
    }

    /**
     * Create a cancelled order.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_state' => Order::CANCELLED,
            'status' => Order::STATUS_GEANNULEERD,
        ]);
    }

    /**
     * Create a cancelled order by client.
     */
    public function cancelledByClient(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_state' => Order::SUCCEED,
            'status' => Order::STATUS_GEANNULEERD,
            'cancelled_by' => Order::CANCELLED_BY_CLIENT,
        ]);
    }

    /**
     * Create a cancelled order by cook.
     */
    public function cancelledByCook(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_state' => Order::SUCCEED,
            'status' => Order::STATUS_GEANNULEERD,
            'cancelled_by' => Order::CANCELLED_BY_COOK,
        ]);
    }

    /**
     * Create a paid out order.
     */
    public function paidOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_state' => Order::PAID_OUT,
            'status' => Order::STATUS_ACTIEF,
        ]);
    }

    /**
     * Create an order with specific portion amount.
     */
    public function withPortions(int $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'portion_amount' => $amount,
        ]);
    }
}
