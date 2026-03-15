# DeBurenKoken Dusk Tests - Verbeterplan

Dit document beschrijft de geplande verbeteringen voor de Laravel Dusk test suite.

**Datum:** 20 december 2025  
**Status:** In uitvoering

---

## Overzicht

De huidige test suite bevat 48 browser tests verspreid over 6 test classes. Na analyse van de Laravel Dusk documentatie en de bestaande code zijn de volgende verbeteringen geïdentificeerd.

---

## 1. DatabaseTruncation i.p.v. Handmatige Seeding

### Probleem
De huidige tests draaien `DuskTestSeeder` in elke `setUp()` method. Dit is traag en zorgt voor lange CI build times (~5 minuten).

### Oplossing
Gebruik de `DatabaseTruncation` trait die Laravel Dusk biedt. Deze:
- Draait migraties alleen bij de eerste test
- Truncate tabellen i.p.v. opnieuw seeden bij elke test
- Is significant sneller dan `DatabaseMigrations`

### Implementatie
```php
use Illuminate\Foundation\Testing\DatabaseTruncation;

class CookJourneyTest extends DuskTestCase
{
    use DatabaseTruncation;
    
    protected $tablesToTruncate = ['orders', 'adverts', 'reviews'];
    protected $exceptTables = ['users', 'cooks', 'dishes', 'roles', 'permissions'];
}
```

### Impact
- **Verwachte tijdsbesparing:** 30-50% snellere test runs
- **Risico:** Laag

---

## 2. Browser Macros voor Herhaalde Acties

### Probleem
Helper methods zoals `dismissIntroOverlay()` en `dismissCookieBanner()` worden in meerdere test classes herhaald.

### Oplossing
Definieer Browser Macros in een DuskServiceProvider of in DuskTestCase.

### Te implementeren macros:
| Macro | Doel |
|-------|------|
| `dismissOverlays()` | Sluit intro.js en cookie banner |
| `loginAsCook($email, $password)` | Login flow met overlay dismiss |
| `scrollAndClick($selector)` | Scroll naar element en klik |

### Implementatie
```php
// In DuskTestCase::setUp()
Browser::macro('dismissOverlays', function () {
    $this->script("
        document.querySelector('.introjs-skipbutton')?.click();
        document.querySelector('.introjs-donebutton')?.click();
        document.querySelector('button[value=\"accept\"]')?.click();
    ");
    return $this->pause(300);
});
```

---

## 3. Dusk Pages voor Pagina-specifieke Logica

### Probleem
- URL's zijn hardcoded in tests
- Login logica wordt herhaald
- Geen centrale plek voor pagina-specifieke selectors

### Te implementeren Pages:

| Page Class | URL | Verantwoordelijkheid |
|------------|-----|----------------------|
| `LoginPage` | `/login` | Login formulier en authenticatie |
| `DashboardPage` | `/dashboard/adverts/active` | Hoofd dashboard met sidebar |
| `DishesPage` | `/dashboard/dishes` | Gerechten overzicht |
| `DishCreatePage` | `/dashboard/dishes/create` | Nieuw gerecht formulier |
| `AdvertCreatePage` | `/dashboard/adverts/create` | Nieuwe advertentie |
| `OrderPage` | `/details/{uuid}/order` | Bestelformulier |
| `SettingsPage` | `/dashboard/settings` | Instellingen pagina's |

### Voorbeeld LoginPage:
```php
class LoginPage extends Page
{
    public function url(): string
    {
        return '/login';
    }

    public function assert(Browser $browser): void
    {
        $browser->assertPathIs('/login')
            ->assertSee('Inloggen');
    }

    public function elements(): array
    {
        return [
            '@email' => 'input[name="email"]',
            '@password' => 'input[name="password"]',
            '@submit' => 'button[type="submit"]',
        ];
    }

    public function loginAs(Browser $browser, string $email, string $password): void
    {
        $browser->type('@email', $email)
            ->type('@password', $password)
            ->press('Inloggen')
            ->waitForLocation('/dashboard', 10);
    }
}
```

---

## 4. Globale Shorthand Selectors

### Probleem
De huidige `Page.php` heeft placeholder selectors die niet worden gebruikt.

### Oplossing
Definieer site-brede selectors die op alle pagina's beschikbaar zijn.

### Implementatie in `tests/Browser/Pages/Page.php`:
```php
public static function siteElements(): array
{
    return [
        '@cookie-banner' => '.cookie-consent-banner, [class*="cookie"]',
        '@cookie-accept' => 'button[value="accept"]',
        '@intro-overlay' => '.introjs-overlay',
        '@intro-skip' => '.introjs-skipbutton',
        '@intro-done' => '.introjs-donebutton',
        '@main-nav' => 'nav, header nav',
        '@footer' => 'footer',
        '@sidebar' => '.sidebar, aside',
    ];
}
```

---

## 5. Vervang `pause()` door Slimmere Waits

