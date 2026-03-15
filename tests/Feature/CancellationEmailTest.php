<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Mail\OrderCancelCustomer;
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

class CancellationEmailTest extends TestCase
{
    use RefreshDatabase;

    private User $cookUser;

    private Cook $cook;

    private Order $order;

    private Client $client;

    private Dish $dish;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate(Roles::CUSTOMER, 'web');
        Role::findOrCreate(Roles::COOK, 'web');
        Role::findOrCreate(Roles::ADMIN, 'web');

        // Create cook user with verified email
        $this->cookUser = User::factory()->create();
        $this->cookUser->assignRole(Roles::COOK);

        // Create user profile (needed for email templates)
        UserProfile::create([
            'user_uuid' => $this->cookUser->uuid,
            'firstname' => 'Test',
            'lastname' => 'Kok',
            'phone_number' => '0612345678',
            'birthday' => now()->subYears(30),
        ]);

        // Create cook with mail_cancel enabled
        $this->cook = Cook::factory()->create(['user_uuid' => $this->cookUser->uuid]);

        // Create client
        $this->client = Client::factory()->create();

        // Create dish linked to the cook
        $this->dish = Dish::factory()->create([
            'user_uuid' => $this->cookUser->uuid,
            'cook_uuid' => $this->cook->uuid,
        ]);

        // Create advert linked to the dish
        $advert = Advert::factory()->create(['dish_uuid' => $this->dish->uuid]);

