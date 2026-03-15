<?php

namespace Tests\Browser;

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
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class CancellationEmailDuskTest extends DuskTestCase
{
    private User $user;

    private Client $client;

    private Dish $dish;

    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure MAIL_FROM_ADDRESS is set (may be null in CI)
        if (empty(env('MAIL_FROM_ADDRESS'))) {
            putenv('MAIL_FROM_ADDRESS=test@deburenkoken.nl');
            putenv('MAIL_FROM_NAME=DeBurenKoken Test');
        }

        $this->user = User::factory()->create();
        UserProfile::create([
            'user_uuid' => $this->user->uuid,
            'firstname' => 'Test',
            'lastname' => 'Kok',
            'phone_number' => '0612345678',
            'birthday' => now()->subYears(30),
        ]);
        Cook::factory()->create(['user_uuid' => $this->user->uuid]);
        $this->client = Client::factory()->create();
        $this->dish = Dish::factory()->create([
            'user_uuid' => $this->user->uuid,
            'cook_uuid' => $this->user->cook->uuid,
        ]);
        $advert = Advert::factory()->create(['dish_uuid' => $this->dish->uuid]);
        $this->order = Order::factory()->cancelledByClient()->create([
            'user_uuid' => $this->user->uuid,
            'client_uuid' => $this->client->uuid,
            'dish_uuid' => $this->dish->uuid,
            'advert_uuid' => $advert->uuid,
        ]);
    }

    #[Test]
    public function cook_cancelled_email_renders_cancel_message(): void
    {
        $this->browse(function (Browser $browser) {
            $cancelMessage = 'De klant kon helaas niet meer ophalen vanwege priveredenen.';

            $mailable = new OrderCookCancelled($this->user, $this->order, $cancelMessage);
            $html = $mailable->render();

            $filename = 'temp-cook-cancelled-email-'.uniqid().'.html';
            $tempPath = public_path($filename);
            file_put_contents($tempPath, $html);

            try {
                $browser->visit('/'.$filename)
                    ->assertSee('Annuleringsbericht')
                    ->assertSee($cancelMessage)
                    ->assertSee($this->client->getName())
                    ->assertSee($this->dish->getTitle())
                    ->screenshot('BL-185-cook-cancelled-email');
            } finally {
                @unlink($tempPath);
            }
        });
    }

    #[Test]
    public function customer_cancelled_email_renders_cancel_message(): void
    {
        $this->browse(function (Browser $browser) {
            $cancelMessage = 'Bestelling geannuleerd wegens overmacht.';

            $mailable = new OrderCustomerCancelled($this->user, $this->order, $cancelMessage);
            $html = $mailable->render();

            $filename = 'temp-customer-cancelled-email-'.uniqid().'.html';
            $tempPath = public_path($filename);
            file_put_contents($tempPath, $html);

            try {
                $browser->visit('/'.$filename)
                    ->assertSee('Annuleringsbericht')
                    ->assertSee($cancelMessage)
                    ->assertSee($this->user->getUsername())
                    ->assertSee($this->dish->getTitle())
                    ->screenshot('BL-185-customer-cancelled-email');
            } finally {
                @unlink($tempPath);
            }
        });
    }

    #[Test]
    public function cook_cancel_customer_email_renders_cancel_text(): void
    {
        // This test uses cancelledByCook state, so create a separate order
        $order = Order::factory()->cancelledByCook()->create([
            'user_uuid' => $this->user->uuid,
            'client_uuid' => $this->client->uuid,
            'dish_uuid' => $this->dish->uuid,
            'advert_uuid' => $this->order->advert_uuid,
        ]);

        $this->browse(function (Browser $browser) use ($order) {
            $cancelText = 'Helaas kan ik de maaltijd niet meer bereiden.';

            $mailable = new OrderCancelCustomer($this->user, $order, $this->dish, $cancelText, $this->client);
            $html = $mailable->render();

            $filename = 'temp-cook-cancel-customer-email-'.uniqid().'.html';
            $tempPath = public_path($filename);
            file_put_contents($tempPath, $html);

            try {
                $browser->visit('/'.$filename)
                    ->assertSee('Boodschap van de Thuiskok')
                    ->assertSee($cancelText)
                    ->assertSee($this->client->getName())
                    ->assertSee($this->dish->getTitle())
                    ->screenshot('BL-185-cook-cancel-customer-email');
            } finally {
                @unlink($tempPath);
            }
        });
    }
}