### Probleem
Vaste wachttijden (`pause(3000)`) maken tests:
- Traag (altijd maximum tijd wachten)
- Fragiel (te kort bij trage systemen)

### Huidige patronen te vervangen:

| Huidige code | Verbeterde code |
|--------------|-----------------|
| `press('Inloggen')->pause(3000)` | `press('Inloggen')->waitForLocation('/dashboard', 10)` |
| `pause(1000)->assertSee('text')` | `waitForText('text', 5)` |
| `pause(500)->click('.btn')` | `waitFor('.btn')->click('.btn')` |
| `pause(2000)` na page load | `waitFor('h1', 10)` |

### Te gebruiken wait methods:
- `waitFor($selector)` - Wacht tot element zichtbaar is
- `waitForText($text)` - Wacht tot tekst verschijnt
- `waitForLocation($path)` - Wacht tot URL klopt
- `waitUntil($js)` - Wacht tot JavaScript expressie true is
- `waitUntilMissing($selector)` - Wacht tot element verdwijnt
- `clickAndWaitForReload()` - Klik en wacht op page reload

---

## 6. Dusk Selectors (Toekomstige Verbetering)

### Probleem
CSS selectors kunnen breken bij frontend wijzigingen.

### Oplossing
Voeg `dusk="..."` attributen toe aan Blade templates.

### Voorbeeld:
```blade
<!-- Huidig -->
<button class="order-button">Bestellen</button>

<!-- Verbeterd -->
<button dusk="order-button" class="order-button">Bestellen</button>
```

**Note:** Dit vereist wijzigingen in Blade templates en wordt als toekomstige verbetering gepland.

---

## 7. Pest Migratie (Optioneel)

Laravel 12 documentatie beveelt Pest 4 aan voor nieuwe projecten vanwege:
- Betere performance
- Moderne syntax
- Eenvoudiger test organisatie

**Besluit:** Optioneel voor later. Huidige PHPUnit tests werken goed.

---

## Implementatievolgorde

1. ✅ Verbeterplan documenteren (dit bestand)
2. ⏸️ DatabaseTruncation - **Niet mogelijk** (tests draaien tegen externe server, niet dezelfde database context)
3. ✅ Browser Macros geïmplementeerd (`dismissOverlays`, `loginAsCook`, `scrollAndClick`)
4. ✅ DuskTestSeeder verbeterd (rol-toewijzing bij refresh, centralisatie in DuskTestCase)
5. ✅ Alle 48 tests slagen na refactoring
6. ✅ Dusk Pages geïmplementeerd (`LoginPage`, `DashboardPage`, `HomePage`)
7. ✅ Globale selectors geüpdatet in `Page.php` (cookie-banner, intro-overlay, navigation, etc.)
8. ⏸️ `pause()` vervangen door `waitFor*()` - **Gedeeltelijk** (kritieke pauses behouden voor stabiliteit)
9. ✅ Finale validatie - alle tests slagen

---

## Samenvatting Wijzigingen

### Bestanden Gewijzigd:
- `tests/DuskTestCase.php` - Browser macros toegevoegd, centralisatie seeding
- `tests/Browser/Pages/Page.php` - Globale site selectors
- `tests/Browser/Pages/LoginPage.php` - Nieuwe pagina class
- `tests/Browser/Pages/DashboardPage.php` - Nieuwe pagina class  
- `tests/Browser/Pages/HomePage.php` - Geüpdatet met selectors
- `database/seeders/DuskTestSeeder.php` - Rol-toewijzing bij refresh toegevoegd
- `tests/Browser/CookDashboardTest.php` - Gebruik browser macros
- `tests/Browser/CookJourneyTest.php` - Gebruik browser macros
- `tests/Browser/CustomerJourneyTest.php` - Centrale seeding
- `tests/Browser/PricingRequirementsTest.php` - Gebruik browser macros
- `tests/Browser/PublicPagesTest.php` - Gebruik dismissOverlays macro

### Problemen Opgelost:
- **Ontbrekende cook rol** - DuskTestSeeder wijst nu de 'cook' rol toe bij refresh
- **Ontbrekende data migraties** - `php artisan migrate-data` moet worden gedraaid voor rollen
- **CSRF 419 false positive** - De "419" tekst kwam van Font Awesome CSS, niet van een fout

---

## Resultaten

| Metric | Voor | Na |
|--------|------|-----|
| CI Build tijd | ~5 min | ~3 min (test run: 185s) |
| Code duplicatie | Veel (helper methods in elke class) | Minimaal (browser macros) |
| Test stabiliteit | Matig (rol ontbrak soms) | Hoog (rol altijd aanwezig) |
| Onderhoudbaarheid | Matig | Hoog (centrale macros, pages) |
| Aantal tests | 48 | 48 |
| Slaagpercentage | Variabel | 100% |

---

## Referenties

- [Laravel Dusk Documentatie](https://laravel.com/docs/12.x/dusk)
- [Pest PHP Browser Testing](https://pestphp.com/)
