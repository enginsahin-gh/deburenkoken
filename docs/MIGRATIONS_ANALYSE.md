# Database Migrations Analyse

**Datum:** 21 december 2025  
**Totaal aantal migraties:** 58

---

## Samenvatting

De migraties zijn over het algemeen **goed gestructureerd** en volgen Laravel conventies. Er zijn echter enkele **aandachtspunten en inconsistenties** gevonden die aandacht verdienen.

---

## ✅ Wat Goed Is

### 1. UUID-based Primary Keys
Alle tabellen gebruiken consistent `uuid` als primary key, wat goed aansluit bij de `HasPrimaryUuid` trait.

### 2. Foreign Key Constraints
Relaties zijn goed gedefinieerd met `cascadeOnDelete()` voor referentiële integriteit.

### 3. Soft Deletes
Belangrijke tabellen (users, cooks, dishes, adverts, orders, reviews) hebben soft deletes, wat data recovery mogelijk maakt.

### 4. Logische Evolutie
De pricing-migratie van adverts naar dishes (2025_07_11 en 2025_07_21) is correct uitgevoerd met de juiste volgorde.

---

## ⚠️ Aandachtspunten

### 1. Lege Migratie
**Bestand:** `2025_07_21_124842_remove_portion_price_from_advert_table.php`

```php
Schema::table('advert', function (Blueprint $table) {
    // Leeg - geen actie
});
```

**Probleem:** Deze migratie is leeg en doet niets. De tabelnaam is ook incorrect (`advert` i.p.v. `adverts`).

**Aanbeveling:** Verwijder deze migratie of implementeer de beoogde functionaliteit.

---

### 2. Bestandsnaam Formatting
**Bestand:** `2025_9_22_00000_make_order_uuid_nullable_in_wallet_lines_table.php`

**Probleem:** De datum in de bestandsnaam volgt niet het standaard format:
- ❌ `2025_9_22_00000` (maand zonder leading zero, ongebruikelijk timestamp)
- ✅ `2025_09_22_000001`

**Aanbeveling:** Hernoem naar `2025_09_22_000001_make_order_uuid_nullable_in_wallet_lines_table.php`

---

### 3. Inconsistente Foreign Key Naming (dac7 tabellen)
**Bestanden:** 
- `2025_02_27_112315_create_dac7_informations_table.php` → `user_id` met `foreignUuid()`
- `2025_04_02_130005_Ensure_Dac7_Informations_Table.php` → `user_id` als `string()`
- `2025_04_03_120003_create_dac7_establishments_table.php` → `user_id` als `string()`

**Probleem:** 
- Inconsistent gebruik van `user_id` vs `user_uuid` (rest van codebase gebruikt `user_uuid`)
- Mix van `foreignUuid()` en handmatige `string()` + `foreign()` definitie
- Auto-increment `id()` i.p.v. UUID primary keys

**Aanbeveling:** Standaardiseer naar:
```php
$table->uuid('uuid')->primary();
$table->uuid('user_uuid');
$table->foreign('user_uuid')->references('uuid')->on('users')->cascadeOnDelete();
```

---

### 4. Type Mismatch: profile_deleted
**Bestand:** `2023_12_24_151352_add_cancel_column_to_orders_table.php`

```php
$table->unsignedTinyInteger('profile_deleted')->default(false)->nullable();
```

**Probleem:** 
- Kolom is `unsignedTinyInteger` maar default is `false` (boolean)
- Dit werkt technisch (MySQL cast false naar 0), maar is semantisch verwarrend

**Aanbeveling:** Gebruik consistent:
```php
$table->boolean('profile_deleted')->default(false)->nullable();
// OF
$table->unsignedTinyInteger('profile_deleted')->default(0)->nullable();
```

---

### 5. Dubbele Tabel Creatie (dac7_informations)
**Bestanden:**
- `2025_02_27_112315_create_dac7_informations_table.php`
- `2025_04_02_130005_Ensure_Dac7_Informations_Table.php`

**Probleem:** Twee migraties maken dezelfde tabel aan met `Schema::hasTable()` checks. Dit wijst op migratie-synchronisatie problemen tussen omgevingen.

**Aanbeveling:** Consolideer naar één migratie of documenteer waarom beide nodig zijn.

---

### 6. Float voor Geld Kolommen
**Bestanden:** Meerdere tabellen gebruiken `float` voor geldwaarden:
- `wallets.total_available`
- `wallets.total_processing`
- `wallets.total_paid`
- `wallet_lines.amount`
- `payments.amount`
- `dishes.portion_price`

