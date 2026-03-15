<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\SensitiveDataAccessLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SensitiveDataAuditLogsTest extends TestCase
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
    public function admin_can_view_audit_logs_page(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->get(route('dashboard.admin.audit-logs'));

        $response->assertStatus(200);
        $response->assertSee('Inzageregistratie');
        $response->assertSee('Datum & tijd', false);
        $response->assertSee('Beheerder');
        $response->assertSee('Betroffen gebruiker');
        $response->assertSee('Type gegeven');
        $response->assertSee('IP-adres');
    }

    #[Test]
    public function audit_logs_page_shows_log_entries(): void
    {
        $admin = $this->createAdmin();
        $targetUser = User::factory()->create(['username' => 'targetuser']);
        $targetUser->assignRole(Roles::COOK);

        SensitiveDataAccessLog::create([
            'admin_user_uuid' => $admin->uuid,
            'target_user_uuid' => $targetUser->uuid,
            'field_type' => 'iban',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('dashboard.admin.audit-logs'));

        $response->assertStatus(200);
        $response->assertSee($admin->username);
        $response->assertSee('targetuser');
        $response->assertSee('iban');
        $response->assertSee('127.0.0.1');
    }

    #[Test]
    public function audit_logs_page_search_filters_by_username(): void
    {
        $admin = $this->createAdmin();
        $matchingUser = User::factory()->create(['username' => 'zoekgebruiker']);
        $matchingUser->assignRole(Roles::COOK);
        $otherUser = User::factory()->create(['username' => 'anderegebruiker']);
        $otherUser->assignRole(Roles::COOK);

        SensitiveDataAccessLog::create([
            'admin_user_uuid' => $admin->uuid,
            'target_user_uuid' => $matchingUser->uuid,
            'field_type' => 'iban',
            'ip_address' => '10.0.0.1',
        ]);

        SensitiveDataAccessLog::create([
            'admin_user_uuid' => $admin->uuid,
            'target_user_uuid' => $otherUser->uuid,
            'field_type' => 'bsn',
            'ip_address' => '10.0.0.2',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('dashboard.admin.audit-logs', ['search' => 'zoekgebruiker']));

        $response->assertStatus(200);
        $response->assertSee('zoekgebruiker');
        $response->assertDontSee('anderegebruiker');
    }

    #[Test]
    public function non_admin_cannot_view_audit_logs_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Roles::COOK);

        $response = $this->actingAs($user)
            ->get(route('dashboard.admin.audit-logs'));

        $response->assertStatus(403);
    }

    #[Test]
    public function guest_cannot_view_audit_logs_page(): void
    {
        $response = $this->get(route('dashboard.admin.audit-logs'));

        $response->assertRedirect(route('login.home'));
    }

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole(Roles::ADMIN);

        return $admin;
    }
}
