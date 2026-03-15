<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Mail\OrderCookCancelled;
use App\Mail\OrderCustomerCancelled;
use App\Models\Advert;
use App\Models\Client;
use App\Models\Cook;
use App\Models\Dish;
use App\Models\Order;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CancellationSecurityTest extends TestCase
{
    use RefreshDatabase;

    private User $cookUser;

    private Cook $cook;

    private Order $order;

    private Client $client;

    private Dish $dish;

    private string $validKey;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate(Roles::CUSTOMER, 'web');
        Role::findOrCreate(Roles::COOK, 'web');
        Role::findOrCreate(Roles::ADMIN, 'web');

        $this->cookUser = User::factory()->create();
        $this->cookUser->assignRole(Roles::COOK);

        UserProfile::create([
            'user_uuid' => $this->cookUser->uuid,
            'firstname' => 'Test',
            'lastname' => 'Kok',
            'phone_number' => '0612345678',
            'birthday' => now()->subYears(30),
        ]);

        $this->cook = Cook::factory()->create(['user_uuid' => $this->cookUser->uuid]);
        $this->client = Client::factory()->create();

        $this->dish = Dish::factory()->create([
            'user_uuid' => $this->cookUser->uuid,
            'cook_uuid' => $this->cook->uuid,
        ]);

        $advert = Advert::factory()->create(['dish_uuid' => $this->dish->uuid]);

        $this->order = Order::factory()->paid()->create([
            'user_uuid' => $this->cookUser->uuid,
            'client_uuid' => $this->client->uuid,
            'dish_uuid' => $this->dish->uuid,
            'advert_uuid' => $advert->uuid,
            'payment_id' => null,
        ]);

        $this->validKey = encrypt($this->order->client->getCreatedAt()->unix());
    }

    // --- XSS Protection ---

    #[Test]
    public function cancel_text_with_html_tags_is_escaped_in_cook_email(): void
    {
        Mail::fake();

        $xssPayload = '<script>alert("XSS")</script>';

        $this->post(
            route('submit.customer.cancel.order', ['uuid' => $this->order->uuid, 'key' => $this->validKey]),
            ['cancel_text' => $xssPayload]
        );

        Mail::assertSent(OrderCookCancelled::class, function (OrderCookCancelled $mail) use ($xssPayload) {
            $html = $mail->render();
            // The raw <script> tag should NOT appear unescaped in the HTML
            $this->assertStringNotContainsString($xssPayload, $html);
            // The escaped version should be present
            $this->assertStringContainsString('&lt;script&gt;', $html);

            return true;
        });
    }

    #[Test]
    public function cancel_text_with_html_tags_is_escaped_in_customer_email(): void
    {
        Mail::fake();

        $xssPayload = '<img src=x onerror=alert(1)>';

        $this->post(
            route('submit.customer.cancel.order', ['uuid' => $this->order->uuid, 'key' => $this->validKey]),
            ['cancel_text' => $xssPayload]
        );

        Mail::assertSent(OrderCustomerCancelled::class, function (OrderCustomerCancelled $mail) use ($xssPayload) {
            $html = $mail->render();
            $this->assertStringNotContainsString($xssPayload, $html);
            $this->assertStringContainsString('&lt;img', $html);

            return true;
        });
    }

    // --- Key Tampering ---

    #[Test]
    public function invalid_encrypted_key_is_rejected(): void
    {
        $response = $this->post(
            route('submit.customer.cancel.order', ['uuid' => $this->order->uuid, 'key' => 'invalid-key']),
            ['cancel_text' => 'Test annulering']
        );

        $response->assertRedirect(route('home'));
        $this->order->refresh();
        $this->assertNotEquals(Order::STATUS_GEANNULEERD, $this->order->status);
    }

    #[Test]
    public function wrong_timestamp_key_is_rejected(): void
    {
        $wrongKey = encrypt(99999999);

        $response = $this->post(
            route('submit.customer.cancel.order', ['uuid' => $this->order->uuid, 'key' => $wrongKey]),
            ['cancel_text' => 'Test annulering']
        );

        $response->assertRedirect(route('home'));
        $this->order->refresh();
        $this->assertNotEquals(Order::STATUS_GEANNULEERD, $this->order->status);
    }

    #[Test]
    public function nonexistent_order_uuid_is_rejected(): void
    {
        $response = $this->post(
            route('submit.customer.cancel.order', ['uuid' => 'nonexistent-uuid', 'key' => $this->validKey]),
            ['cancel_text' => 'Test annulering']
        );

        $response->assertRedirect(route('home'));
    }

    // --- Input Validation ---

    #[Test]
    public function cancel_text_is_required(): void
    {
        $response = $this->post(
            route('submit.customer.cancel.order', ['uuid' => $this->order->uuid, 'key' => $this->validKey]),
            ['cancel_text' => '']
        );

        $response->assertSessionHasErrors('cancel_text');
        $this->order->refresh();
        $this->assertNotEquals(Order::STATUS_GEANNULEERD, $this->order->status);
    }

    #[Test]
    public function cancel_text_exceeding_max_length_is_rejected(): void
    {
        $response = $this->post(
            route('submit.customer.cancel.order', ['uuid' => $this->order->uuid, 'key' => $this->validKey]),
            ['cancel_text' => str_repeat('A', 1001)]
        );

        $response->assertSessionHasErrors('cancel_text');
        $this->order->refresh();
        $this->assertNotEquals(Order::STATUS_GEANNULEERD, $this->order->status);
    }

    #[Test]
    public function cancel_text_at_max_length_is_accepted(): void
    {
        Mail::fake();

        $response = $this->post(
            route('submit.customer.cancel.order', ['uuid' => $this->order->uuid, 'key' => $this->validKey]),
            ['cancel_text' => str_repeat('A', 1000)]
        );

        $response->assertOk();
        $this->order->refresh();
        $this->assertEquals(Order::STATUS_GEANNULEERD, $this->order->status);
    }

    // --- Double Cancellation ---

    #[Test]
    public function already_cancelled_order_shows_already_cancelled_page(): void
    {
        Mail::fake();

        // First cancellation
        $this->post(
            route('submit.customer.cancel.order', ['uuid' => $this->order->uuid, 'key' => $this->validKey]),
            ['cancel_text' => 'Eerste annulering']
        );

        // Second attempt
        $response = $this->post(
            route('submit.customer.cancel.order', ['uuid' => $this->order->uuid, 'key' => $this->validKey]),
            ['cancel_text' => 'Tweede poging']
        );

        $response->assertViewIs('customer.order.already-cancelled');

        // Should only have sent emails once
        Mail::assertSent(OrderCustomerCancelled::class, 1);
    }
}
