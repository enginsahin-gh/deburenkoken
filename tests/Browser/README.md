# Dusk Browser Tests voor DeBurenKoken.nl

## Overzicht

Deze directory bevat Laravel Dusk browser tests voor de business requirements van DeBurenKoken.nl.

### Ondersteunde omgevingen

| Omgeving | URL | Database | Truncation |
|----------|-----|----------|------------|
| **Lokaal** | `https://deburenkoken.test` | `dbk_t` | ✅ DatabaseTruncation |
| **GitHub Actions** | `http://127.0.0.1:8000` | `testing` | ✅ DatabaseTruncation |
| **Test (optioneel)** | `https://test.deburenkoken.nl` | Remote | ❌ Alleen seeding |

**BELANGRIJK**: GitHub Actions CI draait ALTIJD tegen een lokale development server (`http://127.0.0.1:8000`), NOOIT tegen externe servers!

## Requirements die getest worden

### Pricing Requirements (PricingRequirementsTest.php)
- **R16**: Maximum price per portion must be 25 euro
- **R17**: Minimum price per portion must be 0.50 euro

### Advert Requirements (AdvertPortionRequirementsTest.php)
- **R18**: Maximum amount of portions per advertisement must be 25

### Timing Requirements (TimingRequirementsTest.php)
- **R19**: Period between latest ordering moment and pick-up moment must be maximal 7 days
- **R20**: Period between current time and last ordering moment must be maximal 3 months
- **R22**: Pick up moment must be later than last ordering moment
- **R23**: Start pick up moment and last pick up moment must be on same day
- **R24**: Pick up till time must be later than pick up from time
- **R25**: Last ordering moment must be in the future
- **R122**: Time between start pickup and end pickup must be minimal 30 minutes

## Setup

### 1. Installeer Laravel Dusk

```bash
composer require --dev laravel/dusk
php artisan dusk:install
```

### 2. Configureer omgevingsvariabelen

#### Lokale ontwikkeling (aanbevolen)

Kopieer `.env.dusk.local.example` naar `.env.dusk.local`:

```bash
cp .env.dusk.local.example .env.dusk.local
```

De standaard configuratie is ingesteld voor lokale tests tegen `https://deburenkoken.test`.

#### Remote test omgeving (optioneel)

Om tests **handmatig** tegen de remote test server te draaien (niet voor CI):

```bash
cp .env.dusk.testing.example .env.dusk.testing
```

Voeg de HTTP Basic Auth credentials toe aan `.env.dusk.testing`:

```env
APP_URL=https://test.deburenkoken.nl
DUSK_HTTP_USERNAME=jouw_username
DUSK_HTTP_PASSWORD=jouw_wachtwoord
DUSK_SKIP_TRUNCATION=true  # Schakelt DatabaseTruncation uit
```

⚠️ **WAARSCHUWING**: 
- De `.env.dusk.*` files staan in `.gitignore` en worden NIET gecommit!
- GitHub Actions CI draait NOOIT tegen remote servers - het gebruikt een lokale server op `http://127.0.0.1:8000`

### 3. Download ChromeDriver

```bash
php artisan dusk:chrome-driver
```

### 4. DatabaseTruncation (lokaal)

De tests gebruiken Laravel's `DatabaseTruncation` trait voor snelle database resets:

- **Lokaal**: Tabellen worden getruncate tussen tests, daarna opnieuw geseeded
- **Remote** (`DUSK_SKIP_TRUNCATION=true`): Truncation wordt overgeslagen, tests draaien tegen bestaande data
- **Nooit `migrate:fresh`**: De database schema wordt nooit gewist, alleen de data

Tabellen die NIET worden getruncate (referentie data):
- `roles`, `permissions`, `role_has_permissions`, `model_has_roles`, `model_has_permissions`
- `migrations`, `data_migrations`, `website_status`, `mail_messages`

### 4. Begrijp de Test Flow

**Belangrijke wijziging**: Deze tests gebruiken GEEN pre-seeded accounts. Elke test:
1. Registreert automatisch een nieuwe cook met unieke credentials
2. Haalt verificatie URL uit de debug bar email
3. Verifieert het account
4. Is automatisch ingelogd en klaar om te testen

Alle test emails gebruiken Gmail+ aliasing (`winfried1+dbk{timestamp}@gmail.com`) zodat:
- ✅ Emails naar één inbox gaan (`winfried1@gmail.com`)
- ✅ Elke test unieke credentials heeft
- ✅ `email:rfc,dns` validatie werkt (gmail.com heeft geldige DNS)
- ✅ Geen emails naar externe adressen

