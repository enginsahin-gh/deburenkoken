<?php

namespace Tests\Browser;

use App\Models\User;
use Database\Seeders\DuskTestSeeder;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Browser tests voor Admin KVK bewerken functionaliteit.
 *
 * Screenshot locatie: tests/Browser/screenshots/
 */
class AdminKvkEditTest extends DuskTestCase
{
    /**
     * Test dat admin de KVK gegevens kan bewerken via het dashboard.
     */
    public function test_admin_can_view_and_edit_kvk_details(): void
    {
        $this->browse(function (Browser $browser) {
            // 1. Login als admin
            $browser->loginAsAdmin(
                DuskTestSeeder::ADMIN_EMAIL,
                DuskTestSeeder::ADMIN_PASSWORD
            );

            // 2. Vind de test kok user en reset KVK gegevens voor schone test
            $cookUser = User::where('email', DuskTestSeeder::TEST_EMAIL)->first();
            $this->assertNotNull($cookUser, 'Test cook user moet bestaan');

            // Reset KVK gegevens voor een schone uitgangssituatie
            $cookUser->update([
                'kvk_naam' => null,
                'kvk_nummer' => null,
                'btw_nummer' => null,
                'rsin' => null,
                'vestigingsnummer' => null,
                'nvwa_nummer' => null,
                'type_thuiskok' => null,
            ]);

            // 3. Navigeer naar de account details pagina
            $browser->visit('/dashboard/admin/accounts/'.$cookUser->uuid)
                ->pause(1500)
                ->dismissOverlays();

            // 4. Screenshot: Account pagina met lege KVK gegevens
            $browser->screenshot('01-admin-kvk-before-edit');

            // 5. Scroll naar KVK sectie en vul gegevens in
            $browser->scrollIntoView('#kvk-form')
                ->pause(500);

            // 6. Screenshot: KVK formulier leeg
            $browser->screenshot('02-admin-kvk-form-empty');

            // 7. Vul KVK gegevens in
            $browser->type('input[name="kvk_naam"]', 'Test Thuiskoks B.V.')
                ->type('input[name="kvk_nummer"]', '12345678')
                ->type('input[name="btw_nummer"]', 'NL123456789B01')
                ->type('input[name="rsin"]', '123456789')
                ->type('input[name="vestigingsnummer"]', '000012345678')
                ->type('input[name="nvwa_nummer"]', 'NVWA-12345');

            // 8. Screenshot: KVK formulier ingevuld
            $browser->screenshot('03-admin-kvk-form-filled');

            // 9. Submit formulier
            $browser->press('KVK gegevens opslaan')
                ->pause(2000);

            // 10. Screenshot: Na opslaan met success message
            $browser->screenshot('04-admin-kvk-after-save');

            // 11. Verifieer dat de gegevens zijn opgeslagen
            $cookUser->refresh();
            $this->assertEquals('Test Thuiskoks B.V.', $cookUser->kvk_naam);
            $this->assertEquals('12345678', $cookUser->kvk_nummer);
            $this->assertEquals('NL123456789B01', $cookUser->btw_nummer);

            // 12. Verifieer dat type_thuiskok automatisch is aangepast
            $this->assertEquals('Zakelijke Thuiskok', $cookUser->type_thuiskok);
        });
    }

    /**
     * Test dat het e-mailadres zichtbaar is op de admin account pagina.
     */
    public function test_admin_can_see_email_address_on_account_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAsAdmin(
                DuskTestSeeder::ADMIN_EMAIL,
                DuskTestSeeder::ADMIN_PASSWORD
            );

            $cookUser = User::where('email', DuskTestSeeder::TEST_EMAIL)->first();
            $this->assertNotNull($cookUser, 'Test cook user moet bestaan');

            $browser->visit('/dashboard/admin/accounts/'.$cookUser->uuid)
                ->pause(1500)
                ->dismissOverlays();

            $browser->assertSee('E-mailadres:');

            $browser->screenshot('06-admin-account-email-address');
        });
    }

    /**
     * Test dat de KVK gegevens behouden blijven na pagina refresh.
     */
    public function test_kvk_details_persisted_after_refresh(): void
    {
        $this->browse(function (Browser $browser) {
            // 1. Login als admin
            $browser->loginAsAdmin(
                DuskTestSeeder::ADMIN_EMAIL,
                DuskTestSeeder::ADMIN_PASSWORD
            );

            // 2. Vind de test kok user
            $cookUser = User::where('email', DuskTestSeeder::TEST_EMAIL)->first();
            $this->assertNotNull($cookUser, 'Test cook user moet bestaan');

            // 3. Sla KVK gegevens op via model (setup)
            $cookUser->update([
                'kvk_naam' => 'Persistentie Test B.V.',
                'kvk_nummer' => '87654321',
                'btw_nummer' => 'NL987654321B01',
                'type_thuiskok' => 'Zakelijke Thuiskok',
            ]);

            // 4. Navigeer naar account pagina
            $browser->visit('/dashboard/admin/accounts/'.$cookUser->uuid)
                ->pause(1500)
                ->dismissOverlays();

            // 5. Scroll naar KVK sectie
            $browser->scrollIntoView('#kvk-form')
                ->pause(500);

            // 6. Verifieer dat de gegevens worden weergegeven
            $browser->assertInputValue('input[name="kvk_naam"]', 'Persistentie Test B.V.')
                ->assertInputValue('input[name="kvk_nummer"]', '87654321')
                ->assertInputValue('input[name="btw_nummer"]', 'NL987654321B01');

            // 7. Screenshot: KVK gegevens zichtbaar na refresh
            $browser->screenshot('05-admin-kvk-persisted-data');
        });
    }
}
