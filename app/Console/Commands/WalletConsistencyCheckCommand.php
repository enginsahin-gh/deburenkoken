<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Wallet;
use App\Models\WalletLine;
use App\Repositories\WalletRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletConsistencyCheckCommand extends Command
{
    protected $signature = 'wallet:consistency-check';

    protected $description = 'Check and fix wallet balance inconsistencies';

    protected $walletRepository;

    public function __construct(WalletRepository $walletRepository)
    {
        parent::__construct();
        $this->walletRepository = $walletRepository;
    }

    public function handle()
    {
        $this->info('Starting wallet consistency check...');

        $wallets = Wallet::all();
        $fixedWallets = 0;
        $fixedWalletLines = 0;

        foreach ($wallets as $wallet) {
            DB::beginTransaction();

            try {
                $userUuid = $wallet->user_uuid;

                // 1. Check for wallet lines with state=PROCESSING but with expired orders
                $processingWalletLines = WalletLine::where('wallet_uuid', $wallet->uuid)
                    ->where('state', WalletLine::PROCESSING)
                    ->whereNotNull('order_uuid')
                    ->with('order.advert')
                    ->get();

                foreach ($processingWalletLines as $walletLine) {
                    if ($walletLine->order &&
                        $walletLine->order->advert &&
                        $walletLine->order->advert->getParsedPickupTo()->isPast()) {

                        // Update order status if needed
                        if ($walletLine->order->status !== Order::STATUS_VERLOPEN) {
                            $walletLine->order->status = Order::STATUS_VERLOPEN;
                            $walletLine->order->save();
                        }

                        // Update wallet line state
                        $walletLine->state = WalletLine::AVAILABLE;
                        $walletLine->save();
                        $fixedWalletLines++;
                    }
                }

                // 2. Recalculate wallet balances
                $availableAmount = $this->walletRepository->calculateTotalAvailable($userUuid);
                $processingAmount = $this->walletRepository->calculateTotalProcessing($userUuid);

                // Only update if there's a discrepancy
                if (abs($wallet->total_available - $availableAmount) > 0.001 ||
                    abs($wallet->total_processing - $processingAmount) > 0.001) {

                    $wallet->update([
                        'total_available' => $availableAmount,
                        'total_processing' => $processingAmount,
                    ]);
                    $fixedWallets++;
                }

                // 3. Check for PAYOUT_INITIATED with non-zero available balance
                $payoutInitiatedCount = WalletLine::where('wallet_uuid', $wallet->uuid)
                    ->where('state', WalletLine::PAYOUT_INITIATED)
                    ->count();

                if ($payoutInitiatedCount > 0 && $wallet->total_available > 0) {
                    $wallet->update(['total_available' => 0]);
                    $fixedWallets++;
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Error checking wallet {$wallet->uuid}: {$e->getMessage()}");
            }
        }

        Log::info("Wallet consistency check completed: fixed {$fixedWallets} wallet balances and {$fixedWalletLines} wallet lines");

        return 0;
    }
}