⚠️ **BELANGRIJK**: Gebruik altijd `MAIL_MAILER=array` in Dusk tests!  
MailChannels (Hostinger's mail provider) blokkeert e-mails naar Gmail+ adressen met error 5.7.1.
Dit is geconfigureerd in `.env.dusk.local` en `.env.dusk.test`.

Zie `IMPLEMENTATION_FINDINGS.md` voor complete details.

## Tests uitvoeren

### Lokale tests (standaard):

```bash
# Alle Dusk tests
php artisan dusk

# Specifieke test class
php artisan dusk tests/Browser/PricingRequirementsTest.php

# Specifieke test methode
php artisan dusk --filter=maximum_price_per_portion_must_be_25_euro

# Met zichtbare browser (headful mode)
php artisan dusk --without-headless
```

### Remote test omgeving:

```bash
# Draai tegen test.deburenkoken.nl
php artisan dusk --env=testing
```

**Let op**: Voor remote tests moet `.env.dusk.testing` correct geconfigureerd zijn met `DUSK_SKIP_TRUNCATION=true`.

## Test structuur

Elke test file bevat:

1. **PHPDoc met requirement beschrijving** - Duidelijke documentatie van wat er getest wordt
2. **TODO commentaren** - Placeholders voor implementatie details
3. **Test methode naamgeving** - Beschrijvende namen die de requirement weerspiegelen

## HTTP Basic Authentication

De tests gebruiken HTTP Basic Authentication om toegang te krijgen tot `https://test.deburenkoken.nl/`. Dit wordt afgehandeld in de `DuskTestCase::baseUrl()` methode, die automatisch de credentials uit `.env.dusk.local` leest en toevoegt aan de URL.

**Hoe het werkt:**
```php
// In .env.dusk.local:
DUSK_HTTP_USERNAME=username
DUSK_HTTP_PASSWORD=password

// Wordt omgezet naar:
https://username:password@test.deburenkoken.nl
```

## Email Validatie & Test Data

De applicatie gebruikt **`email:rfc,dns` validatie** die controleert of het email domein geldige DNS records heeft. Dit betekent:

❌ **Test domeinen werken NIET**: `user@example.com`, `test@localhost`, etc.
✅ **Gmail+ aliasing werkt WEL**: `winfried1+dbk001@gmail.com`

**Waarom Gmail+ aliasing?**
- Alle emails gaan naar één inbox (`winfried1@gmail.com`)
- Elke alias is uniek voor het systeem (verschillende users)
- Geen emails naar externe adressen tijdens testing
- Voldoet aan DNS validatie (gmail.com heeft geldige MX records)

## Implementatie TODO's

De huidige tests zijn **skeletons** met TODO's. Voor volledige implementatie moet je:

1. ✅ **Login functionaliteit** - Gebruik `AuthenticatesUsers` trait
2. **Exacte selectors bepalen** - Identificeer HTML elementen (IDs, classes, names)
3. ✅ **Test data voorbereiden** - Run `DuskTestSeeder` op test omgeving
4. **Validatie berichten verifiëren** - Controleer exacte foutmeldingen
5. **Success flows implementeren** - Verifieer dat correcte data wel wordt geaccepteerd

## CustomerJourneyTest Setup

De `CustomerJourneyTest` test de volledige klantreis (zoeken, bestellen, betalen). Deze test is speciaal ontworpen om te draaien op zowel lokale als remote omgevingen **zonder directe database toegang**.

### Hoe het werkt

1. **Zoeken naar bestaande advertenties** - De test zoekt eerst via de publieke zoekpagina naar beschikbare advertenties
2. **Auto-creatie indien nodig** - Als er geen advertenties zijn, logt de test in als test kok en maakt een nieuwe advertentie aan
3. **Graceful skip** - Als er geen advertenties beschikbaar zijn EN er geen test kok bestaat, wordt de test overgeslagen

### Vereiste setup op remote/test server

Om de auto-creatie functie te laten werken, moet de DuskTestSeeder gedraaid worden op de test server:

```bash
# SSH naar test server
ssh user@test.deburenkoken.nl

# Navigeer naar project directory
cd /path/to/deburenkoken

# Run de DuskTestSeeder
php artisan db:seed --class=DuskTestSeeder
```

Dit maakt aan:
- 📧 **Email**: `duskkok@deburenkoken.nl`
- 🔑 **Password**: `DuskTest123!`
- 👨‍🍳 **Thuiskok profiel** in Sliedrecht
- 🍝 **Gerecht**: "Dusk Test Pasta" (€12.50)
- 📢 **Gepubliceerde advertentie** voor morgen

### Alternatief: Skip tests zonder data

Als de test kok niet kan worden aangemaakt (bv. productie-achtige beperkingen), zullen de CustomerJourneyTests netjes worden overgeslagen met een duidelijke melding. De tests zullen NIET falen.

### Voorbeeld implementatie:

```php
use Tests\Browser\Concerns\AuthenticatesUsers;

class PricingRequirementsTest extends DuskTestCase
{
    use AuthenticatesUsers;

    public function maximum_price_per_portion_must_be_25_euro()
    {
        $this->browse(function (Browser $browser) {
            // Login als test cook
            $this->loginAsCook($browser);

            $browser->visit('/dashboard/cook/dishes/create')
                    ->type('title', 'Test Gerecht')
                    ->type('portion_price', '26.00') // Te hoog!
                    ->press('Opslaan')
                    ->assertSee('De prijs mag maximaal €25.00 zijn'); // Validatiefout
        });
    }
}
```

## Troubleshooting

### ChromeDriver problemen
```bash
php artisan dusk:chrome-driver --detect
```

### HTTP Basic Auth werkt niet
Controleer of `.env.dusk.local` correct is geconfigureerd en de credentials bevat.

### Tests falen met SSL errors
Voeg SSL verificatie uit in je `DuskTestCase` als test environment self-signed certificates gebruikt.

### Screenshots van failures
Screenshots van gefaalde tests worden automatisch opgeslagen in `tests/Browser/screenshots/`.

## Best Practices

1. **Gebruik beschrijvende test namen** - Test naam moet requirement weerspiegelen
2. **Documenteer requirements** - Voeg requirement nummer en tekst toe als PHPDoc
3. **Test zowel happy als unhappy path** - Zowel valide als invalide data
4. **Gebruik Page Objects** - Voor complexere flows, overweeg Page Object pattern
5. **Keep credentials safe** - Gebruik altijd `.env.dusk.local`, nooit hardcoded credentials

## Links

- [Laravel Dusk Documentatie](https://laravel.com/docs/dusk)
- [ChromeDriver Downloads](https://chromedriver.chromium.org/downloads)
