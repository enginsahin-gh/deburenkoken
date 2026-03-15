<?php

namespace Tests\Browser;

use Database\Seeders\DuskTestSeeder;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

/**
 * Test class voor Thuiskok Dashboard pagina's (vereist login)
 *
 * Test de volgende pagina's:
 * - Dashboard home (/dashboard/adverts/active)
 * - Actieve Advertenties (/dashboard/adverts/active)
 * - Verlopen Advertenties (/dashboard/adverts/past)
 * - Gerechten (/dashboard/dishes)
 * - Bestellingen (/dashboard/orders)
 * - Instellingen - Profiel (/dashboard/settings)
 * - Instellingen - Gegevens (/dashboard/settings/details)
 * - Instellingen - Meldingen (/dashboard/settings/reports)
 * - Instellingen - Privacy (/dashboard/settings/privacy)
 * - Portemonnee - Saldo (/dashboard/wallet/home)
 * - Portemonnee - IBAN (/dashboard/wallet/iban)
 *
 * Controleert:
 * - Pagina's laden correct na login
 * - Dashboard navigatie werkt
 * - Essentiële content aanwezig
 * - Sidebar menu structuur correct
 */
class CookDashboardTest extends DuskTestCase
{
    /**
     * Helper om als test kok in te loggen
     */
    protected function loginAsTestCook(Browser $browser): void
    {
        $browser->loginAsCook(DuskTestSeeder::TEST_EMAIL, DuskTestSeeder::TEST_PASSWORD);
    }

    /**
     * Helper om intro.js overlay te sluiten
     */
    protected function dismissIntroOverlay(Browser $browser): void
    {
        $browser->dismissOverlays();
    }

    /**
     * Helper om cookie banner te sluiten
     */
    protected function dismissCookieBanner(Browser $browser): void
    {
        $browser->dismissOverlays();
    }

    /**
     * Test dat inloggen succesvol is en redirect naar dashboard
     */
    #[Test]
    public function cook_can_login_and_access_dashboard()
    {
        $this->browse(function (Browser $browser) {
            // Use the loginAsCook macro which handles all the login logic
            $this->loginAsTestCook($browser);

            // Controleer dat we op dashboard zijn
            $browser->assertPathBeginsWith('/dashboard')
                ->assertSee('Advertenties')
                ->assertSee('Gerechten')
                ->assertSee('Bestellingen')
                ->assertSee('Instellingen')
                ->assertSee('Portemonnee')
                ->assertSee('Uitloggen');
        });
    }

    /**
     * Test Actieve Advertenties pagina
     */
    #[Test]
    public function active_adverts_page_displays_correctly()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTestCook($browser);

            $browser->visit('/dashboard/adverts/active')
                ->waitFor('h1', 10);

            $this->dismissIntroOverlay($browser);

            // Titel en tabel headers
            $browser->assertSee('Actieve Advertenties')
                ->assertSee('Advertentienummer')
                ->assertSee('Naam gerecht')
                ->assertSee('Porties verkocht')
                ->assertSee('Status');

            // Test advertentie zichtbaar
            $browser->assertSee('Dusk Test Pasta');

