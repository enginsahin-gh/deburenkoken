<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\Cook;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Test dat telefoonnummer en e-mailadres direct aanpasbaar zijn zonder "Wijzigen" knop.
 */
class CookSettingsDetailsDirectEditTest extends TestCase
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
    public function cook_can_update_phone_number_directly(): void
    {
        Mail::fake();

        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->post(route('dashboard.settings.details.update'), [
            'phone' => '0698765432',
            'email' => $user->getEmail(),
        ]);

        $response->assertRedirect(route('dashboard.settings.details.home'));
        $response->assertSessionHas('message', 'Gegevens zijn aangepast');

        $updatedProfile = UserProfile::where('user_uuid', $user->uuid)->first();
        $this->assertEquals('0698765432', $updatedProfile->getPhoneNumber());
    }

    #[Test]
    public function cook_submitting_same_phone_number_does_not_cause_error(): void
    {
        Mail::fake();

        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->post(route('dashboard.settings.details.update'), [
            'phone' => '0612345678',
            'email' => $user->getEmail(),
        ]);

        $response->assertRedirect(route('dashboard.settings.details.home'));
        $response->assertSessionHas('message', 'Gegevens zijn aangepast');
    }

    #[Test]
    public function cook_submitting_invalid_email_gets_validation_error(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->post(route('dashboard.settings.details.update'), [
            'phone' => '0612345678',
            'email' => 'geen-geldig-email',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    #[Test]
    public function cook_submitting_existing_email_gets_error_message(): void
    {
        $user = $this->createAuthenticatedCook();
        $otherUser = User::factory()->create(['email' => 'anderkok@gmail.com']);

        $response = $this->actingAs($user)->post(route('dashboard.settings.details.update'), [
            'phone' => '0612345678',
            'email' => $otherUser->getEmail(),
        ]);

        $response->assertSessionHas('userAlreadyExist', 'Deze email is al in gebruik');
    }

    #[Test]
    public function unauthenticated_user_cannot_access_details_page(): void
    {
        $response = $this->get(route('dashboard.settings.details.home'));
        $response->assertRedirect(route('login.home'));
    }

    #[Test]
    public function unauthenticated_user_cannot_post_to_details_update(): void
    {
        $response = $this->post(route('dashboard.settings.details.update'), [
            'phone' => '0612345678',
            'email' => 'test@gmail.com',
        ]);
        $response->assertRedirect(route('login.home'));
    }

    #[Test]
    public function settings_details_page_does_not_require_edit_mode_session(): void
    {
        $user = $this->createAuthenticatedCook();

        // Bezoek pagina zonder editMode sessie
        $response = $this->actingAs($user)->get(route('dashboard.settings.details.home'));

        $response->assertStatus(200);
        // Opslaan-knop is direct zichtbaar zonder toggle
        $response->assertSee('Opslaan');
        $response->assertDontSee('Contactgegevens wijzigen');
    }

    /**
     * Creëert een geauthenticeerde kok met alle benodigde relaties.
     */
    private function createAuthenticatedCook(): User
    {
        $user = User::factory()->create([
            'kvk_naam' => 'Test KVK Bedrijf',
            'btw_nummer' => 'NL123456789B01',
            'nvwa_nummer' => '12345678',
            'email' => fake()->unique()->userName().'@gmail.com',
        ]);
        $user->assignRole(Roles::COOK);

        Cook::factory()->create(['user_uuid' => $user->uuid]);

        UserProfile::create([
            'user_uuid' => $user->uuid,
            'firstname' => 'Test',
            'lastname' => 'Kok',
            'phone_number' => '0612345678',
            'birthday' => now()->subYears(30),
        ]);

        return $user;
    }
}
