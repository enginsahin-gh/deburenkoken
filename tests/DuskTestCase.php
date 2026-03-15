<?php

namespace Tests;

use Database\Seeders\DuskTestSeeder;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Support\Collection;
use Laravel\Dusk\Browser;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Use DatabaseTruncation for faster local tests.
     * This trait truncates tables between tests instead of re-migrating.
     *
     * For remote environments (DUSK_REMOTE=true), truncation is skipped.
     */
    use DatabaseTruncation;

    /**
     * Tables that should NOT be truncated between tests.
     * These contain reference data that doesn't change.
     */
    protected array $exceptTables = [
        'migrations',
        'data_migrations',
        'roles',
        'permissions',
        'role_has_permissions',
        'model_has_permissions',
        'model_has_roles',
        'website_status',
        'mail_messages',
        'password_resets',
    ];

    /**
     * Check if database truncation should be skipped.
     * Set DUSK_SKIP_TRUNCATION=true when testing against external servers
     * where you don't have direct database access.
     */
    protected function shouldSkipTruncation(): bool
    {
        return filter_var(env('DUSK_SKIP_TRUNCATION', false), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Override truncation for remote environments.
     * When testing against a remote server, we cannot truncate the database.
     *
     * Note: We NEVER run migrate:fresh for Dusk tests - we assume the database
     * already exists and has the correct schema. This is because Dusk tests
     * run against a real application server that uses the same database.
     */
    protected function truncateDatabaseTables(): void
    {
        if ($this->shouldSkipTruncation()) {
            // Skip truncation - used for external test servers
            // where we don't have direct database access
            return;
        }

        // Mark as migrated to prevent migrate:fresh from running
        // Dusk tests should never wipe the database schema
        \Illuminate\Foundation\Testing\RefreshDatabaseState::$migrated = true;

        $this->beforeTruncatingDatabase();

        // Truncate tables (but NOT the schema)
        $this->truncateTablesForAllConnections();

        // Reseed after truncation
        $this->artisan('db:seed', ['--class' => $this->seeder()]);

        $this->afterTruncatingDatabase();
    }

    /**
     * Define the seeder class to use after truncation.
     */
    protected function seeder()
    {
        return DuskTestSeeder::class;
    }

    /**
     * Set up each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // When not skipping truncation, ensure roles exist
        if (! $this->shouldSkipTruncation()) {
            // Ensure roles exist (from data migrations, these tables are not truncated)
            $this->ensureRolesExist();
        }

        // Ensure test adverts have future dates (prevents stale data failures)
        $this->refreshExpiredTestAdverts();

        // Register browser macros
        $this->registerBrowserMacros();
    }

    /**
     * Ensure roles exist in database (normally created by data migrations).
     */
    protected function ensureRolesExist(): void
    {
        $roleCount = \Spatie\Permission\Models\Role::count();

        if ($roleCount === 0) {
            // Create default roles if they don't exist
            \Spatie\Permission\Models\Role::create(['name' => 'customer', 'guard_name' => 'web']);
            \Spatie\Permission\Models\Role::create(['name' => 'cook', 'guard_name' => 'web']);
            \Spatie\Permission\Models\Role::create(['name' => 'admin', 'guard_name' => 'web']);
        }
    }

    /**
     * Refresh any expired test adverts so they have future pickup dates.
     * This prevents test failures when adverts created by DuskTestSeeder
     * have pickup dates that have passed since the last seed.
     */
    protected function refreshExpiredTestAdverts(): void
    {
        $testUser = \App\Models\User::where('email', DuskTestSeeder::TEST_EMAIL)->first();

        if (! $testUser) {
            return;
        }

        $cook = \App\Models\Cook::where('user_uuid', $testUser->uuid)->first();

        if (! $cook) {
            return;
        }

        $tomorrow = \Carbon\Carbon::tomorrow();

        \App\Models\Advert::whereHas('dish', function ($query) use ($cook) {
            $query->where('cook_uuid', $cook->uuid);
        })
            ->where('pickup_date', '<', $tomorrow->format('Y-m-d'))
            ->update([
                'pickup_date' => $tomorrow->format('Y-m-d'),
                'order_date' => $tomorrow->format('Y-m-d'),
            ]);
    }

    /**
     * Register custom browser macros for common actions.
     */
    protected function registerBrowserMacros(): void
    {
        // Macro to dismiss all overlays (intro.js and cookie banner)
        Browser::macro('dismissOverlays', function () {
            /** @var Browser $this */
            $this->script("
                // Dismiss intro.js overlay
                const skipBtn = document.querySelector('.introjs-skipbutton');
                const doneBtn = document.querySelector('.introjs-donebutton');
                const closeBtn = document.querySelector('.introjs-tooltip button[aria-label=\"Close\"]');
                if (skipBtn) skipBtn.click();
                else if (doneBtn) doneBtn.click();
                else if (closeBtn) closeBtn.click();

                // Dismiss cookie banner
                const acceptBtn = document.querySelector('button[value=\"accept\"]');
                if (acceptBtn) acceptBtn.click();
            ");

            return $this->pause(300);
        });

        // Macro to login as a cook
        Browser::macro('loginAsCook', function (string $email, string $password) {
            /** @var Browser $this */
            // First visit and dismiss overlays before filling the form
            $this->visit('/login')
                ->waitFor('input[name="email"]', 10);

            // Dismiss cookie banner first (important for form interaction)
            $this->dismissOverlays();

            // Now fill and submit the form
            $this->type('input[name="email"]', $email)
                ->type('input[name="password"]', $password)
                ->press('Inloggen')
                ->pause(3000); // Wait for redirect to complete

            // Dismiss any intro.js overlays on dashboard
            $this->dismissOverlays();

            return $this;
        });

        // Macro to scroll to element and click
        Browser::macro('scrollAndClick', function (string $selector) {
            /** @var Browser $this */
            $this->scrollIntoView($selector)
                ->pause(200)
                ->click($selector);

            return $this;
        });

        // Macro to login as admin
        Browser::macro('loginAsAdmin', function (string $email, string $password) {
            /** @var Browser $this */
            // First visit and dismiss overlays before filling the form
            $this->visit('/login')
                ->waitFor('input[name="email"]', 10);

            // Dismiss cookie banner first (important for form interaction)
            $this->dismissOverlays();

            // Now fill and submit the form
            $this->type('input[name="email"]', $email)
                ->type('input[name="password"]', $password)
                ->press('Inloggen')
                ->pause(3000); // Wait for redirect to complete

            // Dismiss any intro.js overlays on dashboard
            $this->dismissOverlays();

            return $this;
        });
    }

    /**
     * Prepare for Dusk test execution.
     */
    #[BeforeClass]
    public static function prepare(): void
    {
        if (! static::runningInSail()) {
            static::startChromeDriver(['--port=9515']);
        }
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=1920,1080',
            '--disable-search-engine-choice-screen',
            '--disable-smooth-scrolling',
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge([
                '--disable-gpu',
                '--headless=new',
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL') ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    /**
     * Get the base URL for Dusk tests with HTTP Basic Auth if needed.
     */
    protected function baseUrl(): string
    {
        $url = env('APP_URL', 'http://localhost');

        // Add HTTP Basic Auth credentials if provided
        $username = env('DUSK_HTTP_USERNAME');
        $password = env('DUSK_HTTP_PASSWORD');

        if ($username && $password) {
            // Parse the URL and inject credentials
            $parsed = parse_url($url);
            $scheme = $parsed['scheme'] ?? 'https';
            $host = $parsed['host'] ?? '';
            $port = isset($parsed['port']) ? ':'.$parsed['port'] : '';
            $path = $parsed['path'] ?? '';

            return "{$scheme}://{$username}:{$password}@{$host}{$port}{$path}";
        }

        return $url;
    }
}
