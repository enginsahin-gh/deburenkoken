<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\Cook;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdvertDisclaimerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate(Roles::CUSTOMER, 'web');
        Role::findOrCreate(Roles::COOK, 'web');
        Role::findOrCreate(Roles::ADMIN, 'web');
    }

    #[Test]
    public function advert_create_page_shows_disclaimer_text(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Roles::COOK);

        Cook::factory()->create(['user_uuid' => $user->uuid]);

        $response = $this->actingAs($user)->get(route('dashboard.adverts.create'));

        $response->assertStatus(200);
        $response->assertSee('Door deze advertentie te plaatsen verklaar ik dat:');
    }

    #[Test]
    public function advert_create_page_shows_terms_conditions_link(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Roles::COOK);

        Cook::factory()->create(['user_uuid' => $user->uuid]);

        $response = $this->actingAs($user)->get(route('dashboard.adverts.create'));

        $response->assertStatus(200);
        $response->assertSee('algemene voorwaarden');
        $response->assertSee(route('terms.conditions'));
    }

    #[Test]
    public function advert_create_page_shows_haccp_information(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Roles::COOK);

        Cook::factory()->create(['user_uuid' => $user->uuid]);

        $response = $this->actingAs($user)->get(route('dashboard.adverts.create'));

        $response->assertStatus(200);
        $response->assertSee('hygiëne- en voedselveiligheidsrichtlijnen');
        $response->assertSee('HACCP');
        $response->assertSee(route('cook.facts'));
    }

    #[Test]
    public function advert_create_page_shows_allergen_responsibility_text(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Roles::COOK);

        Cook::factory()->create(['user_uuid' => $user->uuid]);

        $response = $this->actingAs($user)->get(route('dashboard.adverts.create'));

        $response->assertStatus(200);
        $response->assertSee('Ingrediënten en allergenen volledig en correct zijn vermeld');
    }

    #[Test]
    public function advert_create_page_shows_food_safety_responsibility_text(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Roles::COOK);

        Cook::factory()->create(['user_uuid' => $user->uuid]);

        $response = $this->actingAs($user)->get(route('dashboard.adverts.create'));

        $response->assertStatus(200);
        $response->assertSee('Ik verantwoordelijk ben voor de voedselveiligheid van de aangeboden maaltijd');
    }

    #[Test]
    public function old_disclaimer_text_is_no_longer_shown(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Roles::COOK);

        Cook::factory()->create(['user_uuid' => $user->uuid]);

        $response = $this->actingAs($user)->get(route('dashboard.adverts.create'));

        $response->assertStatus(200);
        $response->assertDontSee('Door het plaatsen van deze advertentie ga ik akkoord met de algemene voorwaarden en bevestig ik dat bovenstaande gegevens correct zijn.');
    }

    #[Test]
    public function disclaimer_uses_semantic_list_without_manual_bullets(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Roles::COOK);

        Cook::factory()->create(['user_uuid' => $user->uuid]);

        $response = $this->actingAs($user)->get(route('dashboard.adverts.create'));

        $response->assertStatus(200);

        // Controleer dat de semantic list class aanwezig is
        $response->assertSee('advert-disclaimer-list');

        // Controleer dat er geen handmatige bullet symbolen in de tekst staan
        $response->assertDontSee('• Ik akkoord');
        $response->assertDontSee('• De maaltijd');
        $response->assertDontSee('• Ingrediënten');
        $response->assertDontSee('• Ik verantwoordelijk');
    }
}
