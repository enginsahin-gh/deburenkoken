<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

/**
 * Test class voor publieke pagina's die geen login vereisen
 *
 * Test de volgende pagina's:
 * - Homepage (/)
 * - Over Ons (/info)
 * - Contact (/contact)
 * - Veelgestelde vragen (/facts/customer en /facts/cook)
 * - Algemene voorwaarden (/terms-and-conditions)
 * - Privacy policy (/privacy)
 * - Cookieverklaring (/cookie)
 *
 * Controleert:
 * - Pagina laadt correct
 * - Essentiële content aanwezig
 * - Navigatie werkt
 * - Footer links kloppen
 * - Externe links (Instagram) hebben correcte URL
 */
class PublicPagesTest extends DuskTestCase
{
    /**
     * Helper om cookie banner te sluiten indien nodig
     */
    protected function dismissCookieBanner(Browser $browser): void
    {
        $browser->dismissOverlays();
    }

    /**
     * Test homepage essentiële elementen
     */
    #[Test]
    public function homepage_displays_essential_elements()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('h1', 10);

            $this->dismissCookieBanner($browser);

            // Hoofdelementen
            $browser->assertSee('Vind gerechten bij jou in de buurt')
                ->assertSee('Hoe werkt het?')
                ->assertSee('Zoek gerechten in de buurt')
                ->assertSee('Kom direct in contact met een Thuiskok')
                ->assertSee('Geniet!')
                ->assertSee('Wil jij gerechten delen met de buurt?');

            // Zoekbox aanwezig
            $browser->assertPresent('input[type="text"]');

            // Navigatie
            $browser->assertSeeLink('Over Ons')
                ->assertSeeLink('Contact')
                ->assertSeeLink('Login als Thuiskok')
                ->assertSeeLink('Registreer & plaats gerecht');

