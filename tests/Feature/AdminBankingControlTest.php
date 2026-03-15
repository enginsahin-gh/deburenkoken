<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\Banking;
use App\Models\Cook;
use App\Models\User;
use App\Models\UserProfile;
use App\Support\SensitiveDataMasker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Test voor admin controle bankrekening functionaliteit.
 * Verifieert dat de admin de IBAN en bijbehorende Mollie-naam (account_holder)
 * kan inzien op het banking overzicht.
 */
class AdminBankingControlTest extends TestCase
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
    public function admin_can_view_banking_overview_page(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->get(route('dashboard.admin.accounts.banking'));

        $response->assertStatus(200);
        $response->assertSee('Controle Bankrekening');
    }

    #[Test]
    public function banking_overview_shows_dutch_column_headers(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->get(route('dashboard.admin.accounts.banking'));

        $response->assertStatus(200);
        $response->assertSee('Gebruikersnaam');
        $response->assertSee('E-mail');
        $response->assertSee('Voornaam');
        $response->assertSee('Achternaam');
        $response->assertSee('Geboortedatum');
        $response->assertSee('IBAN');
        $response->assertSee('Naam IBAN');
    }

    #[Test]
    public function banking_overview_shows_search_button_with_correct_label(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->get(route('dashboard.admin.accounts.banking'));

        $response->assertStatus(200);
        $response->assertSee('Zoeken op gebruikersnaam');
        $response->assertSee('Zoeken');
        $response->assertDontSee('Zoeken op Profielnaam');
    }

    #[Test]
    public function banking_overview_displays_account_holder_from_mollie(): void
    {
        $admin = $this->createAdmin();
        $cook = $this->createCookWithBanking(
            'NL91ABNA0417164300',
            'J. de Vries'
        );

        $response = $this->actingAs($admin)
            ->get(route('dashboard.admin.accounts.banking'));

        $response->assertStatus(200);
        $response->assertDontSee('NL91ABNA0417164300');
        $response->assertSee(SensitiveDataMasker::mask('NL91ABNA0417164300'));
        $response->assertSee('J. de Vries');
    }

    #[Test]
    public function banking_overview_shows_empty_naam_iban_when_not_set(): void
    {
        $admin = $this->createAdmin();
        $cook = $this->createCookWithBanking('NL91ABNA0417164300', '');

        $response = $this->actingAs($admin)
            ->get(route('dashboard.admin.accounts.banking'));

        $response->assertStatus(200);
        $response->assertDontSee('NL91ABNA0417164300');
        $response->assertSee(SensitiveDataMasker::mask('NL91ABNA0417164300'));
    }

    #[Test]
    public function banking_overview_shows_multiple_cooks_with_account_holders(): void
    {
        $admin = $this->createAdmin();
        $this->createCookWithBanking('NL91ABNA0417164300', 'Piet Jansen');
        $this->createCookWithBanking('NL55RABO0057320439', 'Maria Bakker');

        $response = $this->actingAs($admin)
            ->get(route('dashboard.admin.accounts.banking'));

        $response->assertStatus(200);
        $response->assertSee('Piet Jansen');
        $response->assertSee('Maria Bakker');
        $response->assertDontSee('NL91ABNA0417164300');
        $response->assertDontSee('NL55RABO0057320439');
        $response->assertSee(SensitiveDataMasker::mask('NL91ABNA0417164300'));
        $response->assertSee(SensitiveDataMasker::mask('NL55RABO0057320439'));
    }

    #[Test]
    public function banking_overview_search_filters_by_username(): void
    {
        $admin = $this->createAdmin();
        $this->createCookWithBanking('NL91ABNA0417164300', 'Zoek Resultaat', 'zoeknaam');
        $this->createCookWithBanking('NL55RABO0057320439', 'Andere Kok', 'anderekok');

        $response = $this->actingAs($admin)
            ->get(route('dashboard.admin.accounts.banking', ['query' => 'zoeknaam']));

        $response->assertStatus(200);
        $response->assertSee('zoeknaam');
        $response->assertSee('Zoek Resultaat');
        $response->assertDontSee('NL91ABNA0417164300');
        $response->assertSee(SensitiveDataMasker::mask('NL91ABNA0417164300'));
        $response->assertDontSee('anderekok');
        $response->assertDontSee('Andere Kok');
    }

    #[Test]
    public function non_admin_cannot_access_banking_overview(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->get(route('dashboard.admin.accounts.banking'));

        $response->assertStatus(403);
    }

    #[Test]
    public function guest_cannot_access_banking_overview(): void
    {
        $response = $this->get(route('dashboard.admin.accounts.banking'));

        $response->assertRedirect(route('login.home'));
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
     * Creëert een kok gebruiker met bankgegevens.
     */
    private function createCookWithBanking(
        string $iban,
        ?string $accountHolder,
        ?string $username = null
    ): User {
        $attributes = $username ? ['username' => $username] : [];
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

        Banking::create([
            'user_uuid' => $user->uuid,
            'account_holder' => $accountHolder,
            'iban' => $iban,
            'validated' => true,
            'verified' => false,
        ]);

        return $user;
    }
}
