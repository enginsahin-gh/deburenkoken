<?php

namespace Tests\Unit\Repositories;

use App\Dtos\DishDto;
use App\Models\Advert;
use App\Models\Cook;
use App\Models\Dish;
use App\Models\User;
use App\Repositories\DishRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DishRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private DishRepository $dishRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dishRepository = app(DishRepository::class);
    }

    #[Test]
    public function it_can_find_dish_by_uuid(): void
    {
        $dish = Dish::factory()->create();

        $found = $this->dishRepository->find($dish->getUuid());

        $this->assertNotNull($found);
        $this->assertEquals($dish->getUuid(), $found->getUuid());
    }

    #[Test]
    public function it_returns_null_when_dish_not_found(): void
    {
        $result = $this->dishRepository->find('non-existent-uuid');

        $this->assertNull($result);
    }

    #[Test]
    public function it_can_get_all_dishes(): void
    {
        Dish::factory()->count(5)->create();

        $dishes = $this->dishRepository->get();

        $this->assertCount(5, $dishes);
    }

    #[Test]
    public function it_can_create_dish(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->getUuid()]);

        $dishDto = new DishDto(
            user: $user,
            title: 'Pasta Carbonara',
            description: 'Heerlijke Italiaanse pasta',
            vegetarian: false,
            vegan: false,
            halal: false,
            alcohol: false,
            gluten: true,
            lactose: true,
            spiceLevel: 1,
            cook: $cook,
            portionPrice: 12.50
        );

        $dish = $this->dishRepository->create($dishDto);

        $this->assertInstanceOf(Dish::class, $dish);
        $this->assertEquals('Pasta Carbonara', $dish->getTitle());
        $this->assertEquals('Heerlijke Italiaanse pasta', $dish->getDescription());
        $this->assertEquals(12.50, $dish->getPortionPrice());
        $this->assertFalse($dish->isVegetarian());
        $this->assertTrue($dish->hasGluten());
    }

    #[Test]
    public function it_can_create_vegetarian_dish(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->getUuid()]);

        $dishDto = new DishDto(
            user: $user,
            title: 'Groentecurry',
            description: 'Vegetarische curry',
            vegetarian: true,
            vegan: false,
            halal: true,
            alcohol: false,
            gluten: false,
            lactose: true,
            spiceLevel: 2,
            cook: $cook,
            portionPrice: 10.00
        );

        $dish = $this->dishRepository->create($dishDto);

        $this->assertTrue($dish->isVegetarian());
        $this->assertFalse($dish->isVegan());
        $this->assertTrue($dish->isHalal());
    }

    #[Test]
    public function it_can_update_dish(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->getUuid()]);
        $dish = Dish::factory()->create([
            'user_uuid' => $user->getUuid(),
            'cook_uuid' => $cook->getUuid(),
            'title' => 'Old Title',
            'portion_price' => 10.00,
        ]);

        $dishDto = new DishDto(
            user: $user,
            title: 'New Title',
            description: 'Updated description',
            vegetarian: true,
            vegan: false,
            halal: false,
            alcohol: false,
            gluten: false,
            lactose: false,
            spiceLevel: 0,
            cook: $cook,
            portionPrice: 15.00
        );

        $updated = $this->dishRepository->update($dishDto, $dish->getUuid());

        $this->assertEquals('New Title', $updated->getTitle());
        $this->assertEquals(15.00, $updated->getPortionPrice());
        $this->assertTrue($updated->isVegetarian());
    }

    #[Test]
    public function it_returns_null_when_updating_non_existent_dish(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->getUuid()]);

        $dishDto = new DishDto(
            user: $user,
            title: 'Title',
            description: 'Description',
            cook: $cook,
            portionPrice: 10.00
        );

        $result = $this->dishRepository->update($dishDto, 'non-existent-uuid');

        $this->assertNull($result);
    }

    #[Test]
    public function it_can_delete_dish(): void
    {
        $dish = Dish::factory()->create();

        $result = $this->dishRepository->delete($dish->getUuid());

        $this->assertTrue($result);
        $this->assertSoftDeleted('dishes', ['uuid' => $dish->getUuid()]);
    }

    #[Test]
    public function it_can_get_dishes_for_user(): void
    {
        $user = User::factory()->create();
        Dish::factory()->count(3)->create(['user_uuid' => $user->getUuid()]);
        Dish::factory()->count(2)->create(); // Other user's dishes

        $dishes = $this->dishRepository->getDishesForUser($user);

        $this->assertEquals(3, $dishes->total());
    }

    #[Test]
    public function it_can_get_dishes_by_user_uuid(): void
    {
        $user = User::factory()->create();
        Dish::factory()->count(4)->create(['user_uuid' => $user->getUuid()]);
        Dish::factory()->count(3)->create(); // Other user's dishes

        $dishes = $this->dishRepository->getDishesByUserUuid($user);

        $this->assertCount(4, $dishes);
    }

    #[Test]
    public function it_can_update_cook_uuid(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create([
            'user_uuid' => $user->getUuid(),
            'cook_uuid' => null,
        ]);
        $cook = Cook::factory()->create(['user_uuid' => $user->getUuid()]);

        $result = $this->dishRepository->updateCookUuid($dish, $cook);

        $this->assertTrue($result);
        $dish->refresh();
        $this->assertEquals($cook->getUuid(), $dish->cook_uuid);
    }

    #[Test]
    public function dish_portion_price_is_required(): void
    {
        $dish = Dish::factory()->withPrice(8.50)->create();

        $this->assertEquals(8.50, $dish->getPortionPrice());
    }

    #[Test]
    public function dish_price_can_be_minimum(): void
    {
        $dish = Dish::factory()->withPrice(0.50)->create();

        $this->assertEquals(0.50, $dish->getPortionPrice());
    }

    #[Test]
    public function dish_price_can_be_maximum(): void
    {
        $dish = Dish::factory()->withPrice(25.00)->create();

        $this->assertEquals(25.00, $dish->getPortionPrice());
    }
}
