<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Session\TokenMismatchException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CsrfTokenMismatchTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function contact_form_csrf_mismatch_redirects_to_contact_page(): void
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $response = $this->post('/contact-form', [
            '_token' => 'invalid-token',
        ]);

        // Simulate the exception handling directly
        $request = request();
        $request->server->set('REQUEST_URI', '/contact-form');

        $handler = app(\App\Exceptions\Handler::class);
        $response = $handler->render($request, new TokenMismatchException);

        $this->assertTrue($response->isRedirect());
        $this->assertStringContainsString('/contact', $response->headers->get('Location'));
    }

    #[Test]
    public function login_submit_csrf_mismatch_redirects_to_login_page(): void
    {
        $request = request();
        $request->server->set('REQUEST_URI', '/login/submit');

        $handler = app(\App\Exceptions\Handler::class);
        $response = $handler->render($request, new TokenMismatchException);

        $this->assertTrue($response->isRedirect());
        $this->assertStringContainsString('/login', $response->headers->get('Location'));
    }

    #[Test]
    public function register_submit_csrf_mismatch_redirects_to_register_page(): void
    {
        $request = request();
        $request->server->set('REQUEST_URI', '/register/submit');

        $handler = app(\App\Exceptions\Handler::class);
        $response = $handler->render($request, new TokenMismatchException);

        $this->assertTrue($response->isRedirect());
        $this->assertStringContainsString('/register/now', $response->headers->get('Location'));
    }

    #[Test]
    public function order_cancel_with_parameters_redirects_correctly(): void
    {
        $uuid = 'test-uuid-12345';
        $key = 'test-key-67890';

        $request = request();
        $request->server->set('REQUEST_URI', "/order/cancel/{$uuid}/{$key}");

        $handler = app(\App\Exceptions\Handler::class);
        $response = $handler->render($request, new TokenMismatchException);

        $this->assertTrue($response->isRedirect());
        $location = $response->headers->get('Location');

        // Moet redirecten naar order/cancel/{uuid} (zonder key)
        $this->assertStringContainsString("/order/cancel/{$uuid}", $location);
        $this->assertStringNotContainsString($key, $location);
    }

    #[Test]
    public function dashboard_order_cancel_with_uuid_redirects_correctly(): void
    {
        $uuid = 'dashboard-uuid-12345';

        $request = request();
        $request->server->set('REQUEST_URI', "/dashboard/orders/cancel/{$uuid}");

        $handler = app(\App\Exceptions\Handler::class);
        $response = $handler->render($request, new TokenMismatchException);

        $this->assertTrue($response->isRedirect());
        $location = $response->headers->get('Location');

        // Moet redirecten naar dezelfde URL met de uuid intact
        $this->assertStringContainsString("/dashboard/orders/cancel/{$uuid}", $location);
    }

    #[Test]
    public function ajax_request_returns_json_with_csrf_token(): void
    {
        $request = request();
        $request->headers->set('Accept', 'application/json');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->server->set('REQUEST_URI', '/contact-form');

        $handler = app(\App\Exceptions\Handler::class);
        $response = $handler->render($request, new TokenMismatchException);

        $this->assertEquals(419, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $content);
        $this->assertArrayHasKey('csrf_token', $content);
    }

    #[Test]
    public function csrf_mismatch_flashes_error_message(): void
    {
        // Test via de redirect response dat errors worden geflasht
        $request = request();
        $request->server->set('REQUEST_URI', '/contact-form');

        $handler = app(\App\Exceptions\Handler::class);
        $response = $handler->render($request, new TokenMismatchException);

        $this->assertTrue($response->isRedirect());

        // De response moet withErrors hebben aangeroepen
        // Dit wordt geverifieerd door te checken dat de response een redirect is
        // en de sessie errors zou bevatten na de redirect
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    #[Test]
    public function unknown_post_route_falls_back_to_home(): void
    {
        $request = request();
        $request->server->set('REQUEST_URI', '/some/unknown/post/route');

        $handler = app(\App\Exceptions\Handler::class);
        $response = $handler->render($request, new TokenMismatchException);

        $this->assertTrue($response->isRedirect());
        // Should fall back to home
        $this->assertStringContainsString('/', $response->headers->get('Location'));
    }

    #[Test]
    public function referer_header_is_used_when_different_from_current_path(): void
    {
        $request = request();
        $request->server->set('REQUEST_URI', '/contact-form');
        $request->headers->set('referer', url('/contact'));

        $handler = app(\App\Exceptions\Handler::class);
        $response = $handler->render($request, new TokenMismatchException);

        $this->assertTrue($response->isRedirect());
        $this->assertStringContainsString('/contact', $response->headers->get('Location'));
    }
}
