<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class LoginPage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/login';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs('/login')
            ->assertSee('Inloggen');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array<string, string>
     */
    public function elements(): array
    {
        return [
            '@email' => 'input[name="email"]',
            '@password' => 'input[name="password"]',
            '@remember' => 'input[name="remember"]',
            '@submit' => 'button[type="submit"]',
            '@error-message' => '.alert-danger, .error, [class*="error"]',
        ];
    }

    /**
     * Login with given credentials.
     */
    public function loginAs(Browser $browser, string $email, string $password): void
    {
        $browser->dismissOverlays()
            ->type('@email', $email)
            ->type('@password', $password)
            ->press('Inloggen')
            ->pause(3000)
            ->dismissOverlays();
    }
}
