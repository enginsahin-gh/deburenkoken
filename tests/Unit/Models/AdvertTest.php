<?php

namespace Tests\Unit\Models;

use App\Models\Advert;
use App\Models\Cook;
use App\Models\Dish;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdvertTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function advert_belongs_to_dish(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);
        $dish = Dish::factory()->create([
            'user_uuid' => $user->uuid,
            'cook_uuid' => $cook->uuid,
        ]);

        $advert = Advert::create([
            'dish_uuid' => $dish->uuid,
            'portion_amount' => 10,
            'pickup_date' => Carbon::tomorrow()->format('Y-m-d'),
            'pickup_from' => '17:00',
            'pickup_to' => '19:00',
            'order_date' => Carbon::tomorrow()->format('Y-m-d'),
            'order_time' => '14:00',
        ]);

        $this->assertInstanceOf(Dish::class, $advert->dish);
        $this->assertEquals($dish->uuid, $advert->dish->uuid);
    }

    #[Test]
    public function advert_can_get_cook_through_dish(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);
        $dish = Dish::factory()->create([
            'user_uuid' => $user->uuid,
            'cook_uuid' => $cook->uuid,
        ]);

        $advert = Advert::create([
            'dish_uuid' => $dish->uuid,
            'portion_amount' => 10,
            'pickup_date' => Carbon::tomorrow()->format('Y-m-d'),
            'pickup_from' => '17:00',
            'pickup_to' => '19:00',
            'order_date' => Carbon::tomorrow()->format('Y-m-d'),
            'order_time' => '14:00',
        ]);

        $this->assertInstanceOf(Cook::class, $advert->cook);
        $this->assertEquals($cook->uuid, $advert->cook->uuid);
    }

    #[Test]
    public function advert_uses_uuid_as_primary_key(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create(['user_uuid' => $user->uuid]);

        $advert = Advert::create([
            'dish_uuid' => $dish->uuid,
            'portion_amount' => 10,
            'pickup_date' => Carbon::tomorrow()->format('Y-m-d'),
            'pickup_from' => '17:00',
            'pickup_to' => '19:00',
            'order_date' => Carbon::tomorrow()->format('Y-m-d'),
            'order_time' => '14:00',
        ]);

        $this->assertNotNull($advert->uuid);
        $this->assertIsString($advert->uuid);
        $this->assertEquals(36, strlen($advert->uuid));
    }

    #[Test]
    public function advert_can_be_published(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create(['user_uuid' => $user->uuid]);

        $advert = Advert::create([
            'dish_uuid' => $dish->uuid,
            'portion_amount' => 10,
            'pickup_date' => Carbon::tomorrow()->format('Y-m-d'),
            'pickup_from' => '17:00',
            'pickup_to' => '19:00',
            'order_date' => Carbon::tomorrow()->format('Y-m-d'),
            'order_time' => '14:00',
            'published' => null,
        ]);

        $this->assertNull($advert->published);

        $advert->update(['published' => now()]);

        $this->assertNotNull($advert->fresh()->published);
    }

    #[Test]
    public function advert_can_be_soft_deleted(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create(['user_uuid' => $user->uuid]);

        $advert = Advert::create([
            'dish_uuid' => $dish->uuid,
            'portion_amount' => 10,
            'pickup_date' => Carbon::tomorrow()->format('Y-m-d'),
            'pickup_from' => '17:00',
            'pickup_to' => '19:00',
            'order_date' => Carbon::tomorrow()->format('Y-m-d'),
            'order_time' => '14:00',
        ]);

        $advert->delete();

        $this->assertSoftDeleted($advert);
        $this->assertNotNull(Advert::withTrashed()->find($advert->uuid));
    }

    #[Test]
    public function advert_portion_amount_is_limited_to_25(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create(['user_uuid' => $user->uuid]);

        // Test max 25 portions
        $advert = Advert::create([
            'dish_uuid' => $dish->uuid,
            'portion_amount' => 25,
            'pickup_date' => Carbon::tomorrow()->format('Y-m-d'),
            'pickup_from' => '17:00',
            'pickup_to' => '19:00',
            'order_date' => Carbon::tomorrow()->format('Y-m-d'),
            'order_time' => '14:00',
        ]);

        $this->assertEquals(25, $advert->portion_amount);
    }

    #[Test]
    public function advert_returns_parsed_uuid(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create(['user_uuid' => $user->uuid]);

        $advert = Advert::create([
            'dish_uuid' => $dish->uuid,
            'portion_amount' => 10,
            'pickup_date' => Carbon::tomorrow()->format('Y-m-d'),
            'pickup_from' => '17:00',
            'pickup_to' => '19:00',
            'order_date' => Carbon::tomorrow()->format('Y-m-d'),
            'order_time' => '14:00',
        ]);

        $parsedUuid = $advert->getParsedUuid();
        $this->assertEquals(6, strlen($parsedUuid));
        $this->assertEquals(substr($advert->uuid, -6), $parsedUuid);
    }

    #[Test]
    public function advert_can_have_orders(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create(['user_uuid' => $user->uuid]);

        $advert = Advert::create([
            'dish_uuid' => $dish->uuid,
            'portion_amount' => 10,
            'pickup_date' => Carbon::tomorrow()->format('Y-m-d'),
            'pickup_from' => '17:00',
            'pickup_to' => '19:00',
            'order_date' => Carbon::tomorrow()->format('Y-m-d'),
            'order_time' => '14:00',
        ]);

        $this->assertCount(0, $advert->order);
    }
}
