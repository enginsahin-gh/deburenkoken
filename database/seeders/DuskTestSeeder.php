<?php

namespace Database\Seeders;

use App\Models\Advert;
use App\Models\Banking;
use App\Models\Cook;
use App\Models\Dac7Information;
use App\Models\Dish;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * DuskTestSeeder - Maakt testdata aan voor Laravel Dusk E2E tests
 *
 * Deze seeder maakt een volledig functionele thuiskok aan met:
 * - Geverifieerd gebruikersaccount
 * - Compleet kok profiel (adres, bankgegevens, DAC7 info)
 * - Een gerecht
 * - Een gepubliceerde advertentie voor morgen
 *
 * Gebruik: php artisan db:seed --class=DuskTestSeeder
 *
 * Test credentials:
 * Email: duskkok@deburenkoken.nl
 * Password: DuskTest123!
 * Username: duskkok
 */
class DuskTestSeeder extends Seeder
{
    // Test credentials - gebruik deze in Dusk tests
    public const TEST_EMAIL = 'duskkok@deburenkoken.nl';

    public const TEST_PASSWORD = 'DuskTest123!';

    public const TEST_USERNAME = 'duskkok';

    // Admin test credentials
    public const ADMIN_EMAIL = 'duskadmin@deburenkoken.nl';

    public const ADMIN_PASSWORD = 'DuskAdmin123!';

    public const ADMIN_USERNAME = 'duskadmin';

    public function run(): void
    {
        $this->command->info('🧪 Starting Dusk Test Data Seeder...');

        // Check of user al bestaat
        $existingUser = User::where('email', self::TEST_EMAIL)->first();

        if ($existingUser) {
            $this->command->info('⚠️  Dusk test user already exists. Refreshing test data...');
            $this->refreshTestData($existingUser);
        } else {
            // Maak nieuwe user aan
            $user = $this->createUser();
            $cook = $this->createCook($user);
            $this->createUserProfile($user);
            $this->createWallet($user);
            $this->createBanking($user);
            $this->createDac7Information($user);
            $dish = $this->createDish($user, $cook);
            $this->createAdvert($dish);

            $this->command->info('');
            $this->command->info('✅ Dusk test data created successfully!');
            $this->command->info('');
            $this->command->info('📧 Email: '.self::TEST_EMAIL);
            $this->command->info('🔑 Password: '.self::TEST_PASSWORD);
            $this->command->info('👤 Username: '.self::TEST_USERNAME);
        }

        // Admin user aanmaken of bijwerken
        $this->ensureAdminExists();

        $this->command->info('');
    }

    private function createUser(): User
    {
        $user = User::create([
            'username' => self::TEST_USERNAME,
            'email' => self::TEST_EMAIL,
            'password' => Hash::make(self::TEST_PASSWORD),
            'email_verified_at' => now(),
        ]);

        // Assign cook role
        $cookRole = Role::where('name', 'cook')->first();
        if ($cookRole) {
            $user->assignRole($cookRole);
        }

        $this->command->info('👤 User created: '.$user->username);

        return $user;
    }

    private function createCook(User $user): Cook
    {
        $cook = Cook::create([
            'user_uuid' => $user->uuid,
            'lat' => 51.8248681, // Sliedrecht
            'long' => 4.773162399999999,
            'street' => 'Kerkbuurt',
            'house_number' => 1,
            'addition' => null,
            'postal_code' => '3361AB',
            'city' => 'Sliedrecht',
            'country' => 'NL',
            'description' => 'Ik ben een Dusk test thuiskok.',
            'mail_order' => true,
            'mail_cancel' => true,
            'mail_self' => false,
        ]);

        $this->command->info('👨‍🍳 Cook profile created in Sliedrecht');

        return $cook;
    }

    private function createUserProfile(User $user): void
    {
        UserProfile::create([
            'user_uuid' => $user->uuid,
            'firstname' => 'Dusk',
            'lastname' => 'Testkok',
            'phone_number' => '0612345678',
            'birthday' => '1990-01-15',
        ]);

        $this->command->info('📋 User profile created');
    }