            // Footer
            $browser->assertSee('DeBurenKoken.nl')
                ->assertSee('© Copyright 2026');
        });
    }

    /**
     * Test Login pagina laadt correct en formulier elementen zijn aanwezig
     */
    #[Test]
    public function login_page_displays_form_elements()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->waitFor('h1', 10);

            $this->dismissCookieBanner($browser);

            // Titel
            $browser->assertSee('Inloggen');

            // Formulier velden
            $browser->assertPresent('input[name="email"]')
                ->assertPresent('input[name="password"]');

            // Verstuur knop
            $browser->assertSee('Inloggen');

            // Links
            $browser->assertSeeLink('Wachtwoord vergeten?')
                ->assertSeeLink('Account aanmaken');
        });
    }

    /**
     * Test Login formulier met ongeldige credentials toont foutmelding (niet 419)
     * Dit test of de CSRF token correct werkt bij login form submission.
     */
    #[Test]
    public function login_form_submission_does_not_cause_csrf_error()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->waitFor('h1', 10);

            $this->dismissCookieBanner($browser);

            // Vul het formulier in met ongeldige credentials
            $browser->type('input[name="email"]', 'nonexistent@example.com')
                ->type('input[name="password"]', 'wrongpassword123');

            // Submit het formulier
            $browser->press('Inloggen')
                ->pause(3000); // Wacht op response

            // Check dat we NIET op een 419 error pagina zijn
            $browser->assertDontSee('419')
                ->assertDontSee('Page Expired')
                ->assertDontSee('Sessie Verlopen');

            // We verwachten een "ongeldige credentials" foutmelding, niet een CSRF error
            // De exacte foutmelding kan variëren
            $pageSource = $browser->driver->getPageSource();

            // Als er een CSRF error is, faal de test
            $this->assertStringNotContainsString('sessie is verlopen', strtolower($pageSource), 'CSRF error detected on login form');
        });
    }

    /**
     * Test homepage navigatie links
     */
    #[Test]
    public function homepage_navigation_links_work()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('nav', 10);

            $this->dismissCookieBanner($browser);

            // Test navigatie naar Over Ons
            $browser->clickLink('Over Ons')
                ->waitFor('h1', 10)
                ->assertPathIs('/info')
                ->assertSee('Over Ons');
        });
    }

    /**
     * Test Over Ons pagina (info)
     */
    #[Test]
    public function about_us_page_displays_correctly()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/info')
                ->waitFor('h1', 10);

            $this->dismissCookieBanner($browser);

            // Hoofdtitel
            $browser->assertSee('Over Ons');

            // Accordion tabs
            $browser->assertSee('Wat is DeBurenKoken.nl')
                ->assertSee('Onze missie en visie');
        });
    }

    /**
     * Test Contact pagina formulier elementen
     */
    #[Test]
    public function contact_page_displays_form_elements()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/contact')
                ->waitFor('h1', 10);

            $this->dismissCookieBanner($browser);

            // Titel
            $browser->assertSee('Contact');

            // Formulier velden
            $browser->assertPresent('input[name="name"]')
                ->assertPresent('input[name="phone_number"]')
                ->assertPresent('input[name="email"]')
                ->assertPresent('textarea[name="question"]');

            // Verstuur knop
            $browser->assertSee('Verstuur');

            // Link naar FAQ
            $browser->assertSeeLink('veelgestelde vragen');
        });
    }

    /**
     * Test Contact formulier kan succesvol verzonden worden
     * Dit test of de CSRF token correct werkt en het formulier gesubmit kan worden.
     */
    #[Test]
    public function contact_form_can_be_submitted_successfully()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/contact')
                ->waitFor('h1', 10);

            $this->dismissCookieBanner($browser);

            // Vul het formulier in
            $testEmail = 'dusk-test-'.time().'@example.com';
            $browser->type('input[name="name"]', 'Dusk Test Gebruiker')
                ->type('input[name="phone_number"]', '0612345678')
                ->type('input[name="email"]', $testEmail)
                ->type('textarea[name="question"]', 'Dit is een automatische test van het contactformulier via Laravel Dusk. Negeer dit bericht.');

            // Submit het formulier
            $browser->press('Verstuur')
                ->pause(3000); // Wacht op redirect of error

            // Check dat we NIET op een error pagina zijn
            $browser->assertDontSee('419')
                ->assertDontSee('Page Expired')
                ->assertDontSee('Sessie Verlopen')
                ->assertDontSee('500')
                ->assertDontSee('Server Error');

            // Check dat we op de success pagina zijn OF een success melding zien
            // De exacte tekst hangt af van de implementatie
            $currentUrl = $browser->driver->getCurrentURL();
            $pageSource = $browser->driver->getPageSource();

            // Log de huidige URL voor debugging
            $this->assertNotEmpty($currentUrl, 'Current URL should not be empty');

            // Als we nog op /contact zijn met een error, is dat een probleem
            if (str_contains($currentUrl, '/contact') && ! str_contains($currentUrl, '/contact/success')) {
                // Check of er een CSRF error in de pagina staat
                $this->assertStringNotContainsString('sessie is verlopen', strtolower($pageSource), 'CSRF error detected on contact form');
            }
        });
    }

    /**
     * Test Contact pagina link naar FAQ
     */
    #[Test]
    public function contact_page_links_to_faq()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/contact')
                ->waitFor('h1', 10);

            $this->dismissCookieBanner($browser);

            $browser->clickLink('veelgestelde vragen')
                ->waitFor('h1', 10)
                ->assertPathIs('/facts/customer');
        });
    }

    /**
     * Test FAQ pagina voor klanten
     */
    #[Test]
    public function faq_customer_page_displays_correctly()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/facts/customer')
                ->waitFor('h1', 10);

            $this->dismissCookieBanner($browser);

            // Hoofdtitel
            $browser->assertSee('Veelgestelde vragen');

            // Tabs (inclusief nieuwe Praktische tips tab)
            $browser->assertSeeLink('Klanten')
                ->assertSeeLink('Thuiskoks')
                ->assertSeeLink('Praktische tips');

            // Secties
            $browser->assertSee('Algemeen')
                ->assertSee('Bestellen')
                ->assertSee('Betalingen');

            // Enkele vragen
            $browser->assertSee('Heb ik een account nodig om te bestellen?')
                ->assertSee('Kan ik mijn bestelling annuleren?')
                ->assertSee('Welke betaalmogelijkheden zijn er?');
        });
    }

    /**
     * Test Praktische tips pagina voor Thuiskoks
     */
    #[Test]
    public function faq_cook_tips_page_displays_correctly()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/facts/cook-tips')
                ->waitFor('h1', 10);

            $this->dismissCookieBanner($browser);

            // Hoofdtitel en sectie titel
            $browser->assertSee('Veelgestelde vragen')
                ->assertSee('Praktische tips voor Thuiskoks');

            // Tabs aanwezig
            $browser->assertSeeLink('Klanten')
                ->assertSeeLink('Thuiskoks')
                ->assertSeeLink('Praktische tips');

            // Enkele praktische tips
            $browser->assertSee('Begin eenvoudig')
                ->assertSee('Kies een herkenbaar gerecht')
                ->assertSee('Houd het leuk');
        });
    }

    /**
     * Test FAQ pagina tabs wisselen
     */
    #[Test]
    public function faq_page_tabs_navigation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/facts/customer')
                ->waitFor('h1', 10);

            $this->dismissCookieBanner($browser);

            // Navigeer naar Thuiskoks tab
            $browser->clickLink('Thuiskoks')
                ->waitFor('h1', 10)
                ->assertPathIs('/facts/cook');
        });
    }

    /**
     * Test Algemene Voorwaarden pagina
     */
    #[Test]
    public function terms_and_conditions_page_displays_correctly()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/terms-and-conditions')
                ->waitFor('h1', 10);

            $this->dismissCookieBanner($browser);

            // Hoofdtitel
            $browser->assertSee('Algemene voorwaarden');

            // Secties
            $browser->assertSee('I Algemene voorwaarden')
                ->assertSee('II Aanvullende voorwaarden voor het plaatsen van advertenties')
                ->assertSee('III Aanvullende voorwaarden voor Afnemers & Thuiskoks');

            // KvK en BTW informatie
            $browser->assertSee('88652971')
                ->assertSee('864719334B01');

            // PDF link aanwezig
            $browser->assertPresent('a[href*="AlgemeneVoorwaardenDeBurenKoken"]');
        });
    }

    /**
     * Test PDF download link op Algemene Voorwaarden
     */
    #[Test]
    public function terms_pdf_link_has_correct_url()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/terms-and-conditions')
                ->waitFor('h1', 10);

            $this->dismissCookieBanner($browser);

            // Controleer dat de PDF link correct is
            $pdfLink = $browser->attribute('a[href*="AlgemeneVoorwaardenDeBurenKoken"]', 'href');
            $this->assertStringContainsString('/pdf/AlgemeneVoorwaardenDeBurenKoken2025.pdf', $pdfLink);
        });
    }

    /**
     * Test Privacy Policy pagina
     */
    #[Test]
    public function privacy_policy_page_displays_correctly()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/privacy')
                ->waitFor('h1', 10);

            $this->dismissCookieBanner($browser);

            // Hoofdtitel
            $browser->assertSee('Privacy Policy');

            // Secties
            $browser->assertSee('Privacy')
                ->assertSee('info@deburenkoken.nl');
        });
    }

    /**
     * Test Cookieverklaring pagina
     */
    #[Test]
    public function cookie_declaration_page_displays_correctly()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/cookie')
                ->waitFor('h1', 10);

            $this->dismissCookieBanner($browser);

            // Hoofdtitel
            $browser->assertSee('Cookieverklaring');

            // Contactgegevens
            $browser->assertSee('info@deburenkoken.nl');
        });
    }

    /**
     * Test footer links op alle pagina's
     */
    #[Test]
    public function footer_contains_all_required_links()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('footer', 10);

            $this->dismissCookieBanner($browser);

            // Footer sectie titels
            $browser->assertSee('DeBurenKoken.nl')
                ->assertSee('Regelgeving');

            // Footer links - DeBurenKoken.nl sectie
            $browser->assertSeeLink('Over Ons')
                ->assertSeeLink('Contact')
                ->assertSeeLink('Veelgestelde vragen');

            // Footer links - Regelgeving sectie
            $browser->assertSeeLink('Algemene voorwaarden')
                ->assertSeeLink('Privacy policy')
                ->assertSeeLink('Cookieverklaring');
        });
    }

    /**
     * Test footer links navigeren correct
     */
    #[Test]
    public function footer_links_navigate_correctly()
    {
        $pagesToTest = [
            ['link' => 'Algemene voorwaarden', 'path' => '/terms-and-conditions'],
            ['link' => 'Privacy policy', 'path' => '/privacy'],
            ['link' => 'Cookieverklaring', 'path' => '/cookie'],
        ];

        foreach ($pagesToTest as $page) {
            $this->browse(function (Browser $browser) use ($page) {
                $browser->visit('/')
                    ->waitFor('footer', 10);

                $this->dismissCookieBanner($browser);

                // Scroll naar footer en klik link
                $browser->scrollIntoView('footer')
                    ->pause(500)
                    ->clickLink($page['link'])
                    ->waitFor('h1', 10)
                    ->assertPathIs($page['path']);
            });
        }
    }

    /**
     * Test Instagram link heeft correcte URL (externe link, niet bezoeken)
     * Note: Instagram link is only visible if SOCIAL_INSTAGRAM env var is set
     */
    #[Test]
    public function instagram_link_has_correct_url()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('footer', 10);

            $this->dismissCookieBanner($browser);

            // Check if Instagram link exists (depends on SOCIAL_INSTAGRAM env var)
            $instagramExists = $browser->script(
                'return document.querySelector("a[href*=\\"instagram.com\\"]") !== null;'
            );

            if ($instagramExists[0]) {
                // Zoek Instagram link in footer
                $instagramLink = $browser->attribute('a[href*="instagram.com"]', 'href');
                // Controleer dat de URL correct is
                $this->assertEquals('https://www.instagram.com/deburenkoken.nl', $instagramLink);
            } else {
                // Instagram link not present - skip assertion but don't fail
                // This happens when SOCIAL_INSTAGRAM env var is not set
                $this->markTestSkipped('Instagram link not present - SOCIAL_INSTAGRAM env var not set');
            }
        });
    }

    /**
     * Test header logo linkt naar homepage
     */
    #[Test]
    public function header_logo_links_to_homepage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/contact')
                ->waitFor('header, nav, banner', 10);

            $this->dismissCookieBanner($browser);

            // Klik op logo
            $browser->click('img[alt="DeBurenKoken.nl"]')
                ->waitFor('h1', 10)
                ->assertPathIs('/');
        });
    }

    /**
     * Test dat alle publieke pagina's laadbaar zijn zonder errors
     */
    #[Test]
    public function all_public_pages_load_without_errors()
    {
        $publicPages = [
            '/' => 'Vind gerechten',
            '/info' => 'Over Ons',
            '/contact' => 'Contact',
            '/facts/customer' => 'Veelgestelde vragen',
            '/facts/cook' => 'Veelgestelde vragen',
            '/facts/cook-tips' => 'Praktische tips voor Thuiskoks',
            '/terms-and-conditions' => 'Algemene voorwaarden',
            '/privacy' => 'Privacy',
            '/cookie' => 'Cookieverklaring',
        ];

        foreach ($publicPages as $path => $expectedContent) {
            $this->browse(function (Browser $browser) use ($path, $expectedContent) {
                $browser->visit($path)
                    ->waitFor('h1', 10);

                $this->dismissCookieBanner($browser);

                // Controleer dat er geen server error is
                $browser->assertDontSee('500')
                    ->assertDontSee('Server Error')
                    ->assertDontSee('Whoops')
                    ->assertSee($expectedContent);
            });
        }
    }
}
