<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class DashboardPage extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/dashboard/adverts/active';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathBeginsWith('/dashboard');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array<string, string>
     */
    public function elements(): array
    {
        return [
            '@sidebar' => '.sidebar, aside, [class*="sidebar"]',
            '@active-adverts-link' => 'a[href*="adverts/active"]',
            '@past-adverts-link' => 'a[href*="adverts/past"]',
            '@dishes-link' => 'a[href*="dishes"]',
            '@orders-link' => 'a[href*="orders"]',
            '@settings-link' => 'a[href*="settings"]',
            '@wallet-link' => 'a[href*="wallet"]',
            '@logout-link' => 'a[href*="logout"]',
            '@user-menu' => '.user-menu, [class*="user-menu"], .dropdown',
        ];
    }

    /**
     * Navigate to a dashboard section using sidebar.
     */
    public function navigateTo(Browser $browser, string $section): void
    {
        $sectionMap = [
            'adverts' => '@active-adverts-link',
            'past-adverts' => '@past-adverts-link',
            'dishes' => '@dishes-link',
            'orders' => '@orders-link',
            'settings' => '@settings-link',
            'wallet' => '@wallet-link',
        ];

        if (isset($sectionMap[$section])) {
            $browser->click($sectionMap[$section])
                ->pause(500);
        }
    }

    /**
     * Logout from dashboard.
     */
    public function logout(Browser $browser): void
    {
        $browser->click('@logout-link')
            ->pause(1000);
    }
}
