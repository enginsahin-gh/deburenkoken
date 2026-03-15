<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\Mail\Dac7RequiredMail;
use App\Mail\Dac7WarningMail;
use App\Models\Advert;
use App\Models\Cook;
use App\Models\Dish;
use App\Models\Order;
use App\Models\User;
use App\Services\Dac7Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Dac7ServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Dac7Service $dac7Service;

    protected User $cookUser;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate(Roles::CUSTOMER, 'web');
        Role::findOrCreate(Roles::COOK, 'web');
        Role::findOrCreate(Roles::ADMIN, 'web');

        $this->dac7Service = new Dac7Service;
        $this->cookUser = $this->createCookUser();
    }

    protected function createCookUser(): User
    {
        $user = User::factory()->create([
            'dac7_warning_email_sent' => false,
            'dac7_required_email_sent' => false,
        ]);
        $user->assignRole(Roles::COOK);
        Cook::factory()->create(['user_uuid' => $user->uuid]);

        return $user;
    }

    protected function createOrdersForUser(User $user, int $count, float $portionPrice = 10.00): void
    {
        $cook = $user->cook;

        for ($i = 0; $i < $count; $i++) {
            $dish = Dish::factory()->create([
                'user_uuid' => $user->uuid,
                'cook_uuid' => $cook->uuid,
                'portion_price' => $portionPrice,
            ]);

            $advert = Advert::factory()->published()->create([
                'dish_uuid' => $dish->uuid,
            ]);

            Order::factory()->paid()->create([
                'user_uuid' => $user->uuid,
                'dish_uuid' => $dish->uuid,
                'advert_uuid' => $advert->uuid,
                'portion_amount' => 1,
            ]);
        }
    }

    #[Test]
    public function warning_email_is_sent_to_cook_and_admin_when_warning_threshold_reached(): void
    {
        Mail::fake();

        $this->createOrdersForUser($this->cookUser, 15, 10.00);

        $this->dac7Service->checkUserDac7Thresholds($this->cookUser);

        Mail::assertSent(Dac7WarningMail::class, function ($mail) {
            return $mail->hasTo($this->cookUser->email)
                && $mail->hasCc(config('mail.admin.address'));
        });
    }

    #[Test]
    public function required_email_is_sent_to_cook_and_admin_when_required_threshold_reached(): void
    {
        Mail::fake();

        $this->createOrdersForUser($this->cookUser, 20, 10.00);

        $this->dac7Service->checkUserDac7Thresholds($this->cookUser);

        Mail::assertSent(Dac7RequiredMail::class, function ($mail) {
            return $mail->hasTo($this->cookUser->email)
                && $mail->hasCc(config('mail.admin.address'));
        });
    }

    #[Test]
    public function warning_email_is_sent_when_revenue_threshold_reached(): void
    {
        Mail::fake();

        $this->createOrdersForUser($this->cookUser, 10, 150.00);

        $this->dac7Service->checkUserDac7Thresholds($this->cookUser);

        Mail::assertSent(Dac7WarningMail::class, function ($mail) {
            return $mail->hasTo($this->cookUser->email)
                && $mail->hasCc(config('mail.admin.address'));
        });
    }

    #[Test]
    public function required_email_is_sent_when_revenue_threshold_reached(): void
    {
        Mail::fake();

        $this->createOrdersForUser($this->cookUser, 10, 200.00);

        $this->dac7Service->checkUserDac7Thresholds($this->cookUser);

        Mail::assertSent(Dac7RequiredMail::class, function ($mail) {
            return $mail->hasTo($this->cookUser->email)
                && $mail->hasCc(config('mail.admin.address'));
        });
    }

    #[Test]
    public function no_email_sent_when_below_threshold(): void
    {
        Mail::fake();

        $this->createOrdersForUser($this->cookUser, 5, 10.00);

        $this->dac7Service->checkUserDac7Thresholds($this->cookUser);

        Mail::assertNotSent(Dac7WarningMail::class);
        Mail::assertNotSent(Dac7RequiredMail::class);
    }

    #[Test]
    public function warning_email_not_sent_twice(): void
    {
        Mail::fake();

        $this->createOrdersForUser($this->cookUser, 15, 10.00);

        $this->dac7Service->checkUserDac7Thresholds($this->cookUser);

        Mail::assertSent(Dac7WarningMail::class, 1);

        $refreshedUser = User::with(['orders.advert.dish', 'cook'])->find($this->cookUser->uuid);
        $this->dac7Service->checkUserDac7Thresholds($refreshedUser);

        Mail::assertSent(Dac7WarningMail::class, 1);
    }

    #[Test]
    public function required_email_not_sent_twice(): void
    {
        Mail::fake();

        $this->createOrdersForUser($this->cookUser, 20, 10.00);

        $this->dac7Service->checkUserDac7Thresholds($this->cookUser);

        Mail::assertSent(Dac7RequiredMail::class, 1);

        $refreshedUser = User::with(['orders.advert.dish', 'cook'])->find($this->cookUser->uuid);
        $this->dac7Service->checkUserDac7Thresholds($refreshedUser);

        Mail::assertSent(Dac7RequiredMail::class, 1);
    }

    #[Test]
    public function non_cook_user_does_not_receive_dac7_emails(): void
    {
        Mail::fake();

        $customerUser = User::factory()->create([
            'dac7_warning_email_sent' => false,
            'dac7_required_email_sent' => false,
        ]);
        $customerUser->assignRole(Roles::CUSTOMER);

        $this->dac7Service->checkUserDac7Thresholds($customerUser);

        Mail::assertNotSent(Dac7WarningMail::class);
        Mail::assertNotSent(Dac7RequiredMail::class);
    }

    #[Test]
    public function admin_email_is_configured_correctly(): void
    {
        $adminEmail = config('mail.admin.address');

        $this->assertNotEmpty($adminEmail);
        $this->assertEquals('admin@deburenkoken.nl', $adminEmail);
    }

    #[Test]
    public function both_warning_and_required_emails_sent_when_required_threshold_reached_from_zero(): void
    {
        Mail::fake();

        $this->createOrdersForUser($this->cookUser, 20, 10.00);

        $this->dac7Service->checkUserDac7Thresholds($this->cookUser);

        Mail::assertSent(Dac7WarningMail::class, function ($mail) {
            return $mail->hasTo($this->cookUser->email)
                && $mail->hasCc(config('mail.admin.address'));
        });

        Mail::assertSent(Dac7RequiredMail::class, function ($mail) {
            return $mail->hasTo($this->cookUser->email)
                && $mail->hasCc(config('mail.admin.address'));
        });
    }
}
