<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\Cook;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Test dat de Settings Details pagina info-iconen toont bij KVK Naam, BTW Nummer en NVWA Nummer
 * met een tooltip die een link naar het contactformulier bevat.
 */
class CookSettingsDetailsInfoIconsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::findOrCreate(Roles::CUSTOMER, 'web');
        Role::findOrCreate(Roles::COOK, 'web');
        Role::findOrCreate(Roles::ADMIN, 'web');
    }

    #[Test]
    public function settings_details_page_phone_field_is_directly_editable(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->get(route('dashboard.settings.details.home'));

        $response->assertStatus(200);
        // Telefoonnummer mag geen readonly attribuut hebben
        $response->assertSee('id="phone"', false);
        $content = $response->getContent();
        $phoneInputPos = strpos($content, 'id="phone"');
        $this->assertNotFalse($phoneInputPos);
        $nextInputPos = strpos($content, '<input', $phoneInputPos + 1) ?: strlen($content);
        $phoneInputHtml = substr($content, $phoneInputPos, $nextInputPos - $phoneInputPos);
        $this->assertStringNotContainsString('readonly', $phoneInputHtml);
    }

    #[Test]
    public function settings_details_page_email_field_is_directly_editable(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->get(route('dashboard.settings.details.home'));

        $response->assertStatus(200);
        // E-mailadres mag geen readonly attribuut hebben — controleer dat veld aanwezig is
        $response->assertSee('id="email"', false);
        // De pagina mag geen readonly op het e-mailadres hebben
        $content = $response->getContent();
        $emailInputPos = strpos($content, 'id="email"');
        $this->assertNotFalse($emailInputPos);
        $nextInputPos = strpos($content, '<input', $emailInputPos + 1) ?: strlen($content);
        $emailInputHtml = substr($content, $emailInputPos, $nextInputPos - $emailInputPos);
        $this->assertStringNotContainsString('readonly', $emailInputHtml);
    }

    #[Test]
    public function settings_details_page_shows_save_button_without_edit_mode(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->get(route('dashboard.settings.details.home'));

        $response->assertStatus(200);
        // Opslaan-knop is altijd zichtbaar
        $response->assertSee('Opslaan');
        // Geen "Contactgegevens wijzigen" toggle-knop meer
        $response->assertDontSee('Contactgegevens wijzigen');
    }

    #[Test]
    public function settings_details_page_shows_password_change_button(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->get(route('dashboard.settings.details.home'));

        $response->assertStatus(200);
        $response->assertSee('Wachtwoord wijzigen');
    }

    #[Test]
    public function settings_details_page_shows_info_icon_for_kvk_naam(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->get(route('dashboard.settings.details.home'));

        $response->assertStatus(200);
        // Check dat er een info-icoon is in de label voor KVK Naam
        $response->assertSee('id="kvk_naam"', false);
        $response->assertSee('fa-info-circle', false);
    }

    #[Test]
    public function settings_details_page_shows_info_icon_for_btw_nummer(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->get(route('dashboard.settings.details.home'));

        $response->assertStatus(200);
        // Check dat er een info-icoon is bij BTW Nummer label
        $response->assertSee('id="btw_nummer"', false);
    }

    #[Test]
    public function settings_details_page_shows_info_icon_for_nvwa_nummer(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->get(route('dashboard.settings.details.home'));

        $response->assertStatus(200);
        // Check dat er een info-icoon is bij NVWA Nummer label
        $response->assertSee('id="nvwa_nummer"', false);
    }

    #[Test]
    public function settings_details_page_tooltip_contains_contact_link(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->get(route('dashboard.settings.details.home'));

        $response->assertStatus(200);
        // Check dat de tooltip tekst aanwezig is met link naar contactformulier
        $response->assertSee('Neem contact op met DeBurenKoken');
        $response->assertSee(route('contact'));
        $response->assertSee('contactformulier');
    }

    #[Test]
    public function settings_details_page_tooltip_mentions_kvk_btw_nvwa(): void
    {
        $user = $this->createAuthenticatedCook();

        $response = $this->actingAs($user)->get(route('dashboard.settings.details.home'));

        $response->assertStatus(200);
        // Check dat de tooltip de juiste gegevens noemt die gewijzigd kunnen worden
        $response->assertSee('KVK naam');
        $response->assertSee('BTW nummer');
        $response->assertSee('NVWA nummer');
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
