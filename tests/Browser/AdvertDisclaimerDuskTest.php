<?php

namespace Tests\Browser;

use Database\Seeders\DuskTestSeeder;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

/**
 * Test class voor Advertentie Disclaimer tekst op create pagina
 *
 * Controleert:
 * - Nieuwe disclaimer tekst wordt correct weergegeven
 * - Links naar algemene voorwaarden en HACCP informatie werken
 * - Links openen in nieuw tabblad (target="_blank")
 */
class AdvertDisclaimerDuskTest extends DuskTestCase
{
    /**
     * Helper om als test kok in te loggen
     */
    protected function loginAsTestCook(Browser $browser): void
    {
        $browser->loginAsCook(DuskTestSeeder::TEST_EMAIL, DuskTestSeeder::TEST_PASSWORD);
    }

    /**
     * Test dat de disclaimer tekst correct wordt weergegeven op de advertentie create pagina
     */
    #[Test]
    public function advert_create_page_shows_new_disclaimer_text(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTestCook($browser);

            $browser->visit('/dashboard/adverts/create')
                ->waitFor('h1', 10);

            $browser->dismissOverlays();

            // Controleer hoofdtekst
            $browser->assertSee('Door deze advertentie te plaatsen verklaar ik dat:');

            // Controleer alle disclaimer punten
            $browser->assertSee('Ik akkoord ga met de')
                ->assertSee('algemene voorwaarden')
                ->assertSee('informatie in deze advertentie juist is');

            $browser->assertSee('De maaltijd is bereid volgens de geldende')
                ->assertSee('HACCP');

            $browser->assertSee('Ingrediënten en allergenen volledig en correct zijn vermeld');

            $browser->assertSee('Ik verantwoordelijk ben voor de voedselveiligheid van de aangeboden maaltijd');

            // Controleer dat de oude tekst NIET meer aanwezig is
            $browser->assertDontSee('Door het plaatsen van deze advertentie ga ik akkoord met de algemene voorwaarden en bevestig ik dat bovenstaande gegevens correct zijn.');

            // Maak screenshot
            $browser->screenshot('BL-239-advertentie-disclaimer-tekst');
        });
    }

    /**
     * Test dat de link naar algemene voorwaarden aanwezig is en target="_blank" heeft
     */
    #[Test]
    public function terms_conditions_link_opens_in_new_tab(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTestCook($browser);

            $browser->visit('/dashboard/adverts/create')
                ->waitFor('h1', 10);

            $browser->dismissOverlays();

            // Controleer dat de link aanwezig is met target="_blank"
            $browser->assertPresent('.advert-disclaimer a[href*="terms-and-conditions"][target="_blank"]');
        });
    }

    /**
     * Test dat de HACCP link aanwezig is en target="_blank" heeft
     */
    #[Test]
    public function haccp_link_opens_in_new_tab(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTestCook($browser);

            $browser->visit('/dashboard/adverts/create')
                ->waitFor('h1', 10);

            $browser->dismissOverlays();

            // Controleer dat de HACCP link aanwezig is met target="_blank"
            $browser->assertPresent('.advert-disclaimer a[href*="facts/cook"][target="_blank"]');
        });
    }

    /**
     * Test dat de disclaimer sectie visueel correct wordt weergegeven
     */
    #[Test]
    public function disclaimer_section_is_visually_present(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTestCook($browser);

            $browser->visit('/dashboard/adverts/create')
                ->waitFor('h1', 10);

            $browser->dismissOverlays();

            // Scroll naar beneden naar de disclaimer sectie
            $browser->scrollIntoView('.advert-disclaimer');

            // Korte pauze voor rendering
            $browser->pause(500);

            // Maak screenshot van de disclaimer sectie
            $browser->screenshot('BL-239-disclaimer-sectie-detail');
        });
    }

    /**
     * Test dat de disclaimer lijst semantisch correct is opgebouwd met ul/li elementen
     */
    #[Test]
    public function disclaimer_uses_semantic_list_markup(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTestCook($browser);

            $browser->visit('/dashboard/adverts/create')
                ->waitFor('h1', 10);

            $browser->dismissOverlays();

            // Controleer dat de lijst correct is opgebouwd
            $browser->assertPresent('.advert-disclaimer ul.advert-disclaimer-list')
                ->assertPresent('.advert-disclaimer-list li');

            // Controleer dat er 4 list items zijn
            $listItems = $browser->elements('.advert-disclaimer-list li');
            $this->assertCount(4, $listItems);

            // Maak screenshot van de verbeterde lijst styling
            $browser->scrollIntoView('.advert-disclaimer');
            $browser->pause(300);
            $browser->screenshot('BL-239-semantic-list-styling');
        });
    }
}
