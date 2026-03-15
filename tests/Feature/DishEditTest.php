<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\Advert;
use App\Models\Cook;
use App\Models\Dish;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DishEditTest extends TestCase
{
    use RefreshDatabase;

    private User $cookUser;

    private Cook $cook;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate(Roles::CUSTOMER, 'web');
        Role::findOrCreate(Roles::COOK, 'web');
        Role::findOrCreate(Roles::ADMIN, 'web');

        $this->cookUser = User::factory()->create();
        $this->cookUser->assignRole(Roles::COOK);
        $this->cook = Cook::factory()->create(['user_uuid' => $this->cookUser->uuid]);
    }

    // -------------------------------------------------------------------------
    // editDish() – GET /dashboard/dishes/edit/{uuid}
    // -------------------------------------------------------------------------

    #[Test]
    public function cook_can_access_edit_form_for_own_dish_with_no_adverts(): void
    {
        $dish = Dish::factory()->create([
            'user_uuid' => $this->cookUser->uuid,
            'cook_uuid' => $this->cook->uuid,
        ]);

        $response = $this->actingAs($this->cookUser)
            ->get(route('dashboard.dishes.edit', $dish->uuid));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.dishes.edit');
        $response->assertViewHas('edit', true);
    }

    #[Test]
    public function cook_can_access_edit_form_for_own_dish_with_expired_advert(): void
    {
        $dish = Dish::factory()->create([
            'user_uuid' => $this->cookUser->uuid,
            'cook_uuid' => $this->cook->uuid,
        ]);

        Advert::factory()->expired()->create(['dish_uuid' => $dish->uuid]);

        $response = $this->actingAs($this->cookUser)
            ->get(route('dashboard.dishes.edit', $dish->uuid));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.dishes.edit');
    }

    #[Test]
    public function cook_can_access_edit_form_for_own_dish_with_cancelled_advert(): void
    {
        $dish = Dish::factory()->create([
            'user_uuid' => $this->cookUser->uuid,
            'cook_uuid' => $this->cook->uuid,
        ]);

        $advert = Advert::factory()->published()->create(['dish_uuid' => $dish->uuid]);
        $advert->delete(); // Soft-delete = cancelled

        $response = $this->actingAs($this->cookUser)
            ->get(route('dashboard.dishes.edit', $dish->uuid));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.dishes.edit');
    }

    #[Test]
    public function cook_cannot_access_edit_form_for_own_dish_with_active_advert(): void
    {
        $dish = Dish::factory()->create([
            'user_uuid' => $this->cookUser->uuid,
            'cook_uuid' => $this->cook->uuid,
        ]);

        Advert::factory()->published()->create(['dish_uuid' => $dish->uuid]);

        $response = $this->actingAs($this->cookUser)
            ->get(route('dashboard.dishes.edit', $dish->uuid));

        $response->assertRedirect(route('dashboard.dishes.show', $dish->uuid));
        $response->assertSessionHas('error');
    }

    #[Test]
    public function cook_cannot_access_edit_form_for_another_users_dish(): void
    {
        $otherUser = User::factory()->create();
        $otherCook = Cook::factory()->create(['user_uuid' => $otherUser->uuid]);
        $dish = Dish::factory()->create([
            'user_uuid' => $otherUser->uuid,
            'cook_uuid' => $otherCook->uuid,
        ]);

        $response = $this->actingAs($this->cookUser)
            ->get(route('dashboard.dishes.edit', $dish->uuid));

        $response->assertStatus(404);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_edit_form(): void
    {
        $dish = Dish::factory()->create([
            'user_uuid' => $this->cookUser->uuid,
            'cook_uuid' => $this->cook->uuid,
        ]);

        $response = $this->get(route('dashboard.dishes.edit', $dish->uuid));

        $response->assertRedirect();
    }

    // -------------------------------------------------------------------------
    // updateDish() – PATCH /dashboard/dishes/update/{uuid}
    // -------------------------------------------------------------------------

    #[Test]
    public function cook_can_update_description_of_own_dish_with_no_active_advert(): void
    {
        $dish = Dish::factory()->create([
            'user_uuid' => $this->cookUser->uuid,
            'cook_uuid' => $this->cook->uuid,
            'description' => 'Old description',
        ]);

        $response = $this->actingAs($this->cookUser)
            ->patch(route('dashboard.dishes.update', $dish->uuid), [
                'description' => 'New description',
                'spicy' => 1,
                'vegetarian' => '0',
                'vegan' => '0',
                'halal' => '0',
                'alcohol' => '0',
                'gluten' => '0',
                'lactose' => '0',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('dishes', [
            'uuid' => $dish->uuid,
            'description' => 'New description',
        ]);
    }

    #[Test]
    public function cook_cannot_change_title_via_update(): void
    {
        $originalTitle = 'Original Title';
        $dish = Dish::factory()->create([
            'user_uuid' => $this->cookUser->uuid,
            'cook_uuid' => $this->cook->uuid,
            'title' => $originalTitle,
        ]);

        $this->actingAs($this->cookUser)
            ->patch(route('dashboard.dishes.update', $dish->uuid), [
                'description' => 'Some description',
                'spicy' => 0,
                'vegetarian' => '0',
                'vegan' => '0',
                'halal' => '0',
                'alcohol' => '0',
                'gluten' => '0',
                'lactose' => '0',
            ]);

        $this->assertDatabaseHas('dishes', [
            'uuid' => $dish->uuid,
            'title' => $originalTitle,
        ]);
    }

    #[Test]
    public function cook_cannot_change_price_via_update(): void
    {
        $originalPrice = 10.00;
        $dish = Dish::factory()->create([
            'user_uuid' => $this->cookUser->uuid,
            'cook_uuid' => $this->cook->uuid,
            'portion_price' => $originalPrice,
        ]);

        $this->actingAs($this->cookUser)
            ->patch(route('dashboard.dishes.update', $dish->uuid), [
                'description' => 'Some description',
                'price' => 99.99,
                'spicy' => 0,
                'vegetarian' => '0',
                'vegan' => '0',
                'halal' => '0',
                'alcohol' => '0',
                'gluten' => '0',
                'lactose' => '0',
            ]);

        $this->assertDatabaseHas('dishes', [
            'uuid' => $dish->uuid,
            'portion_price' => $originalPrice,
        ]);
    }

    #[Test]
    public function cook_cannot_update_dish_with_active_advert(): void
    {
        $dish = Dish::factory()->create([
            'user_uuid' => $this->cookUser->uuid,
            'cook_uuid' => $this->cook->uuid,
        ]);

        Advert::factory()->published()->create(['dish_uuid' => $dish->uuid]);

        $response = $this->actingAs($this->cookUser)
            ->patch(route('dashboard.dishes.update', $dish->uuid), [
                'description' => 'New description',
                'spicy' => 0,
                'vegetarian' => '0',
                'vegan' => '0',
                'halal' => '0',
                'alcohol' => '0',
                'gluten' => '0',
                'lactose' => '0',
            ]);

        $response->assertRedirect(route('dashboard.dishes.show', $dish->uuid));
        $response->assertSessionHas('error');
    }

    #[Test]
    public function cook_can_update_dish_with_expired_advert(): void
    {
        $dish = Dish::factory()->create([
            'user_uuid' => $this->cookUser->uuid,
            'cook_uuid' => $this->cook->uuid,
            'description' => 'Old description',
        ]);

        Advert::factory()->expired()->create(['dish_uuid' => $dish->uuid]);

        $response = $this->actingAs($this->cookUser)
            ->patch(route('dashboard.dishes.update', $dish->uuid), [
                'description' => 'Updated description',
                'spicy' => 0,
                'vegetarian' => '0',
                'vegan' => '0',
                'halal' => '0',
                'alcohol' => '0',
                'gluten' => '0',
                'lactose' => '0',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('dishes', [
            'uuid' => $dish->uuid,
            'description' => 'Updated description',
        ]);
    }
}
