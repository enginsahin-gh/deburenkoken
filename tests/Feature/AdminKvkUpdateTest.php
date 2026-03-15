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
 * Test voor admin KVK gegevens bijwerken functionaliteit.
 * Deze feature stelt admins in staat om KVK gegevens voor thuiskoks in te vullen
 * via het admin dashboard in plaats van PHPMyAdmin.
 */
class AdminKvkUpdateTest extends TestCase
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
    public function admin_can_view_single_account_page_with_kvk_form(): void
    {
        $admin = $this->createAdmin();
        $cook = $this->createCook();

        $response = $this->actingAs($admin)
            ->get(route('dashboard.admin.accounts.single', $cook->getUuid()));

        $response->assertStatus(200);
        $response->assertSee('KVK Gegevens');
        $response->assertSee('KVK Naam');
        $response->assertSee('KVK Nummer');
        $response->assertSee('BTW Nummer');
        $response->assertSee('RSIN');
        $response->assertSee('Vestigingsnummer');
        $response->assertSee('NVWA Nummer');
        $response->assertSee('KVK gegevens opslaan');
    }

    #[Test]
    public function admin_can_update_kvk_details_for_cook(): void
    {
        $admin = $this->createAdmin();
        $cook = $this->createCook();

        $kvkData = [
            'kvk_naam' => 'Test Bedrijf B.V.',
            'kvk_nummer' => '12345678',
            'btw_nummer' => 'NL123456789B01',
            'rsin' => '123456789',
            'vestigingsnummer' => '000012345678',
            'nvwa_nummer' => 'NVWA-12345',
        ];

        $response = $this->actingAs($admin)
            ->patch(route('dashboard.admin.accounts.kvk.update', $cook->getUuid()), $kvkData);

        $response->assertRedirect(route('dashboard.admin.accounts.single', $cook->getUuid()));
        $response->assertSessionHas('message', 'KVK gegevens zijn succesvol bijgewerkt.');

        $cook->refresh();
        $this->assertEquals('Test Bedrijf B.V.', $cook->kvk_naam);
        $this->assertEquals('12345678', $cook->kvk_nummer);
        $this->assertEquals('NL123456789B01', $cook->btw_nummer);
        $this->assertEquals('123456789', $cook->rsin);
        $this->assertEquals('000012345678', $cook->vestigingsnummer);
        $this->assertEquals('NVWA-12345', $cook->nvwa_nummer);
    }

    #[Test]
    public function updating_kvk_details_changes_type_thuiskok_to_zakelijke(): void
    {
        $admin = $this->createAdmin();
        $cook = $this->createCook(['type_thuiskok' => 'Particuliere Thuiskok']);

        $this->assertEquals('Particuliere Thuiskok', $cook->type_thuiskok);

        $kvkData = [
            'kvk_naam' => 'Mijn KVK Bedrijf',
            'kvk_nummer' => '87654321',
        ];

        $this->actingAs($admin)
            ->patch(route('dashboard.admin.accounts.kvk.update', $cook->getUuid()), $kvkData);

        $cook->refresh();
        $this->assertEquals('Zakelijke Thuiskok', $cook->type_thuiskok);
    }

    #[Test]
    public function updating_only_kvk_naam_changes_type_thuiskok(): void
    {
        $admin = $this->createAdmin();
        $cook = $this->createCook(['type_thuiskok' => 'Particuliere Thuiskok']);

        $kvkData = [
            'kvk_naam' => 'Alleen Naam Bedrijf',
        ];

        $this->actingAs($admin)
            ->patch(route('dashboard.admin.accounts.kvk.update', $cook->getUuid()), $kvkData);

        $cook->refresh();
        $this->assertEquals('Zakelijke Thuiskok', $cook->type_thuiskok);
        $this->assertEquals('Alleen Naam Bedrijf', $cook->kvk_naam);
    }

    #[Test]
    public function updating_only_kvk_nummer_changes_type_thuiskok(): void
    {
        $admin = $this->createAdmin();
        $cook = $this->createCook(['type_thuiskok' => 'Particuliere Thuiskok']);

        $kvkData = [
            'kvk_nummer' => '11223344',
        ];

        $this->actingAs($admin)
            ->patch(route('dashboard.admin.accounts.kvk.update', $cook->getUuid()), $kvkData);

        $cook->refresh();
        $this->assertEquals('Zakelijke Thuiskok', $cook->type_thuiskok);
        $this->assertEquals('11223344', $cook->kvk_nummer);
    }

    #[Test]
    public function updating_without_kvk_naam_or_nummer_does_not_change_type(): void
    {
        $admin = $this->createAdmin();
        $cook = $this->createCook(['type_thuiskok' => 'Particuliere Thuiskok']);

        $kvkData = [
            'btw_nummer' => 'NL999999999B99',
            'nvwa_nummer' => 'NVWA-99999',
        ];

        $this->actingAs($admin)
            ->patch(route('dashboard.admin.accounts.kvk.update', $cook->getUuid()), $kvkData);

        $cook->refresh();
        $this->assertEquals('Particuliere Thuiskok', $cook->type_thuiskok);
        $this->assertEquals('NL999999999B99', $cook->btw_nummer);
        $this->assertEquals('NVWA-99999', $cook->nvwa_nummer);
    }

    #[Test]
    public function non_admin_cannot_update_kvk_details(): void
    {
        $cook = $this->createCook();
        $anotherCook = $this->createCook();

        $kvkData = [
            'kvk_naam' => 'Poging tot fraude',
            'kvk_nummer' => '99999999',
        ];

        $response = $this->actingAs($cook)
            ->patch(route('dashboard.admin.accounts.kvk.update', $anotherCook->getUuid()), $kvkData);

        $response->assertStatus(403);
    }

    #[Test]
    public function guest_cannot_update_kvk_details(): void
    {
        $cook = $this->createCook();

        $kvkData = [
            'kvk_naam' => 'Poging zonder login',
        ];

        $response = $this->patch(route('dashboard.admin.accounts.kvk.update', $cook->getUuid()), $kvkData);

        $response->assertRedirect(route('login.home'));
    }

    #[Test]
    public function kvk_details_validation_rejects_too_long_values(): void
    {
        $admin = $this->createAdmin();
        $cook = $this->createCook();

        $kvkData = [
            'kvk_naam' => str_repeat('a', 101),
            'kvk_nummer' => str_repeat('1', 21),
        ];

        $response = $this->actingAs($admin)
            ->patch(route('dashboard.admin.accounts.kvk.update', $cook->getUuid()), $kvkData);

        $response->assertSessionHasErrors(['kvk_naam', 'kvk_nummer']);
    }

    #[Test]
    public function single_account_page_shows_current_type_thuiskok(): void
    {
        $admin = $this->createAdmin();
        $cook = $this->createCook(['type_thuiskok' => 'Zakelijke Thuiskok']);

        $response = $this->actingAs($admin)
            ->get(route('dashboard.admin.accounts.single', $cook->getUuid()));

        $response->assertStatus(200);
        $response->assertSee('Type Thuiskok:');
        $response->assertSee('Zakelijke Thuiskok');
    }

    #[Test]
    public function single_account_page_shows_existing_kvk_values(): void
    {
        $admin = $this->createAdmin();
        $cook = $this->createCook([
            'kvk_naam' => 'Bestaand Bedrijf',
            'kvk_nummer' => '88776655',
            'btw_nummer' => 'NL888777666B01',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('dashboard.admin.accounts.single', $cook->getUuid()));

        $response->assertStatus(200);
        $response->assertSee('Bestaand Bedrijf');
        $response->assertSee('88776655');
        $response->assertSee('NL888777666B01');
    }

    #[Test]
    public function admin_can_clear_kvk_details(): void
    {
        $admin = $this->createAdmin();
        $cook = $this->createCook([
            'type_thuiskok' => 'Zakelijke Thuiskok',
            'kvk_naam' => 'Te Verwijderen Bedrijf',
            'kvk_nummer' => '11112222',
        ]);

        $kvkData = [
            'kvk_naam' => '',
            'kvk_nummer' => '',
        ];

        $this->actingAs($admin)
            ->patch(route('dashboard.admin.accounts.kvk.update', $cook->getUuid()), $kvkData);

        $cook->refresh();
        $this->assertNull($cook->kvk_naam);
        $this->assertNull($cook->kvk_nummer);
        // Type blijft Zakelijke Thuiskok omdat we geen wijziging doen bij lege waarden
        $this->assertEquals('Zakelijke Thuiskok', $cook->type_thuiskok);
    }

    #[Test]
    public function single_account_page_shows_email_address(): void
    {
        $admin = $this->createAdmin();
        $cook = $this->createCook();

        $response = $this->actingAs($admin)
            ->get(route('dashboard.admin.accounts.single', $cook->getUuid()));

        $response->assertStatus(200);
        $response->assertSee('E-mailadres:');
        $response->assertSee($cook->getEmail());
    }

    #[Test]
    public function single_account_page_shows_restore_button_for_deleted_account(): void
    {
        $admin = $this->createAdmin();
        $cook = $this->createCook();
        $cook->delete();

        $response = $this->actingAs($admin)
            ->get(route('dashboard.admin.accounts.single', $cook->getUuid()));

        $response->assertStatus(200);
        $response->assertSee('Account herstellen');
        $response->assertDontSee('Account blokkeren');
        $response->assertDontSee('Account verwijderen');
        $response->assertDontSee('Log in als Thuiskok');
    }

    #[Test]
    public function single_account_page_shows_block_delete_buttons_for_active_account(): void
    {
        $admin = $this->createAdmin();
        $cook = $this->createCook();

        $response = $this->actingAs($admin)
            ->get(route('dashboard.admin.accounts.single', $cook->getUuid()));

        $response->assertStatus(200);
        $response->assertSee('Log in als Thuiskok');
        $response->assertSee('Account blokkeren');
        $response->assertSee('Account verwijderen');
        $response->assertDontSee('Account herstellen');
    }

    #[Test]
    public function admin_can_restore_deleted_account(): void
    {
        $admin = $this->createAdmin();
        $cook = $this->createCook();
        $cook->delete();

        $this->assertNotNull($cook->fresh()->deleted_at);

        $response = $this->actingAs($admin)
            ->get(route('dashboard.admin.accounts.restore', $cook->getUuid()));

        $response->assertRedirect(route('dashboard.admin.accounts.single', $cook->getUuid()));
        $response->assertSessionHas('message', 'Account is succesvol hersteld.');
        $this->assertNull($cook->fresh()->deleted_at);
    }

    /**
     * Creëert een admin gebruiker.
     */
    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole(Roles::ADMIN);

        return $admin;
    }

    /**
     * Creëert een kok gebruiker met alle benodigde relaties.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function createCook(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
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