    private function createWallet(User $user): void
    {
        Wallet::create([
            'user_uuid' => $user->uuid,
            'total_available' => 0,
            'total_processing' => 0,
            'total_paid' => 0,
            'state' => Wallet::FULL,
        ]);

        $this->command->info('💰 Wallet created');
    }

    private function createBanking(User $user): void
    {
        Banking::create([
            'user_uuid' => $user->uuid,
            'iban' => 'NL91ABNA0417164300',
            'account_holder' => 'D. Testkok',
            'verified' => true,
        ]);

        $this->command->info('🏦 Banking info created');
    }

    private function createDac7Information(User $user): void
    {
        Dac7Information::create([
            'user_id' => $user->uuid,
            'information_provided' => true,
        ]);

        $this->command->info('📄 DAC7 information created');
    }

    private function createDish(User $user, Cook $cook): Dish
    {
        $dish = Dish::create([
            'user_uuid' => $user->uuid,
            'cook_uuid' => $cook->uuid,
            'title' => 'Dusk Test Pasta',
            'description' => 'Heerlijke Italiaanse pasta met verse tomaten, basilicum en Parmezaanse kaas. '.
                'Dit gerecht is speciaal gemaakt voor Dusk E2E tests en is altijd beschikbaar.',
            'is_vegetarian' => true,
            'is_vegan' => false,
            'is_halal' => true,
            'has_alcohol' => false,
            'has_gluten' => true,
            'has_lactose' => true,
            'spice_level' => 1,
            'portion_price' => 12.50,
        ]);

        $this->command->info('🍝 Dish created: '.$dish->title);

        return $dish;
    }

    private function createAdvert(Dish $dish): void
    {
        // Maak advertentie voor morgen
        $tomorrow = Carbon::tomorrow();

        $advert = Advert::create([
            'dish_uuid' => $dish->uuid,
            'portion_amount' => 10,
            'pickup_date' => $tomorrow->format('Y-m-d'),
            'pickup_from' => '17:00',
            'pickup_to' => '19:00',
            'order_date' => $tomorrow->format('Y-m-d'),
            'order_time' => '14:00',
            'published' => now(),
            'profile_deleted' => false,
            'preparation_email_sent' => false,
        ]);

        $this->command->info('📢 Advert created for '.$tomorrow->format('d-m-Y').' (published)');
    }

    /**
     * Ververs testdata voor bestaande gebruiker
     */
    private function refreshTestData(User $user): void
    {
        // Ensure the user has the cook role
        $cookRole = Role::where('name', 'cook')->first();
        if ($cookRole && ! $user->hasRole('cook')) {
            $user->assignRole($cookRole);
            $this->command->info('👨‍🍳 Cook role assigned to existing user');
        }

        // Verwijder oude adverts (alleen voor deze test user)
        $cook = Cook::where('user_uuid', $user->uuid)->first();

        if ($cook) {
            $dishes = Dish::where('cook_uuid', $cook->uuid)->get();

            foreach ($dishes as $dish) {
                // Verwijder alle oude adverts
                Advert::where('dish_uuid', $dish->uuid)
                    ->forceDelete();
            }

            // Maak nieuwe advert voor morgen
            if ($dishes->isNotEmpty()) {
                $this->createAdvert($dishes->first());
                $this->command->info('📢 Fresh advert created for tomorrow');
            }
        }
    }

    /**
     * Zorg dat er een admin user bestaat voor Dusk tests
     */
    private function ensureAdminExists(): void
    {
        $existingAdmin = User::where('email', self::ADMIN_EMAIL)->first();

        if ($existingAdmin) {
            // Zorg dat admin role is toegewezen
            $adminRole = Role::where('name', 'admin')->first();
            if ($adminRole && ! $existingAdmin->hasRole('admin')) {
                $existingAdmin->assignRole($adminRole);
            }
            $this->command->info('✅ Admin user already exists');

            return;
        }

        // Maak nieuwe admin user aan
        $admin = User::create([
            'username' => self::ADMIN_USERNAME,
            'email' => self::ADMIN_EMAIL,
            'password' => Hash::make(self::ADMIN_PASSWORD),
            'email_verified_at' => now(),
        ]);

        // Assign admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $admin->assignRole($adminRole);
        }

        $this->command->info('👑 Admin user created: '.self::ADMIN_EMAIL);
    }
}
