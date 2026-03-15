<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\ViewErrorBag;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CookieConsentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function cookie_banner_is_shown_when_no_cookies_accepted(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Wij gebruiken cookies');
        $response->assertSee('Accepteer');
        $response->assertSee('Essentiële');
    }

    #[Test]
    public function cookie_banner_is_hidden_when_all_cookies_accepted(): void
    {
        $response = $this->withCookie('accepted_cookies', '1')
            ->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Wij gebruiken cookies');
    }

    #[Test]
    public function cookie_banner_is_hidden_when_essential_cookies_accepted(): void
    {
        $response = $this->withCookie('essential_cookies', '1')
            ->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Wij gebruiken cookies');
    }

    #[Test]
    public function accepting_all_cookies_sets_correct_cookie(): void
    {
        $response = $this->post(route('accepted.cookies.response'), [
            'cookierights' => 'accept',
        ]);

        $response->assertStatus(302);
        $response->assertCookie('accepted_cookies');
    }

    #[Test]
    public function declining_cookies_sets_essential_cookie(): void
    {
        $response = $this->post(route('accepted.cookies.response'), [
            'cookierights' => 'decline',
        ]);

        $response->assertStatus(302);
        $response->assertCookie('essential_cookies');
    }

    #[Test]
    public function login_page_loads_without_cookie_consent(): void
    {
        $response = $this->get(route('login.home'));

        $response->assertStatus(200);
        $response->assertSee('Inloggen');
    }

    #[Test]
    public function login_page_loads_with_essential_cookies_only(): void
    {
        $response = $this->withCookie('essential_cookies', '1')
            ->get(route('login.home'));

        $response->assertStatus(200);
        $response->assertSee('Inloggen');
    }

    #[Test]
    public function register_page_loads_without_cookie_consent(): void
    {
        $response = $this->get(route('register.info'));

        $response->assertStatus(200);
        $response->assertSee('Registreer');
    }

    /**
     * @return array<string, array{cookieName: string|null, cookieValue: string|null, expectedAccepted: bool, expectedEssential: bool}>
     */
    public static function cookieConsentDataProvider(): array
    {
        return [
            'no cookies' => [
                'cookieName' => null,
                'cookieValue' => null,
                'expectedAccepted' => false,
                'expectedEssential' => false,
            ],
            'accepted_cookies set' => [
                'cookieName' => 'accepted_cookies',
                'cookieValue' => '1',
                'expectedAccepted' => true,
                'expectedEssential' => false,
            ],
            'essential_cookies set' => [
                'cookieName' => 'essential_cookies',
                'cookieValue' => '1',
                'expectedAccepted' => true,
                'expectedEssential' => true,
            ],
        ];
    }

    #[Test]
    #[DataProvider('cookieConsentDataProvider')]
    public function cookie_consent_middleware_shares_correct_variables(
        ?string $cookieName,
        ?string $cookieValue,
        bool $expectedAccepted,
        bool $expectedEssential
    ): void {
        $request = $this;

        if ($cookieName !== null) {
            $request = $this->withCookie($cookieName, $cookieValue);
        }

        $response = $request->get('/');

        $response->assertViewHas('acceptedCookies', $expectedAccepted);
        $response->assertViewHas('essentialCookies', $expectedEssential);
    }

    #[Test]
    public function register_form_hides_csrf_error_when_no_errors(): void
    {
        $response = $this->get(route('register.info'));

        $response->assertStatus(200);
        $response->assertDontSee('fa-exclamation-triangle');
    }

    #[Test]
    public function register_form_displays_csrf_error_when_present(): void
    {
        $errors = new ViewErrorBag;
        $errors->put('default', new \Illuminate\Support\MessageBag(['csrf' => 'Je sessie is verlopen.']));

        $response = $this->withSession(['errors' => $errors])
            ->get(route('register.info'));

        $response->assertStatus(200);
        $response->assertSee('Je sessie is verlopen.');
        $response->assertSee('fa-exclamation-triangle');
    }

    #[Test]
    public function login_form_hides_csrf_error_when_no_errors(): void
    {
        $response = $this->get(route('login.home'));

        $response->assertStatus(200);
        $response->assertDontSee('fa-exclamation-triangle');
    }

    #[Test]
    public function login_form_displays_csrf_error_when_present(): void
    {
        $errors = new ViewErrorBag;
        $errors->put('default', new \Illuminate\Support\MessageBag(['csrf' => 'Je sessie is verlopen.']));

        $response = $this->withSession(['errors' => $errors])
            ->get(route('login.home'));

        $response->assertStatus(200);
        $response->assertSee('Je sessie is verlopen.');
        $response->assertSee('fa-exclamation-triangle');
    }
}