**Probleem:** `float` kan afrondingsfouten geven bij financiële berekeningen.

**Aanbeveling:** Gebruik `decimal(10, 2)` voor betere precisie:
```php
$table->decimal('amount', 10, 2);
```

⚠️ **Let op:** Dit vereist data migratie en is een breaking change. Evalueer de risico's.

---

### 7. Orders client_uuid Foreign Key Wijziging
**Bestand:** `2023_01_06_133135_add_advert_uuid_to_orders_table.php`

```php
// Up: Foreign naar clients tabel
$table->foreign('client_uuid')->references('uuid')->on('clients')->cascadeOnDelete();

// Down: Foreign naar users tabel (origineel)
$table->foreign('client_uuid')->references('uuid')->on('users')->cascadeOnDelete();
```

**Opmerking:** De oorspronkelijke `create_orders_table` had `client_uuid` referentie naar `users`, later gewijzigd naar `clients`. Dit is correct, maar de down() methode herstelt naar de oude (incorrecte) situatie.

---

### 8. Ontbrekende Indexes
Veel kolommen die vaak gequeried worden missen indexes:

| Tabel     | Kolom           | Reden                        |
| --------- | --------------- | ---------------------------- |
| `orders`  | `status`        | Filter op actief/geannuleerd |
| `orders`  | `payment_state` | Filter op betaalstatus       |
| `orders`  | `cancelled_by`  | Filter op annuleerder        |
| `adverts` | `published`     | Filter op gepubliceerd       |
| `adverts` | `pickup_date`   | Zoeken op datum              |
| `cooks`   | `lat`, `long`   | Locatie zoeken               |

**Aanbeveling:** Voeg indexes toe voor betere query performance:
```php
$table->index('status');
$table->index('payment_state');
$table->index(['lat', 'long']); // Composite index
```

---

## 📊 Tabel Overzicht

| Tabel                 | Primary Key | Soft Delete | Foreign Keys                                   |
| --------------------- | ----------- | ----------- | ---------------------------------------------- |
| users                 | uuid ✅      | ✅           | -                                              |
| clients               | uuid ✅      | ✅           | user_uuid                                      |
| cooks                 | uuid ✅      | ✅           | user_uuid                                      |
| dishes                | uuid ✅      | ✅           | user_uuid, cook_uuid                           |
| adverts               | uuid ✅      | ✅           | dish_uuid                                      |
| orders                | uuid ✅      | ✅           | user_uuid, dish_uuid, client_uuid, advert_uuid |
| reviews               | uuid ✅      | ✅           | order_uuid, client_uuid                        |
| images                | uuid ✅      | ✅           | user_uuid, dish_uuid                           |
| wallets               | uuid ✅      | ✅           | user_uuid                                      |
| wallet_lines          | uuid ✅      | ✅           | wallet_uuid, order_uuid                        |
| payments              | uuid ✅      | ✅           | user_uuid, banking_uuid                        |
| banking               | uuid ✅      | ✅           | user_uuid                                      |
| privacy               | uuid ✅      | ✅           | user_uuid                                      |
| dac7_informations     | id ❌        | ❌           | user_id                                        |
| dac7_establishments   | id ❌        | ❌           | user_id                                        |
| iban_change_histories | uuid ✅      | ❌           | user_uuid                                      |

---

## 🔧 Aanbevolen Acties

### Hoge Prioriteit
1. ❌ **Verwijder of repareer** de lege migratie `2025_07_21_124842_remove_portion_price_from_advert_table.php`
2. 🔄 **Hernoem** `2025_9_22_00000_*` naar correct format

### Medium Prioriteit
3. 📐 **Standaardiseer** dac7 tabellen naar UUID primary keys en `user_uuid` naming
4. 🔢 **Fix type mismatch** voor `profile_deleted` kolom

### Lage Prioriteit (Performance)
5. 📈 **Voeg indexes toe** voor veelgebruikte filter kolommen
6. 💰 **Overweeg** `decimal` i.p.v. `float` voor geldwaarden (breaking change)

---

## Conclusie

De migraties zijn **functioneel correct** en de applicatie werkt. De gevonden issues zijn voornamelijk **code quality en consistency** verbeteringen, geen kritieke bugs. De aanbevelingen kunnen incrementeel worden doorgevoerd bij toekomstige development.
