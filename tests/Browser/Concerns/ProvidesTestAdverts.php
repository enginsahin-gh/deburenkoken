<?php

namespace Tests\Browser\Concerns;

use Carbon\Carbon;
use Database\Seeders\DuskTestSeeder;
use Facebook\WebDriver\Exception\UnknownErrorException;
use Laravel\Dusk\Browser;

/**
 * ProvidesTestAdverts - Generieke helper trait voor tests die advertenties nodig hebben
 *
 * Deze trait biedt methodes om:
 * - Bestaande advertenties te vinden via de publieke zoekpagina
 * - Nieuwe advertenties aan te maken via het thuiskok dashboard
 * - Automatisch een advertentie te vinden of aan te maken
 *
 * Gebruik deze trait in Dusk tests die afhankelijk zijn van het bestaan van een advertentie.
 * De trait werkt zowel op lokale als GitHub Actions CI omgevingen.
 *
 * BELANGRIJK: Deze tests draaien ALTIJD tegen de lokale omgeving (APP_URL),
 * NOOIT tegen externe servers zoals test.deburenkoken.nl in CI.
 *
 * @example
 * class MyTest extends DuskTestCase
 * {
 *     use ProvidesTestAdverts;
 *
 *     public function test_something(): void
 *     {
 *         $this->browse(function (Browser $browser) {
 *             $advertUrl = $this->findOrCreateAdvert($browser);
 *             // ... test logic
 *         });
 *     }
 * }
 */
trait ProvidesTestAdverts
{
    // Zoeklocatie voor het vinden van advertenties
    protected string $searchLocation = 'Sliedrecht';

    // Sliedrecht coordinates voor directe zoek-URL
    protected float $searchLatitude = 51.8248681;

    protected float $searchLongitude = 4.773162399999999;

    // Test kok credentials (uit DuskTestSeeder)
    protected string $cookEmail = DuskTestSeeder::TEST_EMAIL;

    protected string $cookPassword = DuskTestSeeder::TEST_PASSWORD;

    // Retry settings voor CI stabiliteit
    protected int $maxRetries = 3;

    protected int $retryDelayMs = 2000;

