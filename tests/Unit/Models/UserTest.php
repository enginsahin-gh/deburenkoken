<?php

namespace Tests\Unit\Models;

use App\Constants\Roles;
use App\Models\Cook;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles if they don't exist
        Role::findOrCreate(Roles::CUSTOMER, 'web');
        Role::findOrCreate(Roles::COOK, 'web');
        Role::findOrCreate(Roles::ADMIN, 'web');
    }

    #[Test]
    public function user_uses_uuid_as_primary_key(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->uuid);
        $this->assertIsString($user->uuid);
        $this->assertEquals(36, strlen($user->uuid));
    }

    #[Test]
    public function user_can_be_soft_deleted(): void
    {
        $user = User::factory()->create();

        $user->delete();

        $this->assertSoftDeleted($user);
        $this->assertNotNull(User::withTrashed()->find($user->uuid));
    }

    #[Test]
    public function user_can_have_customer_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Roles::CUSTOMER);

        $this->assertTrue($user->hasRole(Roles::CUSTOMER));
        $this->assertFalse($user->hasRole(Roles::COOK));
        $this->assertFalse($user->hasRole(Roles::ADMIN));
    }

    #[Test]
    public function user_can_have_cook_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Roles::COOK);

        $this->assertTrue($user->hasRole(Roles::COOK));
        $this->assertFalse($user->hasRole(Roles::CUSTOMER));
        $this->assertFalse($user->hasRole(Roles::ADMIN));
    }

    #[Test]
    public function user_can_have_admin_role(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Roles::ADMIN);

        $this->assertTrue($user->hasRole(Roles::ADMIN));
    }

    #[Test]
    public function user_can_have_cook_profile(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        $this->assertInstanceOf(Cook::class, $user->cook);
        $this->assertEquals($cook->uuid, $user->cook->uuid);
    }

    #[Test]
    public function user_can_be_blocked(): void
    {
        $user = User::factory()->blocked()->create();

        $this->assertTrue($user->blocked_by_admin);
    }

    #[Test]
    public function user_returns_parsed_uuid(): void
    {
        $user = User::factory()->create();

        $parsedUuid = $user->getParsedUuid();
        $this->assertEquals(6, strlen($parsedUuid));
        $this->assertEquals(substr($user->uuid, -6), $parsedUuid);
    }

    #[Test]
    public function user_has_username_attribute(): void
    {
        $user = User::factory()->create(['username' => 'testuser']);

        $this->assertEquals('testuser', $user->username);
    }

    #[Test]
    public function user_email_must_be_verified(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->email_verified_at);

        $unverifiedUser = User::factory()->unverified()->create();
        $this->assertNull($unverifiedUser->email_verified_at);
    }
}
