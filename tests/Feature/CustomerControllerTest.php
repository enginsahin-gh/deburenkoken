<?php

namespace Tests\Feature;

use App\Models\Advert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function advert_detail_page_loads_with_query_params(): void
    {
        $advert = Advert::factory()->published()->create();

        $response = $this->get(route('advert.details', $advert->uuid).'?calculatedDistance=3&city=Sliedrecht');

        $response->assertStatus(200);
    }

    #[Test]
    public function advert_detail_page_loads_without_query_params(): void
    {
        $advert = Advert::factory()->published()->create();

        $response = $this->get(route('advert.details', $advert->uuid));

        $response->assertStatus(200);
    }
}
