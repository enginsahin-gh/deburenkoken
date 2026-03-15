<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests voor de RegisterController, inclusief BL-196 fix voor MethodNotAllowedHttpException.
 */
class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Maak de benodigde rollen aan voor registratie
        Role::findOrCreate('cook', 'web');
        Role::findOrCreate('customer', 'web');
        Role::findOrCreate('admin', 'web');
    }

    #[Test]
    public function register_info_page_loads_successfully(): void
    {
        $response = $this->get(route('register.info'));

        $response->assertStatus(200);
        $response->assertViewIs('register-info');
    }

    #[Test]
    public function register_now_page_loads_successfully(): void
    {
        $response = $this->get(route('register.now'));

        $response->assertStatus(200);
        $response->assertViewIs('register-now');
    }

    #[Test]
    public function register_submitted_page_loads_successfully(): void
    {
        // BL-196: Na registratie moet de gebruiker naar /register/submitted worden geredirect
        // Deze GET route voorkomt 405 errors bij page refresh
        $response = $this->get(route('register.submitted'));

        $response->assertStatus(200);
        $response->assertViewIs('verification');
        $response->assertViewHas('verificationSend', true);
        $response->assertViewHas('verificationFailed', false);
    }

    #[Test]
    public function get_request_to_register_submit_returns_error(): void
    {
        // BL-196: Dit was het oorspronkelijke probleem - een GET request naar /register/submit
        // gaf een MethodNotAllowedHttpException. De Handler zet dit om naar 404 om POST-only
        // routes te verbergen voor gebruikers (security through obscurity).
        $response = $this->get('/register/submit');

        // Handler converteert MethodNotAllowedHttpException naar 404
        $response->assertStatus(404);
    }

    #[Test]
    public function successful_registration_redirects_to_submitted_page(): void
    {
        // BL-196: PRG patroon - na POST moet redirect naar GET route volgen
        // Gebruik een uniek wachtwoord dat de uncompromised check passeert
        $uniquePassword = 'TestPwd'.bin2hex(random_bytes(4)).'!Aa1';

        $response = $this->from(route('register.info'))->post(route('register.submit'), [
            'username' => 'testkok'.time(),
            'email' => 'test'.time().'@gmail.com',
            'password' => $uniquePassword,
            'password_confirmation' => $uniquePassword,
            'terms' => true,
        ]);

        $response->assertRedirect(route('register.submitted'));
    }

    #[Test]
    public function registration_fails_without_required_fields(): void
    {
        $response = $this->post(route('register.submit'), []);

        $response->assertStatus(302);
        $response->assertSessionHas('usernameRequired');
    }

    #[Test]
    public function registration_fails_with_invalid_email(): void
    {
        $response = $this->post(route('register.submit'), [
            'username' => 'testkok'.time(),
            'email' => 'invalid-email',
            'password' => 'SecurePass123!@#',
            'password_confirmation' => 'SecurePass123!@#',
            'terms' => true,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function registration_fails_with_weak_password(): void
    {
        // Gebruik gmail.com voor DNS validatie
        $response = $this->post(route('register.submit'), [
            'username' => 'testkok'.time(),
            'email' => 'test'.time().'@gmail.com',
            'password' => 'weak',
            'password_confirmation' => 'weak',
            'terms' => true,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('password');
    }

    #[Test]
    public function registration_fails_without_accepting_terms(): void
    {
        // Gebruik gmail.com voor DNS validatie
        $response = $this->post(route('register.submit'), [
            'username' => 'testkok'.time(),
            'email' => 'test'.time().'@gmail.com',
            'password' => 'SecurePass123!@#',
            'password_confirmation' => 'SecurePass123!@#',
            // terms niet geaccepteerd
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('terms');
    }

    #[Test]
    public function page_refresh_after_registration_does_not_cause_405_error(): void
    {
        // BL-196: Het kernprobleem was dat na registratie de URL /register/submit was
        // en een page refresh (GET) een 405 error gaf.
        // Met PRG patroon wordt de gebruiker nu geredirect naar /register/submitted
        // waar een page refresh gewoon de pagina opnieuw laadt.
        $uniquePassword = 'TestPwd'.bin2hex(random_bytes(4)).'!Aa1';

        // Stap 1: Registreer
        $response = $this->from(route('register.info'))->post(route('register.submit'), [
            'username' => 'testkok'.time(),
            'email' => 'test'.time().'@gmail.com',
            'password' => $uniquePassword,
            'password_confirmation' => $uniquePassword,
            'terms' => true,
        ]);

        $response->assertRedirect(route('register.submitted'));

        // Stap 2: Volg de redirect (dit simuleert wat de browser doet)
        $followedResponse = $this->get(route('register.submitted'));
        $followedResponse->assertStatus(200);
        $followedResponse->assertViewIs('verification');

        // Stap 3: Simuleer page refresh (opnieuw GET naar dezelfde URL)
        $refreshResponse = $this->get(route('register.submitted'));
        $refreshResponse->assertStatus(200);
        $refreshResponse->assertViewIs('verification');
    }

    #[Test]
    public function registration_creates_user_and_wallet(): void
    {
        $email = 'newuser'.time().'@gmail.com';
        $username = 'newuser'.time();
        $uniquePassword = 'TestPwd'.bin2hex(random_bytes(4)).'!Aa1';

        $response = $this->from(route('register.info'))->post(route('register.submit'), [
            'username' => $username,
            'email' => $email,
            'password' => $uniquePassword,
            'password_confirmation' => $uniquePassword,
            'terms' => true,
        ]);

        $response->assertRedirect(route('register.submitted'));

        // Controleer dat de gebruiker is aangemaakt
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'username' => $username,
        ]);

        // Controleer dat de wallet is aangemaakt
        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertDatabaseHas('wallets', [
            'user_uuid' => $user->uuid,
        ]);
    }
}
