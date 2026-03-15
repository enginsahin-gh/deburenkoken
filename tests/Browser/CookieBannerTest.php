<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

/**
 * Test class voor cookie consent banner functionaliteit
 *
 * Test de cookiebalk die verschijnt voor nieuwe bezoekers:
 * - Banner verschijnt bij eerste bezoek (geen cookies)
 * - "Accepteer" knop accepteert alle cookies
 * - "Essentiële" knop accepteert alleen noodzakelijke cookies
 * - Banner verdwijnt na keuze maken
 * - Cookie voorkeuren worden onthouden
 */
class CookieBannerTest extends DuskTestCase
{
    /**
     * Test dat cookie banner verschijnt bij eerste bezoek
     * Gebruik deleteCookies() om een "eerste bezoek" te simuleren
     */
    #[Test]
    public function cookie_banner_appears_on_first_visit()
    {
        $this->browse(function (Browser $browser) {
            // Verwijder alle cookies om eerste bezoek te simuleren
            $browser->driver->manage()->deleteAllCookies();

            $browser->visit('/')
                ->waitFor('footer', 10);

            // Cookie banner content moet zichtbaar zijn
            $browser->assertSee('Wij gebruiken cookies')
                ->assertSee('Accepteer')
                ->assertSee('Essentiële');
        });
    }

    /**
     * Test dat "Accepteer" knop werkt en banner verbergt
     */
    #[Test]
    public function accept_cookies_button_hides_banner()
    {
        $this->browse(function (Browser $browser) {
            // Verwijder alle cookies om eerste bezoek te simuleren
            $browser->driver->manage()->deleteAllCookies();

            $browser->visit('/')
                ->waitFor('footer', 10)
                ->assertSee('Wij gebruiken cookies');

            // Klik Accepteer knop
            $browser->press('Accepteer')
                ->pause(1000);

            // Na accepteren, herlaad de pagina
            $browser->refresh()
                ->waitFor('footer', 10);

            // Banner zou niet meer moeten verschijnen
            $browser->assertDontSee('Wij gebruiken cookies');
        });
    }

    /**
     * Test dat "Essentiële" knop werkt en banner verbergt
     */
    #[Test]
    public function essential_cookies_button_hides_banner()
    {
        $this->browse(function (Browser $browser) {
            // Verwijder alle cookies om eerste bezoek te simuleren
            $browser->driver->manage()->deleteAllCookies();

            $browser->visit('/')
                ->waitFor('footer', 10)
                ->assertSee('Wij gebruiken cookies');

            // Klik Essentiële knop
            $browser->press('Essentiële')
                ->pause(1000);

            // Na accepteren, herlaad de pagina
            $browser->refresh()
                ->waitFor('footer', 10);

            // Banner zou niet meer moeten verschijnen
            $browser->assertDontSee('Wij gebruiken cookies');
        });
    }

    /**
     * Test dat cookie banner tekst correct is
     */
    #[Test]
    public function cookie_banner_displays_correct_text()
    {
        $this->browse(function (Browser $browser) {
            // Verwijder alle cookies
            $browser->driver->manage()->deleteAllCookies();

            $browser->visit('/')
                ->waitFor('footer', 10);

            // Controleer essentiële tekst (zonder quotes die problematisch kunnen zijn)
            $browser->assertSee('Wij gebruiken cookies en andere technologieën')
                ->assertSee('te klikken accepteer je het gebruik van alle cookies')
                ->assertSee('Wij plaatsen altijd noodzakelijke cookies');
        });
    }

    /**
     * Test dat cookie keuze behouden blijft na navigatie
     */
    #[Test]
    public function cookie_choice_persists_across_pages()
    {
        $this->browse(function (Browser $browser) {
            // Verwijder alle cookies
            $browser->driver->manage()->deleteAllCookies();

            // Bezoek pagina en accepteer cookies
            $browser->visit('/')
                ->waitFor('footer', 10)
                ->press('Accepteer')
                ->pause(1000);

            // Navigeer naar andere pagina's
            $browser->visit('/contact')
                ->waitFor('footer', 10)
                ->assertDontSee('Wij gebruiken cookies');

            $browser->visit('/facts/customer')
                ->waitFor('footer', 10)
                ->assertDontSee('Wij gebruiken cookies');

            $browser->visit('/terms-and-conditions')
                ->waitFor('footer', 10)
                ->assertDontSee('Wij gebruiken cookies');
        });
    }

    /**
     * Test dat cookie banner ook op andere pagina's verschijnt
     */
    #[Test]
    public function cookie_banner_appears_on_other_pages()
    {
        $pagesToTest = ['/contact', '/facts/customer', '/privacy', '/cookie', '/info'];

        foreach ($pagesToTest as $page) {
            $this->browse(function (Browser $browser) use ($page) {
                // Verwijder alle cookies
                $browser->driver->manage()->deleteAllCookies();

                $browser->visit($page)
                    ->waitFor('footer', 10)
                    ->assertSee('Wij gebruiken cookies')
                    ->assertSee('Accepteer')
                    ->assertSee('Essentiële');
            });
        }
    }
}
