# DeBurenKoken.nl

[![Laravel Dusk Tests](https://github.com/HasimHMKZ/Deburenkoken.nl/actions/workflows/dusk.yml/badge.svg?branch=main)](https://github.com/HasimHMKZ/Deburenkoken.nl/actions/workflows/dusk.yml)

Een peer-to-peer food marketplace platform dat thuiskoks verbindt met lokale klanten.

## Vereisten

- PHP 8.2+
- MySQL 8.0+
- Node.js 18+
- Composer
- [Laravel Herd](https://herd.laravel.com/) (aanbevolen voor lokale development)

## Installatie

```bash
# Clone repository
git clone git@github.com:HasimHMKZ/Deburenkoken.nl.git
cd Deburenkoken.nl

# Installeer PHP dependencies
composer install

# Installeer frontend dependencies
npm install

# Kopieer environment bestand
cp .env.example .env

# Genereer application key
php artisan key:generate

# Run migrations
php artisan migrate

# Run data migrations (voor rollen en referentie data)
php artisan data:migrate

# Build frontend assets
npm run build
```

## Development

```bash
# Start Vite development server (hot reload)
npm run dev

# Build voor productie
npm run build
```

## Code Quality

### Laravel Pint

Dit project gebruikt [Laravel Pint](https://laravel.com/docs/pint) voor code formatting.

#### Handmatig uitvoeren

```bash
# Format alle PHP bestanden
./vendor/bin/pint

# Check zonder te wijzigen
./vendor/bin/pint --test
```

#### Pre-commit Hook

Er is een pre-commit hook geïnstalleerd die automatisch Pint draait op alle staged PHP bestanden voordat een commit wordt uitgevoerd. Dit zorgt ervoor dat alle code consistent geformateerd is.

**Hook installeren (eenmalig, indien nog niet aanwezig):**

```bash
# De hook staat in .git/hooks/pre-commit
# Als deze niet bestaat, maak hem aan:
cat > .git/hooks/pre-commit << 'EOF'
#!/bin/sh

files=$(git diff --cached --name-only --diff-filter=ACM -- '*.php')

if [ -z "$files" ]; then
    exit 0
fi

if [ ! -f "vendor/bin/pint" ]; then
    echo "Laravel Pint is niet geïnstalleerd. Run: composer require laravel/pint --dev"
    exit 1
fi

echo "Running Laravel Pint on staged PHP files..."
vendor/bin/pint $files -q
git add $files
echo "Laravel Pint completed."
EOF

chmod +x .git/hooks/pre-commit
```

## Testing

### PHPUnit Tests

```bash
# Run alle unit/feature tests
php artisan test

# Met coverage
php artisan test --coverage
```

### Laravel Dusk (Browser Tests)

```bash
# Setup (eenmalig)
cp .env.dusk.local.example .env.dusk.local
# Vul de credentials in .env.dusk.local

# Run browser tests
php artisan dusk

# Run specifieke test
php artisan dusk tests/Browser/PublicPagesTest.php

# Run met zichtbare browser
php artisan dusk --without-headless
```

## GitHub Actions

### Dusk Tests

Er is een GitHub Action geconfigureerd die automatisch Dusk tests draait bij:
- Push naar `main` of `develop`
- Pull requests naar `main` of `develop`

De workflow is te vinden in `.github/workflows/dusk.yml`.

## Omgevingen

| Omgeving  | URL                            | Testing |
|-----------|--------------------------------|---------|
| Lokaal    | https://deburenkoken.test      | ✅       |
| Test      | https://test.deburenkoken.nl   | ✅       |
| Productie | https://deburenkoken.nl        | ❌       |

⚠️ **Voer NOOIT tests uit op productie!**

## Licentie

Proprietary - Alle rechten voorbehouden.
