<?php

namespace Tests\Unit\Models;

use App\Models\Advert;
use App\Models\Client;
use App\Models\Cook;
use App\Models\Dish;
use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CookTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function cook_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        $this->assertInstanceOf(User::class, $cook->user);
        $this->assertEquals($user->uuid, $cook->user->uuid);
    }

    #[Test]
    public function cook_uses_uuid_as_primary_key(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        $this->assertNotNull($cook->uuid);
        $this->assertIsString($cook->uuid);
        $this->assertEquals(36, strlen($cook->uuid));
    }

    #[Test]
    public function cook_can_be_soft_deleted(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        $cook->delete();

        $this->assertSoftDeleted($cook);
        $this->assertNotNull(Cook::withTrashed()->find($cook->uuid));
    }

    #[Test]
    public function cook_has_address_fields(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create([
            'user_uuid' => $user->uuid,
            'street' => 'Teststraat',
            'house_number' => 123,
            'postal_code' => '1234AB',
            'city' => 'Amsterdam',
        ]);

        $this->assertEquals('Teststraat', $cook->street);
        $this->assertEquals(123, $cook->house_number);
        $this->assertEquals('1234AB', $cook->postal_code);
        $this->assertEquals('Amsterdam', $cook->city);
    }

    #[Test]
    public function cook_has_location_coordinates(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create([
            'user_uuid' => $user->uuid,
            'lat' => 52.3702,
            'long' => 4.8951,
        ]);

        $this->assertEquals(52.3702, $cook->lat);
        $this->assertEquals(4.8951, $cook->long);
    }

    #[Test]
    public function cook_can_have_dishes(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        $this->assertCount(0, $cook->dishes);
    }

    #[Test]
    public function cook_user_can_have_wallet(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        // Create wallet for the user (not cook directly)
        $wallet = Wallet::create([
            'user_uuid' => $user->uuid,
            'total_available' => 0,
            'total_processing' => 0,
            'total_paid' => 0,
            'state' => Wallet::LIMITED,
        ]);

        $this->assertInstanceOf(Wallet::class, $user->wallet);
        $this->assertEquals($wallet->uuid, $user->wallet->uuid);
    }

    #[Test]
    public function cook_returns_parsed_uuid(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        $parsedUuid = $cook->getParsedUuid();
        $this->assertEquals(6, strlen($parsedUuid));
        $this->assertEquals(substr($cook->uuid, -6), $parsedUuid);
    }

    #[Test]
    public function cook_has_description_field(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create([
            'user_uuid' => $user->uuid,
            'description' => 'Ik ben een gepassioneerde thuiskok.',
        ]);

        $this->assertEquals('Ik ben een gepassioneerde thuiskok.', $cook->description);
    }

    // ========================================
    // TESTS VOOR getSoldPortions() - BL-186
    // ========================================

    /**
     * Helper method om een volledige order setup te maken voor een cook
     */
    private function createOrderForCook(Cook $cook, array $orderAttributes = []): Order
    {
        $dish = Dish::factory()->create([
            'user_uuid' => $cook->user_uuid,
            'cook_uuid' => $cook->uuid,
        ]);

        $advert = Advert::factory()->published()->create([
            'dish_uuid' => $dish->uuid,
        ]);

        $customerUser = User::factory()->create();
        $client = Client::factory()->create(['user_uuid' => $customerUser->uuid]);

        return Order::factory()->create(array_merge([
            'dish_uuid' => $dish->uuid,
            'advert_uuid' => $advert->uuid,
            'user_uuid' => $cook->user_uuid,
            'client_uuid' => $client->uuid,
        ], $orderAttributes));
    }

    #[Test]
    public function get_sold_portions_returns_zero_when_no_orders(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        $this->assertEquals(0, $cook->getSoldPortions());
    }

    #[Test]
    public function get_sold_portions_counts_succeed_orders(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        // Order met SUCCEED payment_state en STATUS_ACTIEF
        $this->createOrderForCook($cook, [
            'portion_amount' => 3,
            'payment_state' => Order::SUCCEED,
            'status' => Order::STATUS_ACTIEF,
        ]);

        $this->assertEquals(3, $cook->getSoldPortions());
    }

    #[Test]
    public function get_sold_portions_counts_payout_pending_orders(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        // Order met PAYOUT_PENDING payment_state
        $this->createOrderForCook($cook, [
            'portion_amount' => 5,
            'payment_state' => Order::PAYOUT_PENDING,
            'status' => Order::STATUS_ACTIEF,
        ]);

        $this->assertEquals(5, $cook->getSoldPortions());
    }

    #[Test]
    public function get_sold_portions_counts_paid_out_orders(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        // Order met PAID_OUT payment_state
        $this->createOrderForCook($cook, [
            'portion_amount' => 4,
            'payment_state' => Order::PAID_OUT,
            'status' => Order::STATUS_VERLOPEN,
        ]);

        $this->assertEquals(4, $cook->getSoldPortions());
    }

    #[Test]
    public function get_sold_portions_excludes_cancelled_orders(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        // Succesvolle order
        $this->createOrderForCook($cook, [
            'portion_amount' => 5,
            'payment_state' => Order::SUCCEED,
            'status' => Order::STATUS_ACTIEF,
        ]);

        // Geannuleerde order (was betaald, nu geannuleerd)
        $this->createOrderForCook($cook, [
            'portion_amount' => 3,
            'payment_state' => Order::SUCCEED,
            'status' => Order::STATUS_GEANNULEERD,
            'cancelled_by' => Order::CANCELLED_BY_CLIENT,
        ]);

        // Alleen de niet-geannuleerde order moet meetellen
        $this->assertEquals(5, $cook->getSoldPortions());
    }

    #[Test]
    public function get_sold_portions_excludes_in_process_orders(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        // Order in process (nog niet betaald)
        $this->createOrderForCook($cook, [
            'portion_amount' => 2,
            'payment_state' => Order::IN_PROCESS,
            'status' => Order::STATUS_ACTIEF,
        ]);

        $this->assertEquals(0, $cook->getSoldPortions());
    }

    #[Test]
    public function get_sold_portions_excludes_failed_orders(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        // Gefaalde order
        $this->createOrderForCook($cook, [
            'portion_amount' => 2,
            'payment_state' => Order::FAILED,
            'status' => Order::STATUS_ACTIEF,
        ]);

        $this->assertEquals(0, $cook->getSoldPortions());
    }

    #[Test]
    public function get_sold_portions_sums_multiple_orders(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        // Meerdere succesvolle orders
        $this->createOrderForCook($cook, [
            'portion_amount' => 3,
            'payment_state' => Order::SUCCEED,
            'status' => Order::STATUS_ACTIEF,
        ]);

        $this->createOrderForCook($cook, [
            'portion_amount' => 5,
            'payment_state' => Order::PAYOUT_PENDING,
            'status' => Order::STATUS_ACTIEF,
        ]);

        $this->createOrderForCook($cook, [
            'portion_amount' => 2,
            'payment_state' => Order::PAID_OUT,
            'status' => Order::STATUS_VERLOPEN,
        ]);

        // 3 + 5 + 2 = 10
        $this->assertEquals(10, $cook->getSoldPortions());
    }

    #[Test]
    public function get_sold_portions_counts_verlopen_orders(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        // Order met STATUS_VERLOPEN (ophaaltijd is gepasseerd)
        $this->createOrderForCook($cook, [
            'portion_amount' => 4,
            'payment_state' => Order::SUCCEED,
            'status' => Order::STATUS_VERLOPEN,
        ]);

        $this->assertEquals(4, $cook->getSoldPortions());
    }

    #[Test]
    public function get_sold_portions_comprehensive_scenario(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        // Scenario: Complete test van alle mogelijke combinaties
        //
        // MOETEN MEETELLEN:
        // - 3 porties: SUCCEED + ACTIEF
        // - 5 porties: PAYOUT_PENDING + ACTIEF
        // - 2 porties: PAID_OUT + VERLOPEN
        // - 4 porties: SUCCEED + VERLOPEN
        // Totaal: 14 porties
        //
        // MOETEN NIET MEETELLEN:
        // - 6 porties: SUCCEED + GEANNULEERD (geannuleerd na betaling)
        // - 1 portie: IN_PROCESS + ACTIEF (nog niet betaald)
        // - 2 porties: FAILED + ACTIEF (betaling mislukt)
        // - 3 porties: CANCELLED + GEANNULEERD (nooit betaald, geannuleerd)

        // Porties die WEL meetellen
        $this->createOrderForCook($cook, [
            'portion_amount' => 3,
            'payment_state' => Order::SUCCEED,
            'status' => Order::STATUS_ACTIEF,
        ]);

        $this->createOrderForCook($cook, [
            'portion_amount' => 5,
            'payment_state' => Order::PAYOUT_PENDING,
            'status' => Order::STATUS_ACTIEF,
        ]);

        $this->createOrderForCook($cook, [
            'portion_amount' => 2,
            'payment_state' => Order::PAID_OUT,
            'status' => Order::STATUS_VERLOPEN,
        ]);

        $this->createOrderForCook($cook, [
            'portion_amount' => 4,
            'payment_state' => Order::SUCCEED,
            'status' => Order::STATUS_VERLOPEN,
        ]);

        // Porties die NIET meetellen
        $this->createOrderForCook($cook, [
            'portion_amount' => 6,
            'payment_state' => Order::SUCCEED,
            'status' => Order::STATUS_GEANNULEERD,
            'cancelled_by' => Order::CANCELLED_BY_COOK,
        ]);

        $this->createOrderForCook($cook, [
            'portion_amount' => 1,
            'payment_state' => Order::IN_PROCESS,
            'status' => Order::STATUS_ACTIEF,
        ]);

        $this->createOrderForCook($cook, [
            'portion_amount' => 2,
            'payment_state' => Order::FAILED,
            'status' => Order::STATUS_ACTIEF,
        ]);

        $this->createOrderForCook($cook, [
            'portion_amount' => 3,
            'payment_state' => Order::CANCELLED,
            'status' => Order::STATUS_GEANNULEERD,
        ]);

        // Verwacht: 3 + 5 + 2 + 4 = 14 porties
        $this->assertEquals(14, $cook->getSoldPortions());
    }

    #[Test]
    public function get_sold_portions_counts_all_time_not_just_recent(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        // Order van 1 jaar geleden
        $oldOrder = $this->createOrderForCook($cook, [
            'portion_amount' => 10,
            'payment_state' => Order::SUCCEED,
            'status' => Order::STATUS_VERLOPEN,
        ]);
        $oldOrder->update(['created_at' => Carbon::now()->subYear()]);

        // Order van vandaag
        $this->createOrderForCook($cook, [
            'portion_amount' => 5,
            'payment_state' => Order::SUCCEED,
            'status' => Order::STATUS_ACTIEF,
        ]);

        // Beide orders moeten meetellen (all time)
        $this->assertEquals(15, $cook->getSoldPortions());
    }

    #[Test]
    public function count_portions_alias_returns_same_as_get_sold_portions(): void
    {
        $user = User::factory()->create();
        $cook = Cook::factory()->create(['user_uuid' => $user->uuid]);

        $this->createOrderForCook($cook, [
            'portion_amount' => 7,
            'payment_state' => Order::SUCCEED,
            'status' => Order::STATUS_ACTIEF,
        ]);

        // countPortions() is een alias voor getSoldPortions()
        $this->assertEquals($cook->getSoldPortions(), $cook->countPortions());
        $this->assertEquals(7, $cook->countPortions());
    }
}
