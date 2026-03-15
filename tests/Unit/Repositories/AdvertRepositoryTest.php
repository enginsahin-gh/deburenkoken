<?php

namespace Tests\Unit\Repositories;

use App\Dtos\AdvertDto;
use App\Models\Advert;
use App\Models\Cook;
use App\Models\Dish;
use App\Models\User;
use App\Repositories\AdvertRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdvertRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private AdvertRepository $advertRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->advertRepository = app(AdvertRepository::class);
    }

    #[Test]
    public function it_can_find_advert_by_uuid(): void
    {
        $advert = Advert::factory()->create();

        $found = $this->advertRepository->find($advert->getUuid());

        $this->assertNotNull($found);
        $this->assertEquals($advert->getUuid(), $found->getUuid());
    }

    #[Test]
    public function it_eager_loads_dish_when_finding_advert(): void
    {
        $advert = Advert::factory()->create();

        $found = $this->advertRepository->find($advert->getUuid());

        // Dish should be eager loaded
        $this->assertTrue($found->relationLoaded('dish'));
        $this->assertNotNull($found->dish);
    }

    #[Test]
    public function it_returns_null_when_advert_not_found(): void
    {
        $result = $this->advertRepository->find('non-existent-uuid');

        $this->assertNull($result);
    }

    #[Test]
    public function it_can_create_advert(): void
    {
        $dish = Dish::factory()->create();
        $pickupDate = Carbon::tomorrow();

        $advertDto = new AdvertDto(
            $dish,
            5,
            $pickupDate->format('Y-m-d'),
            '17:00',
            '19:00',
            $pickupDate->format('Y-m-d'),
            '16:00',
            false
        );

        $advert = $this->advertRepository->create($advertDto);

        $this->assertInstanceOf(Advert::class, $advert);
        $this->assertEquals($dish->getUuid(), $advert->dish_uuid);
        $this->assertEquals(5, $advert->portion_amount);
        $this->assertStringContainsString('17:00', $advert->pickup_from);
        $this->assertStringContainsString('19:00', $advert->pickup_to);
        $this->assertNull($advert->published);
    }

    #[Test]
    public function it_can_create_published_advert(): void
    {
        $dish = Dish::factory()->create();
        $pickupDate = Carbon::tomorrow();

        $advertDto = new AdvertDto(
            $dish,
            5,
            $pickupDate->format('Y-m-d'),
            '17:00',
            '19:00',
            $pickupDate->format('Y-m-d'),
            '16:00',
            true // publish = true
        );

        $advert = $this->advertRepository->create($advertDto);

        $this->assertNotNull($advert->published);
    }

    #[Test]
    public function it_can_publish_advert(): void
    {
        $advert = Advert::factory()->create(['published' => null]);

        $this->assertNull($advert->published);

        $published = $this->advertRepository->publishAdvert($advert->getUuid());

        $this->assertNotNull($published->published);
    }

    #[Test]
    public function it_can_update_advert(): void
    {
        $advert = Advert::factory()->create(['portion_amount' => 5]);
        $dish = $advert->dish;

        $pickupDate = Carbon::tomorrow()->addDay();
        $advertDto = new AdvertDto(
            $dish,
            10, // Changed portion amount
            $pickupDate->format('Y-m-d'),
            '18:00',
            '20:00',
            $pickupDate->format('Y-m-d'),
            '17:00',
            false
        );

        $updated = $this->advertRepository->update($advertDto, $advert->getUuid());

        $this->assertEquals(10, $updated->portion_amount);
        $this->assertStringContainsString('18:00', $updated->pickup_from);
    }

    #[Test]
    public function it_can_delete_advert(): void
    {
        $advert = Advert::factory()->create();

        $result = $this->advertRepository->delete($advert->getUuid());

        $this->assertTrue($result);
        $this->assertSoftDeleted('adverts', ['uuid' => $advert->getUuid()]);
    }

    #[Test]
    public function it_can_get_adverts_for_dish(): void
    {
        $dish = Dish::factory()->create();
        Advert::factory()->count(3)->create(['dish_uuid' => $dish->getUuid()]);
        Advert::factory()->count(2)->create(); // Other adverts

        $adverts = $this->advertRepository->findAdvertsForDish($dish->getUuid());

        $this->assertCount(3, $adverts);
    }

    #[Test]
    public function it_can_get_users_adverts(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->getUuid()]);
        $dish = Dish::factory()->create(['user_uuid' => $user->getUuid()]);

        // Create future adverts
        $pickupDate = Carbon::tomorrow();
        Advert::factory()->count(2)->create([
            'dish_uuid' => $dish->getUuid(),
            'pickup_date' => $pickupDate->format('Y-m-d'),
        ]);

        // Create past adverts (should be excluded by default)
        Advert::factory()->create([
            'dish_uuid' => $dish->getUuid(),
            'pickup_date' => Carbon::yesterday()->format('Y-m-d'),
        ]);

        $adverts = $this->advertRepository->getUsersAdverts(
            $user->getUuid(),
            Carbon::today()->format('Y-m-d'),
            Carbon::tomorrow()->addWeek()->format('Y-m-d')
        );

        $this->assertCount(2, $adverts);
    }

    #[Test]
    public function it_can_get_active_adverts_count(): void
    {
        // Create published future adverts
        $pickupDate = Carbon::tomorrow();
        Advert::factory()->count(3)->create([
            'published' => Carbon::now(),
            'pickup_date' => $pickupDate->format('Y-m-d'),
        ]);

        // Create unpublished adverts (should not count)
        Advert::factory()->count(2)->create([
            'published' => null,
            'pickup_date' => $pickupDate->format('Y-m-d'),
        ]);

        $count = $this->advertRepository->getActiveAdvertsCount();

        $this->assertEquals(3, $count);
    }

    #[Test]
    public function it_can_profile_delete_advert(): void
    {
        $advert = Advert::factory()->create(['profile_deleted' => false]);

        $result = $this->advertRepository->profileDelete($advert);

        $this->assertTrue($result->profile_deleted);
    }

    #[Test]
    public function it_can_find_advert_with_soft_deleted(): void
    {
        $advert = Advert::factory()->create();
        $advert->delete();

        // Should still find soft-deleted adverts
        $found = $this->advertRepository->find($advert->getUuid());

        $this->assertNotNull($found);
        $this->assertNotNull($found->deleted_at);
    }

    #[Test]
    public function it_can_get_cancelled_adverts_by_user(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create(['user_uuid' => $user->getUuid()]);

        // Create and soft delete adverts
        $advert1 = Advert::factory()->create(['dish_uuid' => $dish->getUuid()]);
        $advert2 = Advert::factory()->create(['dish_uuid' => $dish->getUuid()]);
        $advert1->delete();
        $advert2->delete();

        // Create active advert (should not be included)
        Advert::factory()->create(['dish_uuid' => $dish->getUuid()]);

        $cancelled = $this->advertRepository->getCancelledAdvertsByUser($user->getUuid());

        $this->assertCount(2, $cancelled);
    }
}
