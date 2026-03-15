<?php

namespace Tests\Unit\Models;

use App\Models\Cook;
use App\Models\Dish;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DishTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function dish_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create(['user_uuid' => $user->uuid]);

        $this->assertInstanceOf(User::class, $dish->user);
        $this->assertEquals($user->uuid, $dish->user->uuid);
    }

    #[Test]
    public function dish_belongs_to_cook(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);
        $dish = Dish::factory()->create([
            'user_uuid' => $user->uuid,
            'cook_uuid' => $cook->uuid,
        ]);

        $this->assertInstanceOf(Cook::class, $dish->cook);
        $this->assertEquals($cook->uuid, $dish->cook->uuid);
    }

    #[Test]
    public function dish_uses_uuid_as_primary_key(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create(['user_uuid' => $user->uuid]);

        $this->assertNotNull($dish->uuid);
        $this->assertIsString($dish->uuid);
        $this->assertEquals(36, strlen($dish->uuid));
    }

    #[Test]
    public function dish_can_be_soft_deleted(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create(['user_uuid' => $user->uuid]);

        $dish->delete();

        $this->assertSoftDeleted($dish);
        $this->assertNotNull(Dish::withTrashed()->find($dish->uuid));
    }

    #[Test]
    public function dish_has_portion_price(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create([
            'user_uuid' => $user->uuid,
            'portion_price' => 12.50,
        ]);

        $this->assertEquals(12.50, $dish->getPortionPrice());
    }

    #[Test]
    public function dish_can_be_vegetarian(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->vegetarian()->create(['user_uuid' => $user->uuid]);

        $this->assertTrue($dish->isVegetarian());
    }

    #[Test]
    public function dish_can_be_vegan(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->vegan()->create(['user_uuid' => $user->uuid]);

        $this->assertTrue($dish->isVegan());
    }

    #[Test]
    public function dish_can_have_adverts(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create(['user_uuid' => $user->uuid]);

        $this->assertCount(0, $dish->adverts);
    }

    #[Test]
    public function dish_portion_price_must_be_between_0_50_and_25(): void
    {
        $user = User::factory()->create();

        // Test minimum price (€0.50)
        $dishMin = Dish::factory()->create([
            'user_uuid' => $user->uuid,
            'portion_price' => 0.50,
        ]);
        $this->assertEquals(0.50, $dishMin->getPortionPrice());

        // Test maximum price (€25.00)
        $dishMax = Dish::factory()->create([
            'user_uuid' => $user->uuid,
            'portion_price' => 25.00,
        ]);
        $this->assertEquals(25.00, $dishMax->getPortionPrice());
    }

    #[Test]
    public function dish_returns_parsed_uuid(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create(['user_uuid' => $user->uuid]);

        $parsedUuid = $dish->getParsedUuid();
        $this->assertEquals(6, strlen($parsedUuid));
        $this->assertEquals(substr($dish->uuid, -6), $parsedUuid);
    }
}
