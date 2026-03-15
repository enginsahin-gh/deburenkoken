<?php

namespace Tests\Unit\Repositories;

use App\Dtos\ImageDto;
use App\Models\Cook;
use App\Models\Dish;
use App\Models\Image;
use App\Models\User;
use App\Repositories\ImageRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests voor ImageRepository
 *
 * BL-211: Bug fix voor verdwijnen van gerecht afbeeldingen
 */
class ImageRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ImageRepository $imageRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->imageRepository = app(ImageRepository::class);
    }

    #[Test]
    public function it_can_find_image_by_uuid(): void
    {
        $image = Image::factory()->create();

        $found = $this->imageRepository->find($image->getUuid());

        $this->assertNotNull($found);
        $this->assertEquals($image->getUuid(), $found->getUuid());
    }

    #[Test]
    public function it_can_find_soft_deleted_image(): void
    {
        $image = Image::factory()->create();
        $image->delete();

        $found = $this->imageRepository->find($image->getUuid());

        $this->assertNotNull($found);
        $this->assertNotNull($found->deleted_at);
    }

    #[Test]
    public function it_can_create_image(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->getUuid()]);
        $dish = Dish::factory()->create([
            'user_uuid' => $user->getUuid(),
            'cook_uuid' => $cook->getUuid(),
        ]);

        $imageDto = new ImageDto(
            userUuid: $user->getUuid(),
            path: 'img/dishes/'.$cook->getUuid(),
            name: 'test_image.jpg',
            description: 'Test beschrijving',
            type: 'image/jpeg',
            dish: $dish,
            typeId: Image::DISH_IMAGE,
            mainPicture: true
        );

        $image = $this->imageRepository->create($imageDto);

        $this->assertInstanceOf(Image::class, $image);
        $this->assertEquals($user->getUuid(), $image->getUserUuid());
        $this->assertEquals($dish->getUuid(), $image->getDishUuid());
        $this->assertTrue($image->isMainPicture());
    }

    /**
     * BL-211: Cruciaal testgeval - bij het aanmaken van een nieuw gerecht met
     * afbeelding moet de afbeelding van een ANDER gerecht NIET worden verwijderd.
     */
    #[Test]
    public function creating_dish_image_does_not_delete_other_dish_images(): void
    {
        // Arrange: Maak een gebruiker met 2 gerechten
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->getUuid()]);

        $dish1 = Dish::factory()->create([
            'user_uuid' => $user->getUuid(),
            'cook_uuid' => $cook->getUuid(),
            'title' => 'Spareribs',
        ]);

        $dish2 = Dish::factory()->create([
            'user_uuid' => $user->getUuid(),
            'cook_uuid' => $cook->getUuid(),
            'title' => 'Pasta',
        ]);

        // Maak een afbeelding voor het eerste gerecht (Spareribs)
        $image1Dto = new ImageDto(
            userUuid: $user->getUuid(),
            path: 'img/dishes/'.$cook->getUuid(),
            name: 'spareribs.jpg',
            description: 'Spareribs foto',
            type: 'image/jpeg',
            dish: $dish1,
            typeId: Image::DISH_IMAGE,
            mainPicture: true
        );
        $spareribs_image = $this->imageRepository->create($image1Dto);

        // Verifieer dat de afbeelding van spareribs bestaat
        $this->assertNotNull($this->imageRepository->findDishMainImage($dish1->getUuid()));

        // Act: Maak een afbeelding voor het tweede gerecht (Pasta)
        $image2Dto = new ImageDto(
            userUuid: $user->getUuid(),
            path: 'img/dishes/'.$cook->getUuid(),
            name: 'pasta.jpg',
            description: 'Pasta foto',
            type: 'image/jpeg',
            dish: $dish2,
            typeId: Image::DISH_IMAGE,
            mainPicture: true
        );
        $pasta_image = $this->imageRepository->create($image2Dto);

        // Assert: De afbeelding van Spareribs moet nog STEEDS bestaan!
        $spareribs_image_after = $this->imageRepository->findDishMainImage($dish1->getUuid());

        $this->assertNotNull(
            $spareribs_image_after,
            'De afbeelding van Spareribs mag niet worden verwijderd wanneer een ander gerecht een afbeelding krijgt!'
        );
        $this->assertEquals($spareribs_image->getUuid(), $spareribs_image_after->getUuid());

        // Beide gerechten moeten nu een afbeelding hebben
        $pasta_image_after = $this->imageRepository->findDishMainImage($dish2->getUuid());
        $this->assertNotNull($pasta_image_after, 'Pasta moet ook een afbeelding hebben');
    }

    /**
     * BL-211: Bij het updaten van de afbeelding van HETZELFDE gerecht,
     * moet de oude afbeelding WEL worden verwijderd.
     */
    #[Test]
    public function updating_dish_image_replaces_old_image_for_same_dish(): void
    {
        // Arrange
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->getUuid()]);
        $dish = Dish::factory()->create([
            'user_uuid' => $user->getUuid(),
            'cook_uuid' => $cook->getUuid(),
        ]);

        // Maak een eerste afbeelding
        $firstImageDto = new ImageDto(
            userUuid: $user->getUuid(),
            path: 'img/dishes/'.$cook->getUuid(),
            name: 'original.jpg',
            description: 'Originele foto',
            type: 'image/jpeg',
            dish: $dish,
            typeId: Image::DISH_IMAGE,
            mainPicture: true
        );
        $firstImage = $this->imageRepository->create($firstImageDto);

        // Act: Maak een NIEUWE afbeelding voor hetzelfde gerecht
        $secondImageDto = new ImageDto(
            userUuid: $user->getUuid(),
            path: 'img/dishes/'.$cook->getUuid(),
            name: 'updated.jpg',
            description: 'Nieuwe foto',
            type: 'image/jpeg',
            dish: $dish,
            typeId: Image::DISH_IMAGE,
            mainPicture: true
        );
        $secondImage = $this->imageRepository->create($secondImageDto);

        // Assert: Alleen de nieuwe afbeelding bestaat, de oude is soft deleted
        $mainImage = $this->imageRepository->findDishMainImage($dish->getUuid());

        $this->assertNotNull($mainImage);
        $this->assertEquals($secondImage->getUuid(), $mainImage->getUuid());
        $this->assertEquals('updated.jpg', $mainImage->getName());

        // De oude afbeelding moet soft deleted zijn
        $oldImage = Image::withTrashed()->find($firstImage->getUuid());
        $this->assertNotNull($oldImage->deleted_at);
    }

    /**
     * Profiel afbeeldingen moeten nog steeds correct werken:
     * Een nieuwe hoofdprofiel afbeelding moet de oude vervangen.
     */
    #[Test]
    public function creating_profile_image_replaces_old_main_profile_image(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Maak eerste profiel afbeelding
        $firstProfileDto = new ImageDto(
            userUuid: $user->getUuid(),
            path: 'img/profiles/'.$user->getUuid(),
            name: 'profile1.jpg',
            description: 'Eerste profielfoto',
            type: 'image/jpeg',
            dish: null,
            typeId: Image::PROFILE_IMAGE,
            mainPicture: true
        );
        $firstProfile = $this->imageRepository->create($firstProfileDto);

        // Act: Maak nieuwe profiel afbeelding
        $secondProfileDto = new ImageDto(
            userUuid: $user->getUuid(),
            path: 'img/profiles/'.$user->getUuid(),
            name: 'profile2.jpg',
            description: 'Tweede profielfoto',
            type: 'image/jpeg',
            dish: null,
            typeId: Image::PROFILE_IMAGE,
            mainPicture: true
        );
        $secondProfile = $this->imageRepository->create($secondProfileDto);

        // Assert: Alleen de nieuwe profiel afbeelding is actief
        $mainProfile = $this->imageRepository->findMainProfileImage($user->getUuid());

        $this->assertNotNull($mainProfile);
        $this->assertEquals($secondProfile->getUuid(), $mainProfile->getUuid());

        // De oude afbeelding is soft deleted
        $oldProfile = Image::withTrashed()->find($firstProfile->getUuid());
        $this->assertNotNull($oldProfile->deleted_at);
    }

    #[Test]
    public function it_can_delete_image(): void
    {
        $image = Image::factory()->create();

        $result = $this->imageRepository->delete($image->getUuid());

        $this->assertTrue($result);
        $this->assertSoftDeleted('images', ['uuid' => $image->getUuid()]);
    }

    #[Test]
    public function it_can_find_dish_main_image(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->getUuid()]);
        $dish = Dish::factory()->create([
            'user_uuid' => $user->getUuid(),
            'cook_uuid' => $cook->getUuid(),
        ]);

        Image::factory()->forDish($dish)->asMain()->create();

        $mainImage = $this->imageRepository->findDishMainImage($dish->getUuid());

        $this->assertNotNull($mainImage);
        $this->assertEquals($dish->getUuid(), $mainImage->getDishUuid());
        $this->assertTrue($mainImage->isMainPicture());
    }

    #[Test]
    public function it_returns_null_when_dish_has_no_image(): void
    {
        $dish = Dish::factory()->create();

        $mainImage = $this->imageRepository->findDishMainImage($dish->getUuid());

        $this->assertNull($mainImage);
    }

    #[Test]
    public function it_returns_default_path_when_dish_has_no_image(): void
    {
        $dish = Dish::factory()->create();

        $path = $this->imageRepository->getDishImagePath($dish->getUuid());

        $this->assertStringContainsString('defaults/pasta.jpg', $path);
    }

    #[Test]
    public function multiple_dishes_each_keep_their_own_images(): void
    {
        // Arrange: Maak een gebruiker met 3 gerechten
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->getUuid()]);

        $dishes = [];
        $images = [];

        for ($i = 1; $i <= 3; $i++) {
            $dishes[$i] = Dish::factory()->create([
                'user_uuid' => $user->getUuid(),
                'cook_uuid' => $cook->getUuid(),
                'title' => "Gerecht {$i}",
            ]);

            $imageDto = new ImageDto(
                userUuid: $user->getUuid(),
                path: 'img/dishes/'.$cook->getUuid(),
                name: "dish{$i}.jpg",
                description: "Foto gerecht {$i}",
                type: 'image/jpeg',
                dish: $dishes[$i],
                typeId: Image::DISH_IMAGE,
                mainPicture: true
            );
            $images[$i] = $this->imageRepository->create($imageDto);
        }

        // Assert: Elk gerecht heeft nog steeds zijn eigen afbeelding
        for ($i = 1; $i <= 3; $i++) {
            $foundImage = $this->imageRepository->findDishMainImage($dishes[$i]->getUuid());
            $this->assertNotNull($foundImage, "Gerecht {$i} moet een afbeelding hebben");
            $this->assertEquals("dish{$i}.jpg", $foundImage->getName());
            $this->assertEquals($images[$i]->getUuid(), $foundImage->getUuid());
        }
    }
}
