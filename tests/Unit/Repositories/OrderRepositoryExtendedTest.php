<?php

namespace Tests\Unit\Repositories;

use App\Models\Advert;
use App\Models\Client;
use App\Models\Order;
use App\Models\User;
use App\Repositories\OrderRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderRepositoryExtendedTest extends TestCase
{
    use RefreshDatabase;

    private OrderRepository $orderRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = app(OrderRepository::class);
    }

    #[Test]
    public function it_can_find_order_by_uuid(): void
    {
        $order = Order::factory()->create();

        $found = $this->orderRepository->find($order->getUuid());

        $this->assertNotNull($found);
        $this->assertEquals($order->getUuid(), $found->getUuid());
    }

    #[Test]
    public function it_returns_null_when_order_not_found(): void
    {
        $result = $this->orderRepository->find('non-existent-uuid');

        $this->assertNull($result);
    }

    #[Test]
    public function it_can_get_orders_for_user(): void
    {
        $user = User::factory()->create();
        Order::factory()->count(3)->create([
            'user_uuid' => $user->getUuid(),
            'payment_state' => Order::SUCCEED,
        ]);
        Order::factory()->count(2)->create(['payment_state' => Order::SUCCEED]); // Other user's orders

        $orders = $this->orderRepository->getForUser($user->getUuid(), [], 1);

        $this->assertInstanceOf(LengthAwarePaginator::class, $orders);
        $this->assertEquals(3, $orders->total());
    }

    #[Test]
    public function it_can_get_canceled_orders_by_cook(): void
    {
        $user = User::factory()->create();

        // Cancelled by cook (must have SUCCEED or PAYOUT_PENDING payment_state)
        Order::factory()->count(2)->create([
            'user_uuid' => $user->getUuid(),
            'status' => Order::STATUS_GEANNULEERD,
            'cancelled_by' => Order::CANCELLED_BY_COOK,
            'payment_state' => Order::SUCCEED,
            'updated_at' => Carbon::now(),
        ]);

        // Cancelled by client (should not be included)
        Order::factory()->create([
            'user_uuid' => $user->getUuid(),
            'status' => Order::STATUS_GEANNULEERD,
            'cancelled_by' => Order::CANCELLED_BY_CLIENT,
            'payment_state' => Order::SUCCEED,
            'updated_at' => Carbon::now(),
        ]);

        $cancelledByCook = $this->orderRepository->getCanceledOrdersByUser(
            $user->getUuid(),
            'month',
            Order::CANCELLED_BY_COOK
        );

        $this->assertCount(2, $cancelledByCook);
    }

    #[Test]
    public function it_can_get_canceled_orders_by_client(): void
    {
        $user = User::factory()->create();

        // Cancelled by client (must have SUCCEED or PAYOUT_PENDING payment_state)
        Order::factory()->count(3)->create([
            'user_uuid' => $user->getUuid(),
            'status' => Order::STATUS_GEANNULEERD,
            'cancelled_by' => Order::CANCELLED_BY_CLIENT,
            'payment_state' => Order::SUCCEED,
            'updated_at' => Carbon::now(),
        ]);

        // Cancelled by cook (should not be included)
        Order::factory()->create([
            'user_uuid' => $user->getUuid(),
            'status' => Order::STATUS_GEANNULEERD,
            'cancelled_by' => Order::CANCELLED_BY_COOK,
            'payment_state' => Order::SUCCEED,
            'updated_at' => Carbon::now(),
        ]);

        $cancelledByClient = $this->orderRepository->getCanceledOrdersByUser(
            $user->getUuid(),
            'day',
            Order::CANCELLED_BY_CLIENT
        );

        $this->assertCount(3, $cancelledByClient);
    }

    #[Test]
    public function it_can_get_cancellations_by_email_today(): void
    {
        $client = Client::factory()->create(['email' => 'test@example.com']);

        // Cancellations today
        Order::factory()->count(2)->create([
            'client_uuid' => $client->getUuid(),
            'status' => Order::STATUS_GEANNULEERD,
            'cancelled_by' => Order::CANCELLED_BY_CLIENT,
        ]);

        $count = $this->orderRepository->getCancellationsByEmailToday('test@example.com');

        $this->assertEquals(2, $count);
    }

    #[Test]
    public function it_can_set_review_send(): void
    {
        $order = Order::factory()->create(['review_send' => null]);

        $this->assertNull($order->review_send);

        $this->orderRepository->setReviewSend($order);

        $order->refresh();
        $this->assertNotNull($order->review_send);
    }

    #[Test]
    public function it_can_set_deleted_profile(): void
    {
        $order = Order::factory()->create(['profile_deleted' => 0]);

        $this->assertEquals(0, $order->profile_deleted);

        $this->orderRepository->setDeletedProfile($order);

        $order->refresh();
        $this->assertEquals(1, $order->profile_deleted);
    }

    #[Test]
    public function order_payment_states_are_correct(): void
    {
        $this->assertEquals(1, Order::IN_PROCESS);
        $this->assertEquals(2, Order::SUCCEED);
        $this->assertEquals(3, Order::FAILED);
        $this->assertEquals(4, Order::CANCELLED);
        $this->assertEquals(5, Order::PAID_OUT);
        $this->assertEquals(6, Order::PAYOUT_PENDING);
    }

    #[Test]
    public function order_status_constants_are_correct(): void
    {
        $this->assertEquals('Actief', Order::STATUS_ACTIEF);
        $this->assertEquals('Verlopen', Order::STATUS_VERLOPEN);
        $this->assertEquals('Geannuleerd', Order::STATUS_GEANNULEERD);
    }

    #[Test]
    public function order_cancelled_by_constants_are_correct(): void
    {
        $this->assertEquals('client', Order::CANCELLED_BY_CLIENT);
        $this->assertEquals('cook', Order::CANCELLED_BY_COOK);
    }

    #[Test]
    public function it_can_exclude_in_process_orders(): void
    {
        $user = User::factory()->create();

        // In process orders
        Order::factory()->count(2)->create([
            'user_uuid' => $user->getUuid(),
            'payment_state' => Order::IN_PROCESS,
        ]);

        // Succeeded orders
        Order::factory()->count(3)->create([
            'user_uuid' => $user->getUuid(),
            'payment_state' => Order::SUCCEED,
        ]);

        $orders = $this->orderRepository->getForUser($user->getUuid(), [
            'exclude_in_process' => true,
        ], 1);

        $this->assertEquals(3, $orders->total());
    }

    #[Test]
    public function it_can_filter_orders_by_date_range(): void
    {
        $user = User::factory()->create();
        $advert = Advert::factory()->create();

        // Orders in date range with SUCCEED payment state
        Order::factory()->count(2)->create([
            'user_uuid' => $user->getUuid(),
            'advert_uuid' => $advert->getUuid(),
            'payment_state' => Order::SUCCEED,
            'created_at' => Carbon::now()->subDays(2),
        ]);

        // Orders outside date range
        Order::factory()->create([
            'user_uuid' => $user->getUuid(),
            'advert_uuid' => $advert->getUuid(),
            'payment_state' => Order::SUCCEED,
            'created_at' => Carbon::now()->subMonths(2),
        ]);

        $orders = $this->orderRepository->getForUser($user->getUuid(), [
            'from' => Carbon::now()->subWeek()->format('Y-m-d'),
            'to' => Carbon::now()->format('Y-m-d'),
        ], 1);

        $this->assertEquals(2, $orders->total());
    }

    #[Test]
    public function order_has_correct_relationships(): void
    {
        $order = Order::factory()->create();

        $this->assertTrue($order->relationLoaded('dish') || method_exists($order, 'dish'));
        $this->assertTrue($order->relationLoaded('client') || method_exists($order, 'client'));
        $this->assertTrue($order->relationLoaded('user') || method_exists($order, 'user'));
        $this->assertTrue($order->relationLoaded('advert') || method_exists($order, 'advert'));
    }

    #[Test]
    public function paid_order_has_succeed_payment_state(): void
    {
        $order = Order::factory()->paid()->create();

        $this->assertEquals(Order::SUCCEED, $order->getPaymentState());
        $this->assertEquals(Order::STATUS_ACTIEF, $order->getStatus());
    }

    #[Test]
    public function cancelled_order_by_cook_is_properly_tracked(): void
    {
        $order = Order::factory()->cancelledByCook()->create();

        $this->assertEquals(Order::STATUS_GEANNULEERD, $order->getStatus());
        $this->assertEquals(Order::CANCELLED_BY_COOK, $order->cancelled_by);
    }

    #[Test]
    public function cancelled_order_by_client_is_properly_tracked(): void
    {
        $order = Order::factory()->cancelledByClient()->create();

        $this->assertEquals(Order::STATUS_GEANNULEERD, $order->getStatus());
        $this->assertEquals(Order::CANCELLED_BY_CLIENT, $order->cancelled_by);
    }
}
