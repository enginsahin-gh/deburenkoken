<?php

namespace Tests\Feature;

use App\Mail\DailyAdminReport;
use App\Models\Banking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class SendDailyAdminReportTest extends TestCase
{
    use RefreshDatabase;

    private function createPayment(User $user, float $amount, int $state = Payment::INITIATED): Payment
    {
        $banking = Banking::create([
            'user_uuid' => $user->uuid,
            'account_holder' => 'Test Account',
            'iban' => 'NL91ABNA0417164300',
        ]);

        return Payment::create([
            'uuid' => Uuid::uuid4()->toString(),
            'user_uuid' => $user->uuid,
            'banking_uuid' => $banking->uuid,
            'amount' => $amount,
            'state' => $state,
            'payment_type' => 'automatic',
        ]);
    }

    #[Test]
    public function daily_report_email_shows_correct_payout_amount_after_five_percent_fee(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $this->createPayment($user, 100.00);

        $this->artisan('admin:daily-report')
            ->assertSuccessful();

        Mail::assertSent(DailyAdminReport::class, function (DailyAdminReport $mail) {
            $html = $mail->render();

            // Correct: 100 * 0.95 = 95.00 (formatted as 95,00 in Dutch)
            $this->assertStringContainsString('95,00', $html);

            // Incorrect (double deduction): 100 * 0.95 * 0.95 = 90.25
            $this->assertStringNotContainsString('90,25', $html);

            return true;
        });
    }

    #[Test]
    public function daily_report_email_shows_correct_amounts_for_multiple_payouts(): void
    {
        Mail::fake();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->createPayment($user1, 46.50);
        $this->createPayment($user2, 12.00);

        $this->artisan('admin:daily-report')
            ->assertSuccessful();

        Mail::assertSent(DailyAdminReport::class, function (DailyAdminReport $mail) {
            $html = $mail->render();

            // 46.50 * 0.95 = 44.175 -> 44,18 (Dutch formatting)
            $this->assertStringContainsString('44,18', $html);

            // 12.00 * 0.95 = 11.40 -> 11,40
            $this->assertStringContainsString('11,40', $html);

            return true;
        });
    }

    #[Test]
    public function daily_report_email_shows_no_payouts_message_when_none_exist(): void
    {
        Mail::fake();

        $this->artisan('admin:daily-report')
            ->assertSuccessful();

        Mail::assertSent(DailyAdminReport::class, function (DailyAdminReport $mail) {
            $html = $mail->render();

            $this->assertStringContainsString(
                'Er zijn geen uitbetalingsaanvragen',
                $html
            );

            return true;
        });
    }

    #[Test]
    public function daily_report_excludes_non_initiated_payouts(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $this->createPayment($user, 200.00, Payment::COMPLETED);

        $this->artisan('admin:daily-report')
            ->assertSuccessful();

        Mail::assertSent(DailyAdminReport::class, function (DailyAdminReport $mail) {
            $html = $mail->render();

            // 200 * 0.95 = 190.00 should NOT appear
            $this->assertStringNotContainsString('190,00', $html);
            $this->assertStringContainsString(
                'Er zijn geen uitbetalingsaanvragen',
                $html
            );

            return true;
        });
    }
}
