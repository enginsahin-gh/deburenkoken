<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CookRegistrationInformationTest extends TestCase
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
    public function registration_information_page_does_not_show_type_thuiskok_field(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->withCookie('accepted_cookies', 'true')
            ->get(route('verification.first'));

        $response->assertStatus(200);
        $response->assertDontSee('Type Thuiskok');
    }

    #[Test]
    public function registration_information_page_shows_optional_business_fields(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->withCookie('accepted_cookies', 'true')
            ->get(route('verification.first'));

        $response->assertStatus(200);
        $response->assertSee('KVK Naam');
        $response->assertSee('BTW Nummer');
        $response->assertSee('NVWA Nummer');
    }

    #[Test]
    public function user_can_submit_form_without_business_fields(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->withCookie('accepted_cookies', 'true')
            ->post(route('verification.information.submit'), [
                'firstname' => 'Jan',
                'lastname' => 'Jansen',
                'phone' => '0612345678',
                'birthday' => '1990-01-01',
                'kvk_naam' => '',
                'btw_nummer' => '',
                'nvwa_nummer' => '',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $updatedUser = User::find($user->uuid);
        $this->assertNull($updatedUser->type_thuiskok);
        $this->assertNull($updatedUser->kvk_naam);
    }

    #[Test]
    public function user_type_is_set_to_zakelijke_thuiskok_when_kvk_naam_is_filled(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->withCookie('accepted_cookies', 'true')
            ->post(route('verification.information.submit'), [
                'firstname' => 'Jan',
                'lastname' => 'Jansen',
                'phone' => '0612345678',
                'birthday' => '1990-01-01',
                'kvk_naam' => 'Jansen B.V.',
                'btw_nummer' => 'NL123456789B01',
                'nvwa_nummer' => 'NVWA123',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $updatedUser = User::find($user->uuid);
        $this->assertEquals('Zakelijke Thuiskok', $updatedUser->type_thuiskok);
        $this->assertEquals('Jansen B.V.', $updatedUser->kvk_naam);
    }

    #[Test]
    public function user_type_is_null_when_kvk_naam_is_empty(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->withCookie('accepted_cookies', 'true')
            ->post(route('verification.information.submit'), [
                'firstname' => 'Jan',
                'lastname' => 'Jansen',
                'phone' => '0612345678',
                'birthday' => '1990-01-01',
                'kvk_naam' => '',
                'btw_nummer' => '',
                'nvwa_nummer' => '',
            ]);

        $response->assertRedirect();

        $updatedUser = User::find($user->uuid);
        $this->assertNull($updatedUser->type_thuiskok);
    }

    #[Test]
    public function form_validation_does_not_require_business_fields(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->withCookie('accepted_cookies', 'true')
            ->post(route('verification.information.submit'), [
                'firstname' => 'Jan',
                'lastname' => 'Jansen',
                'phone' => '0612345678',
                'birthday' => '1990-01-01',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    #[Test]
    public function whitespace_only_values_are_treated_as_null(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->withCookie('accepted_cookies', 'true')
            ->post(route('verification.information.submit'), [
                'firstname' => 'Jan',
                'lastname' => 'Jansen',
                'phone' => '0612345678',
                'birthday' => '1990-01-01',
                'kvk_naam' => '   ',
                'btw_nummer' => '  ',
                'nvwa_nummer' => '    ',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $updatedUser = User::find($user->uuid);
        $this->assertNull($updatedUser->type_thuiskok);
        $this->assertNull($updatedUser->kvk_naam);
    }

    #[Test]
    public function btw_nummer_without_kvk_naam_does_not_set_zakelijke_thuiskok(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        // Test with only BTW nummer filled - should NOT set type_thuiskok
        // because only KVK Naam determines business cook status
        $response = $this->actingAs($user)
            ->withCookie('accepted_cookies', 'true')
            ->post(route('verification.information.submit'), [
                'firstname' => 'Jan',
                'lastname' => 'Jansen',
                'phone' => '0612345678',
                'birthday' => '1990-01-01',
                'kvk_naam' => '',
                'btw_nummer' => 'NL123456789B01',
                'nvwa_nummer' => '',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $updatedUser = User::find($user->uuid);
        $this->assertNull($updatedUser->type_thuiskok);
        $this->assertEquals('NL123456789B01', $updatedUser->btw_nummer);
        $this->assertEquals('NL123456789B01', $updatedUser->btw_nummer);
    }

    #[Test]
    public function invalid_btw_nummer_format_is_rejected(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->withCookie('accepted_cookies', 'true')
            ->post(route('verification.information.submit'), [
                'firstname' => 'Jan',
                'lastname' => 'Jansen',
                'phone' => '0612345678',
                'birthday' => '1990-01-01',
                'kvk_naam' => 'Test B.V.',
                'btw_nummer' => 'INVALID123',
                'nvwa_nummer' => '',
            ]);

        $response->assertSessionHasErrors(['btw_nummer']);
    }

    #[Test]
    public function valid_btw_nummer_format_is_accepted(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->withCookie('accepted_cookies', 'true')
            ->post(route('verification.information.submit'), [
                'firstname' => 'Jan',
                'lastname' => 'Jansen',
                'phone' => '0612345678',
                'birthday' => '1990-01-01',
                'kvk_naam' => 'Test B.V.',
                'btw_nummer' => 'NL999888777A99',
                'nvwa_nummer' => 'NVWA123',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $updatedUser = User::find($user->uuid);
        $this->assertEquals('NL999888777A99', $updatedUser->btw_nummer);
    }

    #[Test]
    public function kvk_naam_with_special_characters_is_accepted(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->withCookie('accepted_cookies', 'true')
            ->post(route('verification.information.submit'), [
                'firstname' => 'Jan',
                'lastname' => 'Jansen',
                'phone' => '0612345678',
                'birthday' => '1990-01-01',
                'kvk_naam' => 'Café & Restaurant B.V.',
                'btw_nummer' => '',
                'nvwa_nummer' => '',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $updatedUser = User::find($user->uuid);
        $this->assertEquals('Café & Restaurant B.V.', $updatedUser->kvk_naam);
    }

    #[Test]
    public function existing_business_cook_can_become_non_business(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'type_thuiskok' => 'Zakelijke Thuiskok',
            'kvk_naam' => 'Old Company B.V.',
            'btw_nummer' => 'NL123456789B01',
        ]);
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->withCookie('accepted_cookies', 'true')
            ->post(route('verification.information.submit'), [
                'firstname' => 'Jan',
                'lastname' => 'Jansen',
                'phone' => '0612345678',
                'birthday' => '1990-01-01',
                'kvk_naam' => '',
                'btw_nummer' => '',
                'nvwa_nummer' => '',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $updatedUser = User::find($user->uuid);
        $this->assertNull($updatedUser->type_thuiskok);
        $this->assertNull($updatedUser->kvk_naam);
        $this->assertNull($updatedUser->btw_nummer);
    }

    #[Test]
    public function settings_details_page_does_not_show_type_thuiskok_field(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->get(route('dashboard.settings.details.home'));

        $response->assertStatus(200);
        $response->assertDontSee('Type Thuiskok');
    }
}
