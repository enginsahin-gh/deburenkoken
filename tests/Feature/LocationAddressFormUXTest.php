<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests voor de verbetering van de adresinvul-UX op de locatiepagina (BL-250).
 *
 * Controleert dat:
 * - Het zoekveld visueel prominent aanwezig is (CSS-klasse)
 * - De instructietekst als duidelijk blok getoond wordt
 * - De readonly-velden de juiste wrapper en tooltip-HTML bevatten
 * - De pagina toegankelijk is zowel bij registratie als via de instellingen
 */
class LocationAddressFormUXTest extends TestCase
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
    public function location_page_shows_prominent_search_input_during_registration(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->withCookie('accepted_cookies', 'true')
            ->get(route('verification.location'));

        $response->assertStatus(200);
        $response->assertSee('address-search-input', false);
    }

    #[Test]
    public function location_page_shows_instruction_box_during_registration(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->withCookie('accepted_cookies', 'true')
            ->get(route('verification.location'));

        $response->assertStatus(200);
        $response->assertSee('address-instruction-box', false);
        $response->assertSee('Vul hierboven je volledige adres in');
    }

    #[Test]
    public function location_page_shows_readonly_field_wrappers_during_registration(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->withCookie('accepted_cookies', 'true')
            ->get(route('verification.location'));

        $response->assertStatus(200);
        $response->assertSee('readonly-field-wrapper', false);
        $response->assertSee('readonly-address-field', false);
    }

    #[Test]
    public function location_page_shows_readonly_tooltips_for_address_fields_during_registration(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->withCookie('accepted_cookies', 'true')
            ->get(route('verification.location'));

        $response->assertStatus(200);
        $response->assertSee('readonly-field-tooltip', false);
        $response->assertSee('Gebruik het zoekveld hierboven om je adres in te vullen');
    }

    #[Test]
    public function location_page_address_fields_have_readonly_attribute_during_registration(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->withCookie('accepted_cookies', 'true')
            ->get(route('verification.location'));

        $response->assertStatus(200);

        // Postcode, huisnummer, straat, stad, land en toevoeging zijn readonly
        $response->assertSee('id="postal"', false);
        $response->assertSee('id="number"', false);
        $response->assertSee('id="street"', false);
        $response->assertSee('id="city"', false);
        $response->assertSee('id="country"', false);
        $response->assertSee('readonly', false);
    }

    #[Test]
    public function location_page_via_settings_shows_prominent_search_input(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->withCookie('accepted_cookies', 'true')
            ->withSession(['editing_address_from_settings' => true])
            ->get(route('verification.location'));

        $response->assertStatus(200);
        $response->assertSee('address-search-input', false);
    }

    #[Test]
    public function location_page_via_settings_shows_instruction_box(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->withCookie('accepted_cookies', 'true')
            ->withSession(['editing_address_from_settings' => true])
            ->get(route('verification.location'));

        $response->assertStatus(200);
        $response->assertSee('address-instruction-box', false);
    }

    #[Test]
    public function location_page_via_settings_shows_readonly_tooltips(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->withCookie('accepted_cookies', 'true')
            ->withSession(['editing_address_from_settings' => true])
            ->get(route('verification.location'));

        $response->assertStatus(200);
        $response->assertSee('readonly-field-tooltip', false);
    }

    #[Test]
    public function location_page_unauthenticated_user_is_redirected(): void
    {
        $response = $this->get(route('verification.location'));

        $response->assertRedirect();
    }

    #[Test]
    public function settings_location_update_rejects_invalid_postal_code(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->withSession(['editing_address_from_settings' => true])
            ->post(route('dashboard.settings.update.location.submit'), [
                'postal' => 'INVALID',
                'housenumber' => '10',
                'street' => 'Teststraat',
                'place' => 'Teststad',
                'country' => 'NL',
                'latitude' => '52.0',
                'longitude' => '4.0',
            ]);

        $response->assertSessionHasErrors(['postal']);
    }

    #[Test]
    public function settings_location_update_rejects_coordinates_out_of_range(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->withSession(['editing_address_from_settings' => true])
            ->post(route('dashboard.settings.update.location.submit'), [
                'postal' => '1234 AB',
                'housenumber' => '10',
                'street' => 'Teststraat',
                'place' => 'Teststad',
                'country' => 'NL',
                'latitude' => '999',
                'longitude' => '999',
            ]);

        $response->assertSessionHasErrors(['latitude', 'longitude']);
    }

    #[Test]
    public function settings_location_update_accepts_valid_dutch_postal_code(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        foreach (['1234 AB', '1234AB', '1234 ab', '1234ab'] as $postal) {
            $response = $this->actingAs($user)
                ->withSession(['editing_address_from_settings' => true])
                ->post(route('dashboard.settings.update.location.submit'), [
                    'postal' => $postal,
                    'housenumber' => '10',
                    'street' => 'Teststraat',
                    'place' => 'Teststad',
                    'country' => 'NL',
                    'latitude' => '52.370216',
                    'longitude' => '4.895168',
                ]);

            $response->assertSessionHasNoErrors();
        }
    }

    #[Test]
    public function settings_location_update_accepts_alphanumeric_housenumber(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        foreach (['2b50', '10a', '123-B', '1'] as $housenumber) {
            $response = $this->actingAs($user)
                ->withSession(['editing_address_from_settings' => true])
                ->post(route('dashboard.settings.update.location.submit'), [
                    'postal' => '1234 AB',
                    'housenumber' => $housenumber,
                    'street' => 'Teststraat',
                    'place' => 'Teststad',
                    'country' => 'NL',
                    'latitude' => '52.370216',
                    'longitude' => '4.895168',
                ]);

            $response->assertSessionHasNoErrors();
        }
    }

    #[Test]
    public function registration_location_submit_accepts_alphanumeric_housenumber(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        foreach (['2b50', '10a', '123-B', '1'] as $housenumber) {
            $response = $this->actingAs($user)
                ->withCookie('accepted_cookies', 'true')
                ->post(route('verification.location.submit'), [
                    'postal' => '1234 AB',
                    'housenumber' => $housenumber,
                    'street' => 'Teststraat',
                    'place' => 'Teststad',
                    'country' => 'NL',
                    'latitude' => '52.370216',
                    'longitude' => '4.895168',
                ]);

            $response->assertSessionHasNoErrors();
        }
    }

    #[Test]
    public function registration_location_submit_accepts_lowercase_postal_code(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        foreach (['1234 ab', '1234ab'] as $postal) {
            $response = $this->actingAs($user)
                ->withCookie('accepted_cookies', 'true')
                ->post(route('verification.location.submit'), [
                    'postal' => $postal,
                    'housenumber' => '10',
                    'street' => 'Teststraat',
                    'place' => 'Teststad',
                    'country' => 'NL',
                    'latitude' => '52.370216',
                    'longitude' => '4.895168',
                ]);

            $response->assertSessionHasNoErrors();
        }
    }

    #[Test]
    public function registration_location_submit_rejects_invalid_postal_code(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->withCookie('accepted_cookies', 'true')
            ->post(route('verification.location.submit'), [
                'postal' => 'INVALID',
                'housenumber' => '10',
                'street' => 'Teststraat',
                'place' => 'Teststad',
                'country' => 'NL',
                'latitude' => '52.370216',
                'longitude' => '4.895168',
            ]);

        $response->assertSessionHasErrors(['postal']);
    }
}
