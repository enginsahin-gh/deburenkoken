<?php

namespace Tests\Browser;

use Database\Seeders\DuskTestSeeder;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

/**
 * BL-281: Test dat de profielpagina direct bewerkbaar is
 * zonder dat de gebruiker eerst op "Wijzigen" hoeft te klikken.
 */
class ProfileSettingsDirectEditTest extends DuskTestCase
{
    protected function loginAsTestCook(Browser $browser): void
    {
        $browser->loginAsCook(DuskTestSeeder::TEST_EMAIL, DuskTestSeeder::TEST_PASSWORD);
    }

    protected function hideDebugBar(Browser $browser): void
    {
        $browser->script("
            const debugbar = document.getElementById('phpdebugbar');
            if (debugbar) debugbar.style.display = 'none';
            const debugbarMini = document.querySelector('.phpdebugbar-minimized');
            if (debugbarMini) debugbarMini.style.display = 'none';
        ");
    }

    #[Test]
    public function profile_page_is_directly_editable_without_edit_button(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTestCook($browser);

            $browser->visit('/dashboard/settings')
                ->pause(2000);

            $browser->dismissOverlays();

            $this->hideDebugBar($browser);

            $browser->assertSee('Profiel')
                ->assertSee('Opslaan')
                ->assertDontSee('Wijzigen');

            // Scroll naar boven voor overzichts-screenshot
            $browser->script('window.scrollTo({top: 0, behavior: "instant"})');
            $browser->pause(500);
            $browser->screenshot('BL-281/01-profiel-direct-bewerkbaar');
        });
    }

    #[Test]
    public function textarea_is_directly_editable(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTestCook($browser);

            $browser->visit('/dashboard/settings')
                ->pause(2000);

            $browser->dismissOverlays();

            $this->hideDebugBar($browser);

            $isDisabled = $browser->script("return document.getElementById('profile-description').disabled")[0];
            $this->assertFalse($isDisabled, 'Textarea should not be disabled');

            // Klik op de textarea en typ tekst om te laten zien dat het direct bewerkbaar is
            $browser->click('#profile-description')
                ->pause(300)
                ->keys('#profile-description', '{end}')
                ->type('#profile-description', ' [direct bewerkbaar ✓]');

            $browser->script("document.getElementById('profile-description').scrollIntoView({behavior: 'instant', block: 'center'})");
            $browser->pause(500);
            $browser->screenshot('BL-281/02-textarea-direct-bewerkbaar');
        });
    }

    #[Test]
    public function username_field_is_readonly_with_help_text(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTestCook($browser);

            $browser->visit('/dashboard/settings')
                ->pause(2000);

            $browser->dismissOverlays();

            $this->hideDebugBar($browser);

            $isReadonly = $browser->script("return document.getElementById('username').readOnly")[0];
            $this->assertTrue($isReadonly, 'Username field should be readonly');

            $browser->assertSee('De thuiskok naam kan niet worden gewijzigd');

            // Klik op het readonly username veld om de cursor te tonen
            $browser->click('#username');

            // Voeg tijdelijk een visuele highlight toe om het readonly veld duidelijk te maken
            $browser->script("
                const field = document.getElementById('username');
                field.style.outline = '3px solid #e74c3c';
                const help = document.getElementById('username-help');
                if (help) help.style.fontWeight = 'bold';
            ");

            $browser->script("document.getElementById('username').scrollIntoView({behavior: 'instant', block: 'center'})");
            $browser->pause(500);
            $browser->screenshot('BL-281/03-thuiskoknaam-readonly');
        });
    }

    #[Test]
    public function gallery_slots_and_close_buttons_are_visible(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAsTestCook($browser);

            $browser->visit('/dashboard/settings')
                ->pause(2000);

            $browser->dismissOverlays();

            $this->hideDebugBar($browser);

            $hasSlots = $browser->script("return document.querySelectorAll('.empty-slot .add, .profile-image-item .close').length > 0")[0];
            $this->assertTrue($hasSlots, 'Gallery slots or close buttons should be visible');

            // Scroll helemaal naar beneden om de galerij in beeld te krijgen
            $browser->script("
                const gallery = document.querySelector('#emptyImages');
                if (gallery) {
                    gallery.scrollIntoView({behavior: 'instant', block: 'center'});
                    gallery.style.outline = '3px solid #e67e22';
                }
            ");
            $browser->pause(500);
            $browser->screenshot('BL-281/04-gallery-direct-bewerkbaar');
        });
    }
}