    /**
     * Bezoek een URL met retry logic voor CI stabiliteit.
     * In GitHub Actions kan de development server soms tijdelijk niet beschikbaar zijn.
     */
    protected function visitWithRetry(Browser $browser, string $url, ?int $maxRetries = null): Browser
    {
        $maxRetries = $maxRetries ?? $this->maxRetries;
        $lastException = null;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $browser->visit($url);

                return $browser;
            } catch (UnknownErrorException $e) {
                $lastException = $e;

                // Check of het een connection refused error is
                if (str_contains($e->getMessage(), 'ERR_CONNECTION_REFUSED')) {
                    fwrite(STDERR, "\n[ProvidesTestAdverts] Connection refused, attempt {$attempt}/{$maxRetries}, waiting...\n");

                    if ($attempt < $maxRetries) {
                        usleep($this->retryDelayMs * 1000);

                        continue;
                    }
                }

                throw $e;
            }
        }

        throw $lastException;
    }

    /**
     * Vind de eerste beschikbare advertentie via de zoekpagina
     * Retourneert de URL van de advertentie details pagina, of null als geen gevonden
     */
    protected function findFirstAvailableAdvert(Browser $browser): ?string
    {
        try {
            // Gebruik de juiste parameter namen die SearchController verwacht
            $searchUrl = '/search?plaats='.urlencode($this->searchLocation).
                '&latitude='.$this->searchLatitude.
                '&longitude='.$this->searchLongitude.
                '&distance=100'.
                '&searching=1';

            $this->visitWithRetry($browser, $searchUrl);
            $browser->pause(3000);

            $browser->dismissOverlays();
            $browser->screenshot('search-results-page');

            // De advertentie kaarten gebruiken onclick='checkDish("UUID", "1")' in plaats van href links
            // Zoek naar elementen met onclick die checkDish aanroepen
            $advertElements = $browser->elements('[onclick*="checkDish"]');

            if (count($advertElements) === 0) {
                // Fallback: probeer ook de orderDish onclick te vinden
                $advertElements = $browser->elements('[onclick*="orderDish"]');
            }

            if (count($advertElements) === 0) {
                // Log wat er op de pagina staat voor debugging
                fwrite(STDERR, "\n[ProvidesTestAdverts] Geen advertenties gevonden op: {$searchUrl}\n");
                $browser->screenshot('no-search-results');

                return null;
            }

            // Extract de UUID uit de onclick attribuut
            // Format: checkDish("UUID", "1")
            $onclick = $advertElements[0]->getAttribute('onclick');
            if (preg_match('/checkDish\(["\']([a-f0-9-]+)["\']/', $onclick, $matches)) {
                $uuid = $matches[1];
                fwrite(STDERR, "\n[ProvidesTestAdverts] Gevonden advertentie UUID: {$uuid}\n");

                return '/details/'.$uuid;
            }

            // Als geen UUID gevonden, klik op het element en haal de URL op
            $advertElements[0]->click();
            $browser->pause(2000);
            $currentUrl = $browser->driver->getCurrentURL();

            if (str_contains($currentUrl, '/details/')) {
                return parse_url($currentUrl, PHP_URL_PATH);
            }

            return null;
        } catch (\Exception $e) {
            fwrite(STDERR, "\n[ProvidesTestAdverts] Error finding advert: ".$e->getMessage()."\n");
            $browser->screenshot('find-advert-error');

            return null;
        }
    }

    /**
     * Maak een nieuwe advertentie aan via het thuiskok dashboard
     * Logt in als test kok, maakt een advertentie aan en publiceert deze
     *
     * VEREIST: De test kok moet bestaan. Run DuskTestSeeder:
     * php artisan db:seed --class=DuskTestSeeder
     */
    protected function createTestAdvert(Browser $browser): bool
    {
        try {
            // Login als test kok (met retry voor CI stabiliteit)
            $this->visitWithRetry($browser, '/login');
            $browser->waitFor('input[name="email"]', 10);

            $browser->dismissOverlays();

            $browser->type('input[name="email"]', $this->cookEmail)
                ->type('input[name="password"]', $this->cookPassword)
                ->press('Inloggen')
                ->pause(3000);

            $browser->dismissOverlays();
            $browser->screenshot('login-attempt');

            // Check of we ingelogd zijn
            $currentUrl = $browser->driver->getCurrentURL();
            if (str_contains($currentUrl, 'login')) {
                // Login mislukt - test kok bestaat waarschijnlijk niet op de server
                $browser->screenshot('login-failed');

                // Log een duidelijke melding
                fwrite(STDERR, "\n[ProvidesTestAdverts] Login mislukt voor ".$this->cookEmail."\n");
                fwrite(STDERR, "[ProvidesTestAdverts] Run 'php artisan db:seed --class=DuskTestSeeder' op de test server\n");

                return false;
            }

            $browser->screenshot('logged-in-as-cook');

            // Ga naar advertenties aanmaken pagina (met retry)
            $this->visitWithRetry($browser, '/dashboard/adverts/create');
            $browser->pause(2000);

            $browser->dismissOverlays();
            $browser->screenshot('advert-create-page');

            // Check of er gerechten beschikbaar zijn
            $dishOptions = $browser->elements('select#dishes option');
            if (count($dishOptions) <= 1) {
                // Geen gerechten beschikbaar
                $browser->screenshot('no-dishes-available');
                fwrite(STDERR, "\n[ProvidesTestAdverts] Geen gerechten beschikbaar voor test kok\n");

                return false;
            }

            // Selecteer het eerste gerecht (skip de lege optie)
            $firstDishValue = null;
            foreach ($dishOptions as $option) {
                $value = $option->getAttribute('value');
                if (! empty($value)) {
                    $firstDishValue = $value;
                    break;
                }
            }

            if ($firstDishValue === null) {
                $browser->screenshot('no-valid-dish');

                return false;
            }

            $browser->select('select#dishes', $firstDishValue)
                ->pause(1000);

            // Bereken datum voor morgen
            $tomorrow = Carbon::tomorrow()->format('Y-m-d');

            // Vul het advertentie formulier in via JavaScript (betrouwbaarder)
            $browser->script("
                document.getElementById('available').value = '5';
                document.getElementById('order_date').value = '{$tomorrow}';
                document.getElementById('order_time').value = '14:00';
                document.getElementById('pickup_date').value = '{$tomorrow}';
                document.getElementById('pickup_from').value = '17:00';
                document.getElementById('pickup_to').value = '19:00';
            ");

            $browser->pause(500);
            $browser->screenshot('advert-form-filled');

            // Scroll naar en klik op de publiceer button
            $browser->script("
                const concept = document.getElementById('concept');
                if (concept) concept.value = 'true';
            ");

            // Submit de form
            $browser->script("document.getElementById('form').submit();");

            $browser->pause(5000);
            $browser->screenshot('advert-created');

            // Check of de advertentie succesvol is aangemaakt
            $afterUrl = $browser->driver->getCurrentURL();
            $success = str_contains($afterUrl, 'dashboard/adverts') && ! str_contains($afterUrl, 'create');

            // Logout (met retry)
            $this->visitWithRetry($browser, '/logout');
            $browser->pause(1000);

            return $success;

        } catch (\Exception $e) {
            $browser->screenshot('create-advert-error');
            fwrite(STDERR, "\n[ProvidesTestAdverts] Exception: ".$e->getMessage()."\n");

            return false;
        }
    }

    /**
     * Zoek of maak een advertentie aan
     * Probeert eerst een bestaande advertentie te vinden.
     * Als er geen is, maakt een nieuwe aan en zoekt opnieuw.
     *
     * @return string|null De URL van de advertentie details pagina, of null als niet mogelijk
     */
    protected function findOrCreateAdvert(Browser $browser): ?string
    {
        try {
            // Eerst proberen een bestaande advertentie te vinden
            $advertUrl = $this->findFirstAvailableAdvert($browser);

            if ($advertUrl !== null) {
                return $advertUrl;
            }

            // Geen advertenties gevonden, probeer er een aan te maken
            $browser->screenshot('no-adverts-found-trying-to-create');

            $created = $this->createTestAdvert($browser);

            if (! $created) {
                return null;
            }

            // Zoek opnieuw naar advertenties
            return $this->findFirstAvailableAdvert($browser);
        } catch (\Exception $e) {
            fwrite(STDERR, "\n[ProvidesTestAdverts] Error in findOrCreateAdvert: ".$e->getMessage()."\n");
            $browser->screenshot('find-or-create-advert-error');

            return null;
        }
    }

    /**
     * Helper: Controleer of de zoeklocatie kan worden geconfigureerd
     */
    protected function setSearchLocation(string $location, float $lat, float $lng): self
    {
        $this->searchLocation = $location;
        $this->searchLatitude = $lat;
        $this->searchLongitude = $lng;

        return $this;
    }

    /**
     * Helper: Configureer custom test kok credentials
     */
    protected function setTestCookCredentials(string $email, string $password): self
    {
        $this->cookEmail = $email;
        $this->cookPassword = $password;

        return $this;
    }
}
