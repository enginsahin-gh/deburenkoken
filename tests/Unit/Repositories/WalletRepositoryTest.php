<?php

namespace Tests\Unit\Repositories;

use App\Dtos\WalletLineDto;
use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletLine;
use App\Repositories\WalletRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WalletRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private WalletRepository $walletRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->walletRepository = app(WalletRepository::class);
    }

    #[Test]
    public function it_can_create_wallet_for_user(): void
    {
        $user = User::factory()->create();

        $wallet = $this->walletRepository->createWalletForUser($user);

        $this->assertInstanceOf(Wallet::class, $wallet);
        $this->assertEquals($user->getUuid(), $wallet->getUserUuid());
        $this->assertEquals(0, $wallet->getTotalAvailable());
        $this->assertEquals(0, $wallet->getTotalProcessing());
        $this->assertEquals(Wallet::FULL, $wallet->getState());
    }

    #[Test]
    public function it_can_find_wallet_for_user(): void
    {
        $user = User::factory()->create();
        $wallet = $this->walletRepository->createWalletForUser($user);

        $foundWallet = $this->walletRepository->findWalletForUser($user->getUuid());

        $this->assertNotNull($foundWallet);
        $this->assertEquals($wallet->getUuid(), $foundWallet->getUuid());
    }

    #[Test]
    public function it_returns_null_when_wallet_not_found(): void
    {
        $result = $this->walletRepository->findWalletForUser('non-existent-uuid');

        $this->assertNull($result);
    }

    #[Test]
    public function it_can_add_wallet_line(): void
    {
        $user = User::factory()->create();
        $wallet = $this->walletRepository->createWalletForUser($user);
        $order = Order::factory()->create();

        $walletLineDto = new WalletLineDto(
            $wallet->getUuid(),
            $order->getUuid(),
            25.50,
            WalletLine::ON_HOLD
        );

        $walletLine = $this->walletRepository->addWalletLine($walletLineDto);

        $this->assertInstanceOf(WalletLine::class, $walletLine);
        $this->assertEquals($wallet->getUuid(), $walletLine->getWalletUuid());
        $this->assertEquals(25.50, $walletLine->getAmount());
        $this->assertEquals(WalletLine::ON_HOLD, $walletLine->getState());
    }

    #[Test]
    public function it_prevents_duplicate_wallet_lines_for_same_order(): void
    {
        $user = User::factory()->create();
        $wallet = $this->walletRepository->createWalletForUser($user);
        $order = Order::factory()->create();

        $walletLineDto = new WalletLineDto(
            $wallet->getUuid(),
            $order->getUuid(),
            25.50,
            WalletLine::ON_HOLD
        );

        $firstWalletLine = $this->walletRepository->addWalletLine($walletLineDto);
        $secondWalletLine = $this->walletRepository->addWalletLine($walletLineDto);

        // Should return the same wallet line, not create a duplicate
        $this->assertEquals($firstWalletLine->getUuid(), $secondWalletLine->getUuid());
        $this->assertEquals(1, WalletLine::where('order_uuid', $order->getUuid())->count());
    }

    #[Test]
    public function it_can_update_wallet_line_state(): void
    {
        $user = User::factory()->create();
        $wallet = $this->walletRepository->createWalletForUser($user);
        $order = Order::factory()->create();

        $walletLineDto = new WalletLineDto(
            $wallet->getUuid(),
            $order->getUuid(),
            25.50,
            WalletLine::ON_HOLD
        );

        $walletLine = $this->walletRepository->addWalletLine($walletLineDto);

        $updatedWalletLine = $this->walletRepository->setState($walletLine, WalletLine::AVAILABLE);

        $this->assertEquals(WalletLine::AVAILABLE, $updatedWalletLine->getState());
    }

    #[Test]
    public function it_calculates_total_available_correctly(): void
    {
        $user = User::factory()->create();
        $wallet = $this->walletRepository->createWalletForUser($user);

        // Add available wallet lines
        $order1 = Order::factory()->create();
        $this->walletRepository->addWalletLine(new WalletLineDto(
            $wallet->getUuid(),
            $order1->getUuid(),
            10.00,
            WalletLine::AVAILABLE
        ));

        $order2 = Order::factory()->create();
        $this->walletRepository->addWalletLine(new WalletLineDto(
            $wallet->getUuid(),
            $order2->getUuid(),
            15.00,
            WalletLine::AVAILABLE
        ));

        // Add processing wallet line (should not be included)
        $order3 = Order::factory()->create();
        $this->walletRepository->addWalletLine(new WalletLineDto(
            $wallet->getUuid(),
            $order3->getUuid(),
            20.00,
            WalletLine::PROCESSING
        ));

        $totalAvailable = $this->walletRepository->calculateTotalAvailable($user->getUuid());

        $this->assertEquals(25.00, $totalAvailable);
    }

    #[Test]
    public function it_calculates_total_processing_correctly(): void
    {
        $user = User::factory()->create();
        $wallet = $this->walletRepository->createWalletForUser($user);

        // Add processing wallet lines
        $order1 = Order::factory()->create();
        $this->walletRepository->addWalletLine(new WalletLineDto(
            $wallet->getUuid(),
            $order1->getUuid(),
            10.00,
            WalletLine::PROCESSING
        ));

        $order2 = Order::factory()->create();
        $this->walletRepository->addWalletLine(new WalletLineDto(
            $wallet->getUuid(),
            $order2->getUuid(),
            15.00,
            WalletLine::ON_HOLD
        ));

        // Add available wallet line (should not be included)
        $order3 = Order::factory()->create();
        $this->walletRepository->addWalletLine(new WalletLineDto(
            $wallet->getUuid(),
            $order3->getUuid(),
            20.00,
            WalletLine::AVAILABLE
        ));

        $totalProcessing = $this->walletRepository->calculateTotalProcessing($user->getUuid());

        $this->assertEquals(25.00, $totalProcessing);
    }

    #[Test]
    public function it_applies_cancellation_fee_correctly(): void
    {
        $user = User::factory()->create();
        $wallet = $this->walletRepository->createWalletForUser($user);
        $orderForCancel = Order::factory()->create();

        // Add available balance first
        $orderAvail = Order::factory()->create();
        $this->walletRepository->addWalletLine(new WalletLineDto(
            $wallet->getUuid(),
            $orderAvail->getUuid(),
            50.00,
            WalletLine::AVAILABLE
        ));

        $result = $this->walletRepository->applyAnnulationFee($user->getUuid(), $orderForCancel->getUuid(), 0.60);

        $this->assertTrue($result);

        // Verify cancellation cost wallet line was created
        $cancellationLine = WalletLine::where('order_uuid', $orderForCancel->getUuid())
            ->where('state', WalletLine::CANCELLATION_COST)
            ->first();

        $this->assertNotNull($cancellationLine);
        $this->assertEquals(-0.60, $cancellationLine->getAmount());
    }

    #[Test]
    public function it_calculates_total_with_cancellation_costs(): void
    {
        $user = User::factory()->create();
        $wallet = $this->walletRepository->createWalletForUser($user);

        // Add available balance
        $order1 = Order::factory()->create();
        $this->walletRepository->addWalletLine(new WalletLineDto(
            $wallet->getUuid(),
            $order1->getUuid(),
            50.00,
            WalletLine::AVAILABLE
        ));

        // Apply cancellation fee
        $orderCancel = Order::factory()->create();
        $this->walletRepository->applyAnnulationFee($user->getUuid(), $orderCancel->getUuid(), 0.60);

        $totalAvailable = $this->walletRepository->calculateTotalAvailable($user->getUuid());

        // 50.00 - 0.60 = 49.40
        $this->assertEquals(49.40, $totalAvailable);
    }

    #[Test]
    public function it_can_find_wallet_line_by_order_uuid(): void
    {
        $user = User::factory()->create();
        $wallet = $this->walletRepository->createWalletForUser($user);
        $order = Order::factory()->create();

        $this->walletRepository->addWalletLine(new WalletLineDto(
            $wallet->getUuid(),
            $order->getUuid(),
            25.50,
            WalletLine::ON_HOLD
        ));

        $foundWalletLine = $this->walletRepository->findWalletLineByOrderUuid($order->getUuid());

        $this->assertNotNull($foundWalletLine);
        $this->assertEquals($order->getUuid(), $foundWalletLine->getOrderUuid());
    }

    #[Test]
    public function it_updates_wallet_lines_for_payout(): void
    {
        $user = User::factory()->create();
        $wallet = $this->walletRepository->createWalletForUser($user);

        // Add available wallet lines
        $order1 = Order::factory()->create();
        $this->walletRepository->addWalletLine(new WalletLineDto(
            $wallet->getUuid(),
            $order1->getUuid(),
            10.00,
            WalletLine::AVAILABLE
        ));

        $order2 = Order::factory()->create();
        $this->walletRepository->addWalletLine(new WalletLineDto(
            $wallet->getUuid(),
            $order2->getUuid(),
            15.00,
            WalletLine::AVAILABLE
        ));

        $this->walletRepository->updateWalletLinesForPayout($wallet->getUuid());

        // All available lines should now be paid out
        $availableCount = WalletLine::where('wallet_uuid', $wallet->getUuid())
            ->where('state', WalletLine::AVAILABLE)
            ->count();

        $paidOutCount = WalletLine::where('wallet_uuid', $wallet->getUuid())
            ->where('state', WalletLine::PAID_OUT)
            ->count();

        $this->assertEquals(0, $availableCount);
        $this->assertEquals(2, $paidOutCount);
    }

    #[Test]
    public function it_can_set_wallet_to_full_state(): void
    {
        $user = User::factory()->create();
        $wallet = Wallet::create([
            'user_uuid' => $user->getUuid(),
            'total_available' => 0,
            'total_processing' => 0,
            'total_paid' => 0,
            'state' => Wallet::LIMITED,
        ]);

        $updatedWallet = $this->walletRepository->fullWalletState($wallet->getUuid());

        $this->assertEquals(Wallet::FULL, $updatedWallet->getState());
    }
}
