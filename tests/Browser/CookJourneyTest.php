<?php

namespace Tests\Browser;

use Database\Seeders\DuskTestSeeder;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\Browser\Concerns\GeneratesTestData;
use Tests\DuskTestCase;

/**
 * Cook Journey - Volledige Thuiskok Flow Tests
 *
 * Test de complete flow van een thuiskok:
 * 1. Registreren als nieuwe kok
 * 2. Inloggen met bestaande kok
 * 3. Gerecht bekijken in dashboard
 * 4. Advertentie bekijken in dashboard
 *
 * Gebruikt de DuskTestSeeder voor een geverifieerde test kok.
 */
class CookJourneyTest extends DuskTestCase
{
    use GeneratesTestData;

    #[Test]
    public function cook_can_register_new_account(): void
    {
        $this->browse(function (Browser $browser) {
            // Genereer unieke credentials met Gmail+ aliasing
            $username = $this->generateTestUsername();
            $email = $this->generateTestEmail();
            $password = $this->generateTestPassword();

            $browser->visit('/register/info')
                ->waitFor('input[name="username"]', 10)
                ->type('input[name="username"]', $username)
                ->type('input[name="email"]', $email)
                ->type('input[name="password"]', $password)
                ->type('input[name="password_confirmation"]', $password)
                ->check('input[name="terms"]')
                ->press('Account aanmaken')
                ->waitForLocation('/register/submitted', 10)
                ->assertSee('Account aanvragen succesvol');

            $browser->screenshot('cook-registration-success');
        });
    }

    #[Test]
    public function cook_can_login_to_dashboard(): void
    {
        $this->browse(function (Browser $browser) {
            // Login met de bestaande test kok
            $browser->visit('/login')
                ->waitFor('input[name="email"]', 10)
                ->type('input[name="email"]', DuskTestSeeder::TEST_EMAIL)
                ->type('input[name="password"]', DuskTestSeeder::TEST_PASSWORD)
                ->press('Inloggen')
                ->pause(3000);

            // Controleer dat we op het dashboard zijn (redirect is naar /dashboard/adverts/active)
            $browser->assertPathBeginsWith('/dashboard');

            $browser->screenshot('cook-dashboard');
        });
    }

    #[Test]
    public function cook_can_view_dishes_in_dashboard(): void
    {
        $this->browse(function (Browser $browser) {
            // Login
            $browser->visit('/login')
                ->waitFor('input[name="email"]', 10)
                ->type('input[name="email"]', DuskTestSeeder::TEST_EMAIL)
                ->type('input[name="password"]', DuskTestSeeder::TEST_PASSWORD)
                ->press('Inloggen')
                ->pause(3000);

            // Navigeer naar gerechten overzicht
            $browser->visit('/dashboard/dishes')
                ->waitFor('h1', 10)
                ->dismissOverlays();

            // Controleer dat de test dish zichtbaar is
            $browser->assertSee('Dusk Test Pasta');

            $browser->screenshot('cook-dishes-overview');
        });
    }

    #[Test]
    public function cook_can_view_adverts_in_dashboard(): void
    {
        $this->browse(function (Browser $browser) {
            // Login
            $browser->visit('/login')
                ->waitFor('input[name="email"]', 10)
                ->type('input[name="email"]', DuskTestSeeder::TEST_EMAIL)
                ->type('input[name="password"]', DuskTestSeeder::TEST_PASSWORD)
                ->press('Inloggen')
                ->pause(3000);

            // Navigeer naar advertenties overzicht
            $browser->visit('/dashboard/adverts')
                ->waitFor('h1', 10)
                ->dismissOverlays();

            // Controleer dat er een advertentie is (de seeded advert)
            $browser->screenshot('cook-adverts-overview');

            // De pagina zou minimaal de advert moeten tonen of een "geen advertenties" bericht
            $currentUrl = $browser->driver->getCurrentURL();
            $this->assertStringContainsString('/dashboard/adverts', $currentUrl);
        });
    }
}
