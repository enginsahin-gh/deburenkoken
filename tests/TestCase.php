<?php

namespace Tests;

use App\Models\WebsiteStatus;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure website is "online" for tests by creating WebsiteStatus if needed
        // Only do this when using RefreshDatabase trait (database is available)
        if (in_array(\Illuminate\Foundation\Testing\RefreshDatabase::class, class_uses_recursive($this))) {
            WebsiteStatus::updateOrCreate(
                ['id' => 1],
                ['is_online' => true]
            );
        }
    }
}
