<?php

namespace Tests\Unit\Models;

use App\Models\Advert;
use App\Models\Client;
use App\Models\Dish;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function order_has_correct_payment_state_constants(): void
    {
        $this->assertEquals(1, Order::IN_PROCESS);
        $this->assertEquals(2, Order::SUCCEED);
        $this->assertEquals(3, Order::FAILED);
        $this->assertEquals(4, Order::CANCELLED);
        $this->assertEquals(5, Order::PAID_OUT);
        $this->assertEquals(6, Order::PAYOUT_PENDING);
    }

    #[Test]
    public function order_has_correct_status_constants(): void
    {
        $this->assertEquals('Actief', Order::STATUS_ACTIEF);
        $this->assertEquals('Verlopen', Order::STATUS_VERLOPEN);
        $this->assertEquals('Geannuleerd', Order::STATUS_GEANNULEERD);
    }

    #[Test]
    public function order_has_correct_cancelled_by_constants(): void
    {
        $this->assertEquals('client', Order::CANCELLED_BY_CLIENT);
        $this->assertEquals('cook', Order::CANCELLED_BY_COOK);
    }

    #[Test]
    public function order_belongs_to_dish(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create(['user_uuid' => $user->uuid]);
        $client = Client::factory()->create();
        $advert = Advert::factory()->create(['dish_uuid' => $dish->uuid]);

        $order = Order::create([
            'dish_uuid' => $dish->uuid,
            'client_uuid' => $client->uuid,
            'user_uuid' => $user->uuid,
            'advert_uuid' => $advert->uuid,
            'portion_amount' => 2,
            'expected_pickup_time' => now()->addDay(),
            'payment_state' => Order::IN_PROCESS,
        ]);

        $this->assertInstanceOf(Dish::class, $order->dish);
        $this->assertEquals($dish->uuid, $order->dish->uuid);
    }

    #[Test]
    public function order_belongs_to_client(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create(['user_uuid' => $user->uuid]);
        $client = Client::factory()->create();
        $advert = Advert::factory()->create(['dish_uuid' => $dish->uuid]);

        $order = Order::create([
            'dish_uuid' => $dish->uuid,
            'client_uuid' => $client->uuid,
            'user_uuid' => $user->uuid,
            'advert_uuid' => $advert->uuid,
            'portion_amount' => 2,
            'expected_pickup_time' => now()->addDay(),
            'payment_state' => Order::IN_PROCESS,
        ]);

        $this->assertInstanceOf(Client::class, $order->client);
        $this->assertEquals($client->uuid, $order->client->uuid);
    }

    #[Test]
    public function order_belongs_to_advert(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create(['user_uuid' => $user->uuid]);
        $client = Client::factory()->create();
        $advert = Advert::factory()->create(['dish_uuid' => $dish->uuid]);

        $order = Order::create([
            'dish_uuid' => $dish->uuid,
            'client_uuid' => $client->uuid,
            'user_uuid' => $user->uuid,
            'advert_uuid' => $advert->uuid,
            'portion_amount' => 2,
            'expected_pickup_time' => now()->addDay(),
            'payment_state' => Order::IN_PROCESS,
        ]);

        $this->assertInstanceOf(Advert::class, $order->advert);
        $this->assertEquals($advert->uuid, $order->advert->uuid);
    }

    #[Test]
    public function order_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create(['user_uuid' => $user->uuid]);
        $client = Client::factory()->create();
        $advert = Advert::factory()->create(['dish_uuid' => $dish->uuid]);

        $order = Order::create([
            'dish_uuid' => $dish->uuid,
            'client_uuid' => $client->uuid,
            'user_uuid' => $user->uuid,
            'advert_uuid' => $advert->uuid,
            'portion_amount' => 2,
            'expected_pickup_time' => now()->addDay(),
            'payment_state' => Order::IN_PROCESS,
        ]);

        $this->assertInstanceOf(User::class, $order->user);
        $this->assertEquals($user->uuid, $order->user->uuid);
    }

    #[Test]
    public function order_uses_uuid_as_primary_key(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create(['user_uuid' => $user->uuid]);
        $client = Client::factory()->create();
        $advert = Advert::factory()->create(['dish_uuid' => $dish->uuid]);

        $order = Order::create([
            'dish_uuid' => $dish->uuid,
            'client_uuid' => $client->uuid,
            'user_uuid' => $user->uuid,
            'advert_uuid' => $advert->uuid,
            'portion_amount' => 2,
            'expected_pickup_time' => now()->addDay(),
            'payment_state' => Order::IN_PROCESS,
        ]);

        $this->assertNotNull($order->uuid);
        $this->assertIsString($order->uuid);
        $this->assertEquals(36, strlen($order->uuid)); // UUID format
    }

    #[Test]
    public function order_can_be_soft_deleted(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create(['user_uuid' => $user->uuid]);
        $client = Client::factory()->create();
        $advert = Advert::factory()->create(['dish_uuid' => $dish->uuid]);

        $order = Order::create([
            'dish_uuid' => $dish->uuid,
            'client_uuid' => $client->uuid,
            'user_uuid' => $user->uuid,
            'advert_uuid' => $advert->uuid,
            'portion_amount' => 2,
            'expected_pickup_time' => now()->addDay(),
            'payment_state' => Order::IN_PROCESS,
        ]);

        $order->delete();

        $this->assertSoftDeleted($order);
        $this->assertNotNull(Order::withTrashed()->find($order->uuid));
    }

    #[Test]
    public function order_returns_parsed_uuid(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create(['user_uuid' => $user->uuid]);
        $client = Client::factory()->create();
        $advert = Advert::factory()->create(['dish_uuid' => $dish->uuid]);

        $order = Order::create([
            'dish_uuid' => $dish->uuid,
            'client_uuid' => $client->uuid,
            'user_uuid' => $user->uuid,
            'advert_uuid' => $advert->uuid,
            'portion_amount' => 2,
            'expected_pickup_time' => now()->addDay(),
            'payment_state' => Order::IN_PROCESS,
        ]);

        // getParsedOrderUuid should return last 6 characters with prefix
        $parsedUuid = $order->getParsedOrderUuid();
        $this->assertStringContainsString('Bestelnummer:', $parsedUuid);
    }

    #[Test]
    public function order_returns_correct_status_for_active_order(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create(['user_uuid' => $user->uuid]);
        $client = Client::factory()->create();
        $advert = Advert::factory()->create(['dish_uuid' => $dish->uuid]);

        $order = Order::create([
            'dish_uuid' => $dish->uuid,
            'client_uuid' => $client->uuid,
            'user_uuid' => $user->uuid,
            'advert_uuid' => $advert->uuid,
            'portion_amount' => 2,
            'expected_pickup_time' => now()->addDay(),
            'payment_state' => Order::SUCCEED,
            'status' => Order::STATUS_ACTIEF,
        ]);

        $this->assertEquals(Order::STATUS_ACTIEF, $order->getStatus());
    }

    #[Test]
    public function order_returns_correct_status_for_cancelled_order(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create(['user_uuid' => $user->uuid]);
        $client = Client::factory()->create();
        $advert = Advert::factory()->create(['dish_uuid' => $dish->uuid]);

        $order = Order::create([
            'dish_uuid' => $dish->uuid,
            'client_uuid' => $client->uuid,
            'user_uuid' => $user->uuid,
            'advert_uuid' => $advert->uuid,
            'portion_amount' => 2,
            'expected_pickup_time' => now()->addDay(),
            'payment_state' => Order::CANCELLED,
        ]);

        // Set status directly since it's not in fillable
        $order->status = Order::STATUS_GEANNULEERD;
        $order->save();

        $this->assertEquals(Order::STATUS_GEANNULEERD, $order->getStatus());
    }
}