            // Link naar nieuwe advertentie
            $browser->assertSeeLink('Nieuwe advertentie');
        });
    }

    /**
     * Test Verlopen Advertenties pagina
     */
    #[Test]
    public function past_adverts_page_displays_correctly()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTestCook($browser);

            $browser->visit('/dashboard/adverts/past')
                ->waitFor('h1', 10);

            $this->dismissIntroOverlay($browser);

            $browser->assertSee('Verlopen Advertenties')
                ->assertSee('Advertentienummer');
        });
    }

    /**
     * Test Gerechten pagina
     */
    #[Test]
    public function dishes_page_displays_correctly()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTestCook($browser);

            $browser->visit('/dashboard/dishes')
                ->waitFor('h1', 10);

            $this->dismissIntroOverlay($browser);

            $browser->assertSee('Gerechten')
                ->assertSee('Naam gerecht')
                ->assertSee('Dusk Test Pasta')
                ->assertSeeLink('Nieuw gerecht');
        });
    }

    /**
     * Test Bestellingen pagina
     */
    #[Test]
    public function orders_page_displays_correctly()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTestCook($browser);

            $browser->visit('/dashboard/orders')
                ->waitFor('h1', 10);

            $this->dismissIntroOverlay($browser);

            // Tabel headers
            $browser->assertSee('Bestellingen')
                ->assertSee('Bestelnummer')
                ->assertSee('Klantnaam')
                ->assertSee('Gerechtnaam')
                ->assertSee('Aantal porties')
                ->assertSee('Status');
        });
    }

    /**
     * Test Instellingen Profiel pagina
     */
    #[Test]
    public function settings_profile_page_displays_correctly()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTestCook($browser);

            $browser->visit('/dashboard/settings')
                ->waitFor('h1', 10);

            $this->dismissIntroOverlay($browser);

            $browser->assertSee('Profiel')
                ->assertSee('Thuiskok naam')
                ->assertSee('Omschrijving Thuiskok')
                ->assertSee('Opslaan')
                ->assertDontSee('Wijzigen')
                ->assertSeeLink('Account verwijderen');
        });
    }

    /**
     * Test Instellingen Gegevens pagina
     */
    #[Test]
    public function settings_details_page_displays_correctly()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTestCook($browser);

            $browser->visit('/dashboard/settings/details')
                ->waitFor('h1', 10);

            $this->dismissIntroOverlay($browser);

            $browser->assertSee('Gegevens')
                ->assertSee('Voornaam')
                ->assertSee('Achternaam')
                ->assertSee('Geboortedatum')
                ->assertSee('Woonplaats')
                ->assertSee('Postcode')
                ->assertSee('Telefoonnummer')
                ->assertSee('E-mailadres');
        });
    }

    /**
     * Test Instellingen Meldingen pagina
     */
    #[Test]
    public function settings_reports_page_displays_correctly()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTestCook($browser);

            $browser->visit('/dashboard/settings/reports')
                ->waitFor('h1', 10);

            $this->dismissIntroOverlay($browser);

            $browser->assertSee('Meldingen');
        });
    }

    /**
     * Test Instellingen Privacy pagina
     */
    #[Test]
    public function settings_privacy_page_displays_correctly()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTestCook($browser);

            $browser->visit('/dashboard/settings/privacy')
                ->waitFor('h1', 10);

            $this->dismissIntroOverlay($browser);

            $browser->assertSee('Privacy');
        });
    }

    /**
     * Test Portemonnee Saldo pagina
     */
    #[Test]
    public function wallet_home_page_displays_correctly()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTestCook($browser);

            $browser->visit('/dashboard/wallet/home')
                ->waitFor('h1', 10);

            $this->dismissIntroOverlay($browser);

            $browser->assertSee('Portemonnee')
                ->assertSee('Saldo in behandeling')
                ->assertSee('Beschikbaar saldo')
                ->assertSee('Uitbetalen')
                ->assertSee('Uitbetalingen');
        });
    }

    /**
     * Test Portemonnee IBAN pagina
     */
    #[Test]
    public function wallet_iban_page_displays_correctly()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTestCook($browser);

            $browser->visit('/dashboard/wallet/iban')
                ->waitFor('h1', 10);

            $this->dismissIntroOverlay($browser);

            $browser->assertSee('IBAN')
                ->assertSee('Bankrekening')
                ->assertSeeLink('IBAN aanpassen');
        });
    }

    /**
     * Test sidebar navigatie werkt
     */
    #[Test]
    public function sidebar_navigation_works()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTestCook($browser);

            $browser->visit('/dashboard/adverts/active')
                ->waitFor('h1', 10);

            $this->dismissIntroOverlay($browser);

            // Navigeer via sidebar
            $browser->clickLink('Gerechten')
                ->waitFor('h1', 10)
                ->assertPathIs('/dashboard/dishes')
                ->assertSee('Gerechten');

            $browser->clickLink('Bestellingen')
                ->waitFor('h1', 10)
                ->assertPathIs('/dashboard/orders')
                ->assertSee('Bestellingen');
        });
    }

    /**
     * Test dat uitloggen werkt
     */
    #[Test]
    public function cook_can_logout()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTestCook($browser);

            $browser->visit('/dashboard/adverts/active')
                ->waitFor('h1', 10);

            $this->dismissIntroOverlay($browser);

            $browser->clickLink('Uitloggen')
                ->pause(1000)
                ->assertPathIs('/');
        });
    }

    /**
     * Test dat dashboard niet toegankelijk is zonder login
     */
    #[Test]
    public function dashboard_requires_authentication()
    {
        $this->browse(function (Browser $browser) {
            // Verwijder cookies om uitgelogde staat te simuleren
            $browser->driver->manage()->deleteAllCookies();

            $browser->visit('/dashboard/adverts/active')
                ->waitFor('h1', 10);

            // Moet redirect naar login of toegang weigeren
            $browser->assertPathIsNot('/dashboard/adverts/active');
        });
    }

    /**
     * Test dat alle dashboard pagina's laden zonder errors
     */
    #[Test]
    public function all_dashboard_pages_load_without_errors()
    {
        $dashboardPages = [
            '/dashboard/adverts/active' => 'Actieve Advertenties',
            '/dashboard/adverts/past' => 'Verlopen Advertenties',
            '/dashboard/dishes' => 'Gerechten',
            '/dashboard/orders' => 'Bestellingen',
            '/dashboard/settings' => 'Profiel',
            '/dashboard/settings/details' => 'Gegevens',
            '/dashboard/settings/reports' => 'Meldingen',
            '/dashboard/settings/privacy' => 'Privacy',
            '/dashboard/wallet/home' => 'Portemonnee',
            '/dashboard/wallet/iban' => 'IBAN',
        ];

        $this->browse(function (Browser $browser) use ($dashboardPages) {
            $this->loginAsTestCook($browser);

            foreach ($dashboardPages as $path => $expectedContent) {
                $browser->visit($path)
                    ->waitFor('h1', 10);

                $this->dismissIntroOverlay($browser);

                // Controleer dat er geen server error is
                $browser->assertDontSee('500')
                    ->assertDontSee('Server Error')
                    ->assertDontSee('Whoops')
                    ->assertSee($expectedContent);
            }
        });
    }

    /**
     * Test header navigatie in dashboard
     */
    #[Test]
    public function dashboard_header_navigation_works()
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTestCook($browser);

            $browser->visit('/dashboard/adverts/active')
                ->waitFor('h1', 10);

            $this->dismissIntroOverlay($browser);

            // Header links
            $browser->assertSeeLink('Plaats advertentie')
                ->assertSeeLink('Mijn omgeving');

            // Test navigatie naar plaats advertentie
            $browser->clickLink('Plaats advertentie')
                ->waitFor('h1', 10)
                ->assertPathIs('/dashboard/adverts/create');
        });
    }
}
