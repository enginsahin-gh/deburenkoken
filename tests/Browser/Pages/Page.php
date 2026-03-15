<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Page as BasePage;

abstract class Page extends BasePage
{
    /**
     * Get the global element shortcuts for the site.
     * These selectors are available on all pages.
     *
     * @return array<string, string>
     */
    public static function siteElements(): array
    {
        return [
            // Cookie consent
            '@cookie-banner' => '.cookie-consent-banner, [class*="cookie-consent"]',
            '@cookie-accept' => 'button[value="accept"]',
            '@cookie-essential' => 'button[value="essential"]',

            // Intro.js overlays
            '@intro-overlay' => '.introjs-overlay',
            '@intro-skip' => '.introjs-skipbutton',
            '@intro-done' => '.introjs-donebutton',

            // Navigation
            '@main-nav' => 'nav, header nav',
            '@header' => 'header',
            '@footer' => 'footer',
            '@sidebar' => '.sidebar, aside, [class*="sidebar"]',

            // Common form elements
            '@email-input' => 'input[name="email"]',
            '@password-input' => 'input[name="password"]',
            '@submit-button' => 'button[type="submit"]',

            // Dashboard elements
            '@dashboard-content' => '.dashboard-content, main, [class*="content"]',
        ];
    }
}
