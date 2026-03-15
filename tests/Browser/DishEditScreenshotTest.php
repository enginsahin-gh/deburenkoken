<?php

namespace Tests\Browser;

use Database\Seeders\DuskTestSeeder;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

/**
 * Maakt screenshots voor BL-283 (gerecht aanpassen).
 * Dit is een tijdelijke testklasse puur voor screenshot-doeleinden.
 */
class DishEditScreenshotTest extends DuskTestCase
{
    /** Dish UUID met actieve advertentie (geblokkeerd) */
    private const DISH_WITH_ACTIVE_ADVERT = '574706ab-bf90-4bf4-ad5b-049100d269be';

    /** Dish UUID zonder actieve advertentie (bewerkbaar) */
    private const DISH_WITHOUT_ADVERT = 'ea27bc1f-4d8a-4ed5-bf6e-8046a5ce4290';

    #[Test]
    public function screenshot_dish_show_with_active_advert_blocked(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAsCook(DuskTestSeeder::TEST_EMAIL, DuskTestSeeder::TEST_PASSWORD);
            $browser->dismissOverlays();

            $browser->visit('/dashboard/dishes/show/'.self::DISH_WITH_ACTIVE_ADVERT);
            $browser->dismissOverlays();

            // Verberg DebugBar
            $browser->script("document.getElementById('debugbar') && (document.getElementById('debugbar').style.display='none');");

            $browser->screenshot('BL-283/01-gerecht-actieve-advertentie-geblokkeerd');
        });
    }

    #[Test]
    public function screenshot_dish_show_without_advert_editable(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAsCook(DuskTestSeeder::TEST_EMAIL, DuskTestSeeder::TEST_PASSWORD);
            $browser->dismissOverlays();

            $browser->visit('/dashboard/dishes/show/'.self::DISH_WITHOUT_ADVERT);
            $browser->dismissOverlays();

            // Verberg DebugBar
            $browser->script("document.getElementById('debugbar') && (document.getElementById('debugbar').style.display='none');");

            $browser->screenshot('BL-283/02-gerecht-zonder-advertentie-aanpassen-knop');
        });
    }

    #[Test]
    public function screenshot_dish_edit_form(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAsCook(DuskTestSeeder::TEST_EMAIL, DuskTestSeeder::TEST_PASSWORD);
            $browser->dismissOverlays();

            $browser->visit('/dashboard/dishes/edit/'.self::DISH_WITHOUT_ADVERT);
            $browser->dismissOverlays();

            // Verberg DebugBar
            $browser->script("document.getElementById('debugbar') && (document.getElementById('debugbar').style.display='none');");

            $browser->screenshot('BL-283/03-gerecht-aanpassen-formulier');
        });
    }
}