        // Create order
        $this->order = Order::factory()->paid()->create([
            'user_uuid' => $this->cookUser->uuid,
            'client_uuid' => $this->client->uuid,
            'dish_uuid' => $this->dish->uuid,
            'advert_uuid' => $advert->uuid,
        ]);
    }

    #[Test]
    public function order_cook_cancelled_mailable_contains_cancel_message(): void
    {
        $cancelMessage = 'Ik kan helaas niet meer komen ophalen.';

        $mailable = new OrderCookCancelled(
            $this->cookUser,
            $this->order,
            $cancelMessage
        );

        $mailable->assertSeeInHtml($cancelMessage);
        $mailable->assertSeeInHtml('Annuleringsbericht');
    }

    #[Test]
    public function order_customer_cancelled_mailable_contains_cancel_message(): void
    {
        $cancelMessage = 'Bestelling geannuleerd wegens overmacht.';

        $mailable = new OrderCustomerCancelled(
            $this->cookUser,
            $this->order,
            $cancelMessage
        );

        $mailable->assertSeeInHtml($cancelMessage);
        $mailable->assertSeeInHtml('Annuleringsbericht');
    }

    #[Test]
    public function order_cancel_customer_mailable_contains_cancel_text(): void
    {
        $cancelText = 'Helaas moet ik de bestelling annuleren.';

        $mailable = new OrderCancelCustomer(
            $this->cookUser,
            $this->order,
            $this->dish,
            $cancelText,
            $this->client
        );

        $mailable->assertSeeInHtml($cancelText);
        $mailable->assertSeeInHtml('Boodschap van de Thuiskok');
    }

    #[Test]
    public function cook_cancelled_email_renders_with_order_details(): void
    {
        $cancelMessage = 'Test annuleringsopmerking voor de kok.';

        $mailable = new OrderCookCancelled(
            $this->cookUser,
            $this->order,
            $cancelMessage
        );

        $html = $mailable->render();

        $this->assertStringContainsString($cancelMessage, $html);
        $this->assertStringContainsString($this->client->getName(), $html);
        $this->assertStringContainsString($this->dish->getTitle(), $html);
    }

    #[Test]
    public function customer_cancelled_email_renders_with_order_details(): void
    {
        $cancelMessage = 'Test annuleringsopmerking voor de klant.';

        $mailable = new OrderCustomerCancelled(
            $this->cookUser,
            $this->order,
            $cancelMessage
        );

        $html = $mailable->render();

        $this->assertStringContainsString($cancelMessage, $html);
        $this->assertStringContainsString($this->cookUser->getUsername(), $html);
        $this->assertStringContainsString($this->dish->getTitle(), $html);
    }

    #[Test]
    public function no_payment_id_path_sends_cancellation_emails(): void
    {
        Mail::fake();

        // Create order without payment_id
        $advert = Advert::factory()->create(['dish_uuid' => $this->dish->uuid]);
        $order = Order::factory()->paid()->create([
            'user_uuid' => $this->cookUser->uuid,
            'client_uuid' => $this->client->uuid,
            'dish_uuid' => $this->dish->uuid,
            'advert_uuid' => $advert->uuid,
            'payment_id' => null,
        ]);

        $key = encrypt($order->client->getCreatedAt()->unix());
        $cancelText = 'Ik wil mijn bestelling annuleren.';

        $response = $this->post(
            route('submit.customer.cancel.order', ['uuid' => $order->uuid, 'key' => $key]),
            ['cancel_text' => $cancelText]
        );

        $response->assertOk();
        $response->assertViewIs('customer.order.cancel-complete');

        // Verify order was cancelled
        $order->refresh();
        $this->assertEquals(Order::STATUS_GEANNULEERD, $order->status);
        $this->assertEquals(Order::CANCELLED_BY_CLIENT, $order->cancelled_by);

        // Verify emails were sent
        Mail::assertSent(OrderCookCancelled::class, function (OrderCookCancelled $mail) {
            return $mail->hasTo($this->cookUser->getEmail());
        });

        Mail::assertSent(OrderCustomerCancelled::class, function (OrderCustomerCancelled $mail) {
            return $mail->hasTo($this->client->getEmail());
        });
    }

    #[Test]
    public function no_payment_id_path_skips_cook_email_when_mail_cancel_disabled(): void
    {
        Mail::fake();

        // Disable mail_cancel for the cook
        $this->cook->update(['mail_cancel' => false]);

        // Create order without payment_id
        $advert = Advert::factory()->create(['dish_uuid' => $this->dish->uuid]);
        $order = Order::factory()->paid()->create([
            'user_uuid' => $this->cookUser->uuid,
            'client_uuid' => $this->client->uuid,
            'dish_uuid' => $this->dish->uuid,
            'advert_uuid' => $advert->uuid,
            'payment_id' => null,
        ]);

        $key = encrypt($order->client->getCreatedAt()->unix());

        $this->post(
            route('submit.customer.cancel.order', ['uuid' => $order->uuid, 'key' => $key]),
            ['cancel_text' => 'Annulering test']
        );

        // Cook email should NOT be sent (mail_cancel is disabled)
        Mail::assertNotSent(OrderCookCancelled::class);

        // Customer email should still be sent
        Mail::assertSent(OrderCustomerCancelled::class);
    }

    #[Test]
    public function no_payment_id_path_skips_cook_email_when_email_not_verified(): void
    {
        Mail::fake();

        // Set email as unverified
        $this->cookUser->update(['email_verified_at' => null]);

        // Create order without payment_id
        $advert = Advert::factory()->create(['dish_uuid' => $this->dish->uuid]);
        $order = Order::factory()->paid()->create([
            'user_uuid' => $this->cookUser->uuid,
            'client_uuid' => $this->client->uuid,
            'dish_uuid' => $this->dish->uuid,
            'advert_uuid' => $advert->uuid,
            'payment_id' => null,
        ]);

        $key = encrypt($order->client->getCreatedAt()->unix());

        $this->post(
            route('submit.customer.cancel.order', ['uuid' => $order->uuid, 'key' => $key]),
            ['cancel_text' => 'Annulering test']
        );

        // Cook email should NOT be sent (email not verified)
        Mail::assertNotSent(OrderCookCancelled::class);

        // Customer email should still be sent
        Mail::assertSent(OrderCustomerCancelled::class);
    }
}
