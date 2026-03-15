<?php

namespace Tests\Browser;

use App\Support\SensitiveDataMasker;
use Database\Seeders\DuskTestSeeder;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Browser test voor Admin Controle Bankrekening functionaliteit.
 * Verifieert dat het banking overzicht Nederlandse kolomheaders toont,
 * IBANs gemaskeerd zijn, en de reveal-functionaliteit werkt.
 *
 * Screenshot locatie: tests/Browser/screenshots/
 */
class AdminBankingControlDuskTest extends DuskTestCase
{
    /**
     * Test dat het banking overzicht Nederlandse kolommen toont met gemaskeerde IBAN.
     */
    public function test_admin_banking_overview_shows_dutch_columns_and_masked_iban(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAsAdmin(
                DuskTestSeeder::ADMIN_EMAIL,
                DuskTestSeeder::ADMIN_PASSWORD
            );

            $browser->visit('/dashboard/admin/banking')
                ->pause(1500)
                ->dismissOverlays();

            $browser->assertSee('Controle Bankrekening');
            $browser->assertSee('Gebruikersnaam');
            $browser->assertSee('E-mail');
            $browser->assertSee('Voornaam');
            $browser->assertSee('Achternaam');
            $browser->assertSee('Geboortedatum');
            $browser->assertSee('IBAN');
            $browser->assertSee('Naam IBAN');
            $browser->assertSee('Zoeken op gebruikersnaam');
            $browser->assertSee('D. Testkok');

            // IBAN moet gemaskeerd zijn - volledige IBAN mag niet zichtbaar zijn
            $browser->assertDontSee('NL91ABNA0417164300');
            $browser->assertSee(SensitiveDataMasker::mask('NL91ABNA0417164300'));

            $browser->screenshot('BL-247/banking-masked-iban');
        });
    }

    /**
     * Test dat de zoekfunctie op gebruikersnaam filtert met gemaskeerde IBAN.
     */
    public function test_admin_banking_search_filters_by_username(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAsAdmin(
                DuskTestSeeder::ADMIN_EMAIL,
                DuskTestSeeder::ADMIN_PASSWORD
            );

            $browser->visit('/dashboard/admin/banking')
                ->pause(1000)
                ->dismissOverlays();

            $browser->type('input[name="query"]', DuskTestSeeder::TEST_USERNAME)
                ->press('Zoeken')
                ->pause(1500);

            $browser->assertSee(DuskTestSeeder::TEST_USERNAME);
            $browser->assertDontSee('NL91ABNA0417164300');
            $browser->assertSee(SensitiveDataMasker::mask('NL91ABNA0417164300'));
            $browser->assertSee('D. Testkok');

            $browser->screenshot('BL-247/banking-search-masked');
        });
    }

    /**
     * Test dat de reveal-knop zichtbaar is bij gemaskeerde IBANs.
     */
    public function test_admin_banking_shows_reveal_button(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAsAdmin(
                DuskTestSeeder::ADMIN_EMAIL,
                DuskTestSeeder::ADMIN_PASSWORD
            );

            $browser->visit('/dashboard/admin/banking')
                ->pause(1500)
                ->dismissOverlays();

            // Reveal button moet aanwezig zijn
            $browser->assertPresent('.reveal-btn');

            $browser->screenshot('BL-247/banking-reveal-button');
        });
    }

    /**
     * Test dat klikken op reveal-knop de bevestigingsmodal toont.
     */
    public function test_admin_banking_reveal_shows_confirmation_modal(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAsAdmin(
                DuskTestSeeder::ADMIN_EMAIL,
                DuskTestSeeder::ADMIN_PASSWORD
            );

            $browser->visit('/dashboard/admin/banking')
                ->pause(1500)
                ->dismissOverlays();

            // Verberg de PHPDebugbar zodat deze de klik niet blokkeert
            $browser->script('document.querySelectorAll(".phpdebugbar").forEach(function(el) { el.style.display = "none"; })');
            $browser->pause(300);

            // Klik op de eerste reveal-knop
            $browser->click('.reveal-btn')
                ->pause(500);

            // Bevestigingsmodal moet zichtbaar zijn
            $browser->assertSee('Gevoelige gegevens');
            $browser->assertSee('Weet je zeker dat je deze gegevens wilt inzien?');
            $browser->assertSee('Bevestigen');
            $browser->assertSee('Annuleren');

            $browser->screenshot('BL-247/banking-confirm-modal');
        });
    }

    /**
     * Test dat bevestigen in de modal het volledige IBAN toont.
     */
    public function test_admin_banking_reveal_shows_full_iban_after_confirm(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAsAdmin(
                DuskTestSeeder::ADMIN_EMAIL,
                DuskTestSeeder::ADMIN_PASSWORD
            );

            $browser->visit('/dashboard/admin/banking')
                ->pause(1500)
                ->dismissOverlays();

            // Verberg de PHPDebugbar zodat deze de klik niet blokkeert
            $browser->script('document.querySelectorAll(".phpdebugbar").forEach(function(el) { el.style.display = "none"; })');
            $browser->pause(300);

            // Klik op de eerste reveal-knop
            $browser->click('.reveal-btn')
                ->pause(500);

            // Bevestig in de modal
            $browser->press('Bevestigen')
                ->pause(2000);

            // Nu moet het volledige IBAN zichtbaar zijn
            $browser->assertSee('NL91ABNA0417164300');

            $browser->screenshot('BL-247/banking-revealed-iban');
        });
    }
}
