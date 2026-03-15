<?php

namespace App\Console\Commands;

use App\Models\Advert;
use App\Models\Order;
use App\Models\Wallet;
use App\Models\WalletLine;
use App\Repositories\WalletRepository;
use App\Services\Dac7Service;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateOrderStatusesCommand extends Command
{
    protected $signature = 'orders:update-statuses';

    protected $description = 'Update statuses for expired orders and recalculate wallet balances';

    protected $walletRepository;

    public function __construct(WalletRepository $walletRepository)
    {
        parent::__construct();
        $this->walletRepository = $walletRepository;
    }

    public function handle()
    {
        $this->info('Starting order status updates...');

        try {
            // Begin transaction for atomicity
            DB::beginTransaction();

            // Get all active adverts
            $adverts = Advert::whereNull('deleted_at')->get();
            $updatedOrders = 0;
            $updatedWalletLines = 0;
            $updatedWallets = 0;

            $now = Carbon::now();

            foreach ($adverts as $advert) {
                // 1. Update orders if pickup time has passed
                if ($advert->getParsedPickupTo()->isPast()) {
                    // Update all active orders to EXPIRED if pickup period has passed
                    $affectedOrders = $advert->order()
                        ->where('status', Order::STATUS_ACTIEF)
                        ->where('payment_state', Order::SUCCEED)
                        ->update(['status' => Order::STATUS_VERLOPEN]);

                    $updatedOrders += $affectedOrders;

                    // Get all orders that were just marked as expired
                    $expiredOrders = $advert->order()
                        ->where('status', Order::STATUS_VERLOPEN)
                        ->where('payment_state', Order::SUCCEED)
                        ->get();

                    foreach ($expiredOrders as $order) {
                        // Update associated wallet lines
                        $walletLine = WalletLine::where('order_uuid', $order->getUuid())
                            ->where('state', WalletLine::PROCESSING)
                            ->first();

                        if ($walletLine) {
                            $walletLine->update(['state' => WalletLine::AVAILABLE]);
                            $updatedWalletLines++;

                            // Recalculate wallet totals for the user
                            if ($wallet = $walletLine->wallet) {
                                $userUuid = $wallet->user_uuid;

                                // Recalculate wallet balances
                                $this->walletRepository->countAvailableWalletLinesForUser($userUuid);
                                $updatedWallets++;
                            }

                            // Check DAC7 thresholds after wallet updates
                            if ($order->user) {
                                try {
                                    app(Dac7Service::class)->checkUserDac7Thresholds($order->user);
                                } catch (\Exception $e) {
                                    Log::warning("Error checking DAC7 thresholds: {$e->getMessage()}");
                                }
                            }
                        }
                    }
                }

                // 2. Update expired payments (timed out)
                if ($advert->getParsedOrderTo()->isPast()) {
                    $affectedTimeouts = $advert->order()
                        ->where('status', Order::STATUS_ACTIEF)
                        ->where('payment_state', Order::IN_PROCESS)
                        ->where('created_at', '<', $now->copy()->subMinutes(15))
                        ->update(['payment_state' => Order::FAILED]);

                    $updatedOrders += $affectedTimeouts;
                }
            }

            // 3. Fix any inconsistent wallet balances
            $wallets = Wallet::all();
            foreach ($wallets as $wallet) {
                $userUuid = $wallet->user_uuid;
                $available = $this->walletRepository->calculateTotalAvailable($userUuid);
                $processing = $this->walletRepository->calculateTotalProcessing($userUuid);

                // Only update if there's a discrepancy
                if (abs($wallet->total_available - $available) > 0.001 || abs($wallet->total_processing - $processing) > 0.001) {
                    $wallet->update([
                        'total_available' => $available,
                        'total_processing' => $processing,
                    ]);
                    $updatedWallets++;
                }

                // Also check for PAYOUT_INITIATED with non-zero available balance
                $payoutInitiatedCount = WalletLine::where('wallet_uuid', $wallet->uuid)
                    ->where('state', WalletLine::PAYOUT_INITIATED)
                    ->count();

                if ($payoutInitiatedCount > 0 && $wallet->total_available > 0) {
                    $wallet->update(['total_available' => 0]);
                    $updatedWallets++;
                }
            }

            DB::commit();

            Log::info("Order status update completed: processed orders: {$updatedOrders}, wallet lines: {$updatedWalletLines}, wallets: {$updatedWallets}");

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update order statuses: '.$e->getMessage());

            return 1;
        }
    }
}
