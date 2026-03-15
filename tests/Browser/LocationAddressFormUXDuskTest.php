<?php

namespace Tests\Browser;

use Database\Seeders\DuskTestSeeder;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

/**
 * Dusk screenshots voor de verbeterde adresinvul-UX (BL-250).
 *
 * Maakt screenshots van de locatiepagina bij het wijzigen van het adres
 * via de instellingen, zodat de verbeteringen visueel gedocumenteerd zijn.
 */
class LocationAddressFormUXDuskTest extends DuskTestCase
{
    private function hideDebugbar(Browser $browser): void
    {
        $browser->script("
            var debugbar = document.getElementById('phpdebugbar');
            if (debugbar) { debugbar.style.display = 'none'; }
            var debugbarMini = document.getElementById('phpdebugbar-header');
            if (debugbarMini) { debugbarMini.style.display = 'none'; }
        ");
    }

    #[Test]
    public function location_form_shows_prominent_search_and_instruction_box(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAsCook(DuskTestSeeder::TEST_EMAIL, DuskTestSeeder::TEST_PASSWORD);

            // Navigeer direct naar de locatiepagina via de instellingen-route
            $browser->visit(route('dashboard.settings.update.location'))
                ->waitFor('#autocomplete', 10)
                ->pause(500);

            $this->hideDebugbar($browser);

            $browser->screenshot('BL-250-location-form-prominent-search');

            $browser->assertPresent('.address-search-input')
                ->assertPresent('.address-instruction-box')
                ->assertPresent('.readonly-field-wrapper')
                ->assertPresent('.readonly-field-tooltip');
        });
    }

    #[Test]
    public function location_form_shows_tooltip_on_readonly_field_click(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAsCook(DuskTestSeeder::TEST_EMAIL, DuskTestSeeder::TEST_PASSWORD);

            $browser->visit(route('dashboard.settings.update.location'))
                ->waitFor('#autocomplete', 10)
                ->pause(800);

            $browser->dismissOverlays();

            $this->hideDebugbar($browser);

            // Klik op het postcode-veld om de tooltip te triggeren
            $browser->click('#postal')
                ->pause(400);

            $browser->screenshot('BL-250-readonly-tooltip-visible');

            $browser->assertVisible('.readonly-field-tooltip.visible');
        });
    }
}
