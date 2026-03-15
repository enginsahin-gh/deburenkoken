<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class HomePage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs('/');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array<string, string>
     */
    public function elements(): array
    {
        return [
            '@hero' => '.hero, [class*="hero"], .banner, [class*="banner"]',
            '@search-form' => 'form[action*="zoeken"], form[method="get"]',
            '@search-input' => 'input[name="search"], input[type="search"], input[placeholder*="zoek"]',
            '@search-button' => 'button[type="submit"]',
            '@featured-meals' => '.featured, [class*="featured"], .meals, [class*="meals"]',
            '@login-link' => 'a[href*="login"]',
            '@register-link' => 'a[href*="register"], a[href*="aanmelden"]',
            '@how-it-works' => '.how-it-works, [class*="how-it-works"]',
        ];
    }

    /**
     * Search for meals.
     */
    public function searchFor(Browser $browser, string $query): void
    {
        $browser->type('@search-input', $query)
            ->click('@search-button')
            ->pause(500);
    }

    /**
     * Navigate to login page.
     */
    public function goToLogin(Browser $browser): void
    {
        $browser->click('@login-link')
            ->pause(500);
    }
}
