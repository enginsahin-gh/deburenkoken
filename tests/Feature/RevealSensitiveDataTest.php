<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\Banking;
use App\Models\Cook;
use App\Models\SensitiveDataAccessLog;
use App\Models\User;
use App\Models\UserProfile;
use App\Support\SensitiveDataMasker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RevealSensitiveDataTest extends TestCase
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
    public function admin_can_reveal_iban(): void
    {
        $admin = $this->createAdmin();
        $cook = $this->createCookWithBanking('NL91ABNA0417164300');

        $response = $this->actingAs($admin)
            ->postJson(route('dashboard.admin.reveal.sensitive.data'), [
                'user_uuid' => $cook->uuid,
                'field_type' => 'iban',
            ]);

        $response->assertOk();
        $response->assertJson(['value' => 'NL91ABNA0417164300']);
        $this->assertDatabaseHas('sensitive_data_access_logs', [
            'admin_user_uuid' => $admin->uuid,
            'target_user_uuid' => $cook->uuid,
            'field_type' => 'iban',
        ]);
    }

    #[Test]
    public function admin_can_reveal_bsn(): void
    {
        $admin = $this->createAdmin();
        $cook = $this->createCookWithBanking('NL91ABNA0417164300', '123456789');

        $response = $this->actingAs($admin)
            ->postJson(route('dashboard.admin.reveal.sensitive.data'), [
                'user_uuid' => $cook->uuid,
                'field_type' => 'bsn',
            ]);

        $response->assertOk();
        $response->assertJson(['value' => '123456789']);
        $this->assertDatabaseHas('sensitive_data_access_logs', [
            'field_type' => 'bsn',
        ]);
    }

    #[Test]
    public function audit_log_records_correct_data(): void
    {
        $admin = $this->createAdmin();
        $cook = $this->createCookWithBanking('NL91ABNA0417164300');

        $this->actingAs($admin)
            ->postJson(route('dashboard.admin.reveal.sensitive.data'), [
                'user_uuid' => $cook->uuid,
                'field_type' => 'iban',
            ]);

        $log = SensitiveDataAccessLog::first();
        $this->assertNotNull($log);
        $this->assertEquals($admin->uuid, $log->admin_user_uuid);
        $this->assertEquals($cook->uuid, $log->target_user_uuid);
        $this->assertEquals('iban', $log->field_type);
        $this->assertNotNull($log->ip_address);
    }

    #[Test]
    public function non_admin_cannot_reveal_sensitive_data(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Roles::COOK);
        $cook = $this->createCookWithBanking('NL91ABNA0417164300');

        $response = $this->actingAs($user)
            ->postJson(route('dashboard.admin.reveal.sensitive.data'), [
                'user_uuid' => $cook->uuid,
                'field_type' => 'iban',
            ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function guest_cannot_reveal_sensitive_data(): void
    {
        $cook = $this->createCookWithBanking('NL91ABNA0417164300');

        $response = $this->postJson(route('dashboard.admin.reveal.sensitive.data'), [
            'user_uuid' => $cook->uuid,
            'field_type' => 'iban',
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function reveal_with_invalid_user_uuid_returns_validation_error(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->postJson(route('dashboard.admin.reveal.sensitive.data'), [
                'user_uuid' => 'non-existent-uuid',
                'field_type' => 'iban',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('user_uuid');
    }

    #[Test]
    public function reveal_with_invalid_field_type_returns_validation_error(): void
    {
        $admin = $this->createAdmin();
        $cook = $this->createCookWithBanking('NL91ABNA0417164300');

        $response = $this->actingAs($admin)
            ->postJson(route('dashboard.admin.reveal.sensitive.data'), [
                'user_uuid' => $cook->uuid,
                'field_type' => 'email',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('field_type');
    }

    #[Test]
    public function banking_page_shows_masked_iban(): void
    {
        $admin = $this->createAdmin();
        $this->createCookWithBanking('NL91ABNA0417164300');

        $response = $this->actingAs($admin)
            ->get(route('dashboard.admin.accounts.banking'));

        $response->assertStatus(200);
        $response->assertDontSee('NL91ABNA0417164300');
        $response->assertSee(SensitiveDataMasker::mask('NL91ABNA0417164300'));
    }

    #[Test]
    public function dac7_page_shows_masked_iban_and_bsn(): void
    {
        $admin = $this->createAdmin();
        $cook = $this->createCookWithBanking('NL91ABNA0417164300', '123456789');

        $response = $this->actingAs($admin)
            ->get(route('dashboard.admin.dac7'));

        $response->assertStatus(200);
        $response->assertDontSee('NL91ABNA0417164300');
        $response->assertDontSee('123456789');
        $response->assertSee(SensitiveDataMasker::mask('NL91ABNA0417164300'));
        $response->assertSee(SensitiveDataMasker::mask('123456789'));
    }

    #[Test]
    public function payouts_page_shows_masked_iban(): void
    {
        $admin = $this->createAdmin();
        $cook = $this->createCookWithBanking('NL91ABNA0417164300');

        // Create a payment for this user so it shows on payouts page
        \App\Models\Payment::create([
            'user_uuid' => $cook->uuid,
            'banking_uuid' => $cook->banking->uuid,
            'amount' => 100.00,
            'state' => \App\Models\Payment::INITIATED,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('dashboard.admin.payouts'));

        $response->assertStatus(200);
        $response->assertDontSee('NL91ABNA0417164300');
        $response->assertSee(SensitiveDataMasker::mask('NL91ABNA0417164300'));
    }

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole(Roles::ADMIN);

        return $admin;
    }

    private function createCookWithBanking(string $iban, ?string $bsn = null): User
    {
        $user = User::factory()->create($bsn ? ['bsn' => $bsn] : []);
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
            'account_holder' => 'Test Kok',
            'iban' => $iban,
            'validated' => true,
            'verified' => false,
        ]);

        return $user;
    }
}
