<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthenticationTest extends TestCase
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
    public function login_page_loads_successfully(): void
    {
        $response = $this->get(route('login.home'));

        $response->assertStatus(200);
    }

    #[Test]
    public function user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(302); // Redirect after login
        // Check user is logged in by trying to access a protected page
    }

    #[Test]
    public function user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
    }

    #[Test]
    public function register_info_page_loads_successfully(): void
    {
        $response = $this->get(route('register.info'));

        $response->assertStatus(200);
    }

    #[Test]
    public function register_now_page_loads_successfully(): void
    {
        $response = $this->get(route('register.now'));

        $response->assertStatus(200);
    }

    #[Test]
    public function user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('logout'));

        $response->assertStatus(302); // Redirect after logout
        $this->assertGuest();
    }

    #[Test]
    public function forgot_password_page_loads_successfully(): void
    {
        $response = $this->get(route('login.forgot'));

        $response->assertStatus(200);
    }

    #[Test]
    public function guest_is_redirected_when_accessing_protected_routes(): void
    {
        // Try to access the verification page which requires auth
        $response = $this->get('/register/information');

        $response->assertStatus(302); // Redirects to login
    }
}
