<?php

namespace App\Repositories;

use App\Dtos\WalletDto;
use App\Dtos\WalletLineDto;
use App\Models\Payment;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletLine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletRepository
{
    private Wallet $wallet;

    private WalletLine $walletLine;

    public function __construct(
        Wallet $wallet,
        WalletLine $walletLine
    ) {
        $this->wallet = $wallet;
        $this->walletLine = $walletLine;
    }

    public function findWallet(string $walletUuid): ?Wallet
    {
        return $this->wallet->find($walletUuid);
    }

    public function findWalletForUser(string $userUuid): ?Wallet
    {
        return $this->wallet
            ->where('user_uuid', $userUuid)
            ->with('walletLines')
            ->first();
    }

    public function createWalletForUser(User $user): Wallet
    {
        return $this->wallet->create([
            'user_uuid' => $user->getUuid(),
            'total_available' => 0,
            'total_processing' => 0,
            'total_paid' => 0,
            'state' => Wallet::FULL,
        ]);
    }

    public function fullWalletState(string $walletUuid): ?Wallet
    {
        $wallet = $this->wallet->find($walletUuid);

        if (is_null($wallet)) {
            return null;
        }

        $wallet->update(['state' => Wallet::FULL]);

        return $wallet;
    }

    public function addWalletLine(WalletLineDto $walletLineDto): WalletLine
    {
        // Gebruik een transactie om atomiciteit te garanderen
        DB::beginTransaction();

        try {
            // Check voor duplicaten - alleen voor echte order UUIDs, niet voor payment IDs
            if (! str_starts_with($walletLineDto->getOrderUuid(), 'payment-')) {
                $existingLine = $this->walletLine
                    ->where('wallet_uuid', $walletLineDto->getWalletUuid())
                    ->where('order_uuid', $walletLineDto->getOrderUuid())
                    ->first();

                if ($existingLine) {
                    // Als er al een wallet line bestaat voor deze bestelling, gebruik die
                    \Log::info('Voorkomen dubbele wallet line voor order: '.$walletLineDto->getOrderUuid());
                    DB::commit();

                    return $existingLine;
                }
            }

            // Create new wallet line
            $walletLine = $this->walletLine->create([
                'wallet_uuid' => $walletLineDto->getWalletUuid(),
                'order_uuid' => $walletLineDto->getOrderUuid(),
                'amount' => $walletLineDto->getAmount(),
                'state' => $walletLineDto->getState(),
            ]);

            // Update wallet totalen
            $wallet = $this->findWallet($walletLineDto->getWalletUuid());
            if ($wallet) {
                $userUuid = $wallet->getUserUuid();
                $available = $this->calculateTotalAvailable($userUuid);
                $processing = $this->calculateTotalProcessing($userUuid);

                $this->updateWalletBalances($wallet, $available, $processing);
            }

            DB::commit();

            return $walletLine;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to add wallet line: '.$e->getMessage());
            throw $e;
        }
    }

    public function setState(WalletLine $walletLine, int $state): WalletLine
    {
        // Use a transaction to ensure atomic operation
        DB::beginTransaction();

        try {
            $walletLine->update(['state' => $state]);

            // Update the wallet totals whenever a wallet line state changes
            $wallet = $this->findWallet($walletLine->getWalletUuid());
            if ($wallet) {
                $userUuid = $wallet->getUserUuid();
                $available = $this->calculateTotalAvailable($userUuid);
                $processing = $this->calculateTotalProcessing($userUuid);

                $this->updateWalletBalances($wallet, $available, $processing);
            }

            DB::commit();

            return $walletLine;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update wallet line state: '.$e->getMessage());
            throw $e;
        }
    }

    public function updateWalletLine(
        int $state,
        string $walletLineUuid
    ): ?WalletLine {
        $walletLine = $this->walletLine->find($walletLineUuid);

        if (is_null($walletLine)) {
            return null;
        }

        // Use a transaction to ensure atomic operation
        DB::beginTransaction();

        try {
            $walletLine->update(['state' => $state]);

            // Update the wallet totals whenever a wallet line state changes
            $wallet = $this->findWallet($walletLine->getWalletUuid());
            if ($wallet) {
                $userUuid = $wallet->getUserUuid();
                $available = $this->calculateTotalAvailable($userUuid);
                $processing = $this->calculateTotalProcessing($userUuid);

                $this->updateWalletBalances($wallet, $available, $processing);
            }

            DB::commit();

            return $walletLine;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to update wallet line state: '.$e->getMessage());
            throw $e;
        }
    }

    public function resetAvailableBalanceAfterPayout(string $userUuid): bool
    {
        // Gebruik een transactie met locking om race conditions te voorkomen
        DB::beginTransaction();

        try {
            // Lock de wallet record
            $wallet = Wallet::where('user_uuid', $userUuid)->lockForUpdate()->first();

            if (! $wallet) {
                DB::rollBack();
                Log::error("Failed to reset balance: Wallet not found for user $userUuid");

                return false;
            }

            // Check direct in de database of er nog beschikbare wallet lines zijn
            $availableCount = WalletLine::whereHas('wallet', function ($query) use ($userUuid) {
                $query->where('user_uuid', $userUuid);
            })
                ->whereIn('state', [WalletLine::AVAILABLE, WalletLine::COMPLETED])
                ->count();

            if ($availableCount === 0) {
                // Forceer de balans op precies 0.00 als er geen beschikbare wallet lines zijn
                Log::info("Force resetting available balance to 0 for user $userUuid");
                $wallet->update(['total_available' => 0]);
            } else {
                // Er zijn nog wallet lines, bereken de juiste waarde
                $availableAmount = $this->calculateTotalAvailable($userUuid);
                Log::info("Recalculating available balance for user $userUuid: $availableAmount");
                $wallet->update(['total_available' => $availableAmount]);
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reset available balance: '.$e->getMessage());

            return false;
        }
    }

    public function updateWalletLinesForPayout(string $walletUuid): void
    {
        WalletLine::where('wallet_uuid', $walletUuid)
            ->where('state', WalletLine::AVAILABLE)
            ->update(['state' => WalletLine::PAID_OUT]);
    }

    public function countAvailableWalletLinesForUser(string $userId): float
    {
        // Lock de wallet record om race conditions te voorkomen
        DB::beginTransaction();

        try {
            $wallet = Wallet::where('user_uuid', $userId)->lockForUpdate()->first();

            if (! $wallet) {
                DB::rollBack();

                return 0;
            }

            // Bereken de totalen direct uit de database
            $totalAvailable = $this->calculateTotalAvailable($userId);
            $totalProcessing = $this->calculateTotalProcessing($userId);

            // Update wallet totalen
            $wallet->update([
                'total_available' => $totalAvailable,
                'total_processing' => $totalProcessing,
            ]);

            DB::commit();

            // Forceer refresh van object
            $wallet = Wallet::where('user_uuid', $userId)->first();

            return $wallet->total_available;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to count available wallet lines: '.$e->getMessage());

            // Probeer alsnog een waarde te retourneren zonder lock
            $wallet = Wallet::where('user_uuid', $userId)->first();

            return $wallet ? $wallet->total_available : 0;
        }
    }

    public function calculateTotalProcessing(string $userUuid): float
    {
        return WalletLine::whereHas('wallet', function ($query) use ($userUuid) {
            $query->where('user_uuid', $userUuid);
        })
            ->whereIn('state', [
                WalletLine::PROCESSING,
                WalletLine::ON_HOLD,
                WalletLine::RESERVED,
            ])
            ->sum('amount');
    }

    public function applyAnnulationFee(string $userUuid, string $orderUuid, float $fee = 0.60): bool
    {
        DB::beginTransaction();

        try {
            $wallet = $this->findWalletForUser($userUuid);

            if (! $wallet) {
                Log::error("Wallet not found for user when applying cancellation fee: $userUuid");
                DB::rollBack();

                return false;
            }

            // Log current balance for debugging
            Log::info("Wallet before cancellation fee: Available = {$wallet->getTotalAvailable()}, Processing = {$wallet->getTotalProcessing()}");

            // Create a specific wallet line for the cancellation fee - IMPORTANT: amount is negative
            $walletLine = $this->walletLine->create([
                'wallet_uuid' => $wallet->getUuid(),
                'order_uuid' => $orderUuid,
                'amount' => -$fee,  // Negative amount for costs
                'state' => WalletLine::CANCELLATION_COST,
            ]);

            // Calculate new balances
            $availableAmount = $this->calculateTotalAvailable($userUuid);
            $processingAmount = $this->calculateTotalProcessing($userUuid);

            Log::info("Calculated new balances: Available = $availableAmount, Processing = $processingAmount");

            // Update wallet balances directly
            $wallet->update([
                'total_available' => $availableAmount,
                'total_processing' => $processingAmount,
            ]);

            // Get fresh wallet to verify
            $freshWallet = $this->wallet->find($wallet->getUuid());
            Log::info("Updated wallet balances: Available = {$freshWallet->getTotalAvailable()}, Processing = {$freshWallet->getTotalProcessing()}");

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error applying cancellation fee: '.$e->getMessage());

            return false;
        }
    }

    public function applyTransactionCost(string $userUuid, string $orderUuid, float $fee): void
    {
        DB::beginTransaction();

        try {
            $wallet = $this->wallet->where('user_uuid', $userUuid)->lockForUpdate()->first();

            if (! $wallet) {
                DB::rollBack();
                \Log::error("Cannot find wallet for user $userUuid when applying transaction cost");

                return;
            }

            // Create negative wallet line for transaction cost
            $this->walletLine->create([
                'wallet_uuid' => $wallet->getUuid(),
                'order_uuid' => $orderUuid,
                'amount' => -abs($fee), // Ensure it's always negative
                'state' => WalletLine::CANCELLATION_COST,
            ]);

            // Recalculate total available using the calculateTotalAvailable method
            $newAvailableBalance = $this->calculateTotalAvailable($userUuid);
            $processingAmount = $this->calculateTotalProcessing($userUuid);

            // Update wallet balances
            $wallet->update([
                'total_available' => $newAvailableBalance,
                'total_processing' => $processingAmount,
            ]);

            DB::commit();

            \Log::info("Applied transaction cost of €{$fee} for user {$userUuid}, new balance: €{$newAvailableBalance}");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to apply transaction cost: '.$e->getMessage());
        }
    }

    public function calculateTotalAvailable(string $userUuid): float
    {
        // Beschikbare wallet lines
        $availableAmount = WalletLine::whereHas('wallet', function ($query) use ($userUuid) {
            $query->where('user_uuid', $userUuid);
        })
            ->whereIn('state', [
                WalletLine::AVAILABLE,
                WalletLine::COMPLETED,
            ])
            ->sum('amount');

        // Openstaande transactiekosten
        $unpaidCancellationCosts = WalletLine::whereHas('wallet', function ($query) use ($userUuid) {
            $query->where('user_uuid', $userUuid);
        })
            ->where('state', WalletLine::CANCELLATION_COST)
            ->sum('amount');

        $total = $availableAmount + $unpaidCancellationCosts;

        // Add to debug info if it exists
        if (session()->has('wallet_debug')) {
            $debug = session('wallet_debug', []);
            $debug[] = "calculateTotalAvailable() breakdown: Regular={$availableAmount}, Costs={$unpaidCancellationCosts}, Total={$total}";
            session()->flash('wallet_debug', $debug);
        }

        return $total;
    }

    public function updateWalletBalances(Wallet $wallet, float $availableAmount, float $processingAmount): Wallet
    {
        $wallet->update([
            'total_available' => $availableAmount,
            'total_processing' => $processingAmount,
        ]);

        return $wallet->fresh();
    }

    public function findWalletLineByOrderUuid(string $orderId): ?WalletLine
    {
        return $this->walletLine->where('order_uuid', $orderId)->first();
    }

    public function findAvailableWalletLineByOrderUuid(string $orderUuid): ?WalletLine
    {
        return $this->walletLine
            ->where('order_uuid', $orderUuid)
            ->where('state', WalletLine::AVAILABLE)
            ->first();
    }

    public function findUnavailableWalletLineByOrderUuid(string $orderUuid): ?WalletLine
    {
        return $this->walletLine
            ->where('order_uuid', $orderUuid)
            ->where('state', '!=', WalletLine::AVAILABLE)
            ->first();
    }

    public function processOrderStatusUpdates(string $userUuid): void
    {
        // Check if there are any PAYOUT_INITIATED lines - if so, don't change any wallet lines to AVAILABLE
        $payoutInitiatedCount = WalletLine::whereHas('wallet', function ($query) use ($userUuid) {
            $query->where('user_uuid', $userUuid);
        })
            ->where('state', WalletLine::PAYOUT_INITIATED)
            ->count();

        // Get all adverts for this user
        $activeAdverts = $this->advertRepository->getUsersAdverts(
            $userUuid,
            Carbon::now()->subYear()->format('Y-m-d'),
            Carbon::now()->endOfMonth()->addYear()->format('Y-m-d')
        );

        foreach ($activeAdverts as $advert) {
            $now = Carbon::now();
            $pickupTo = $advert->getParsedPickupTo();

            foreach ($advert->order as $order) {
                // Only handle successful payments that aren't canceled or expired
                if ($order->payment_state == Order::SUCCEED &&
                    $order->status !== Order::STATUS_GEANNULEERD) {

                    $walletLine = $this->walletRepository->findWalletLineByOrderUuid($order->getUuid());

                    if (! $walletLine) {
                        continue;
                    }

                    // Protected states that shouldn't be modified
                    $protectedStates = [
                        WalletLine::CANCELLATION_COST,
                        WalletLine::CANCELLATION_COST_PAID,
                        WalletLine::PAYOUT_INITIATED,
                        WalletLine::PAID_OUT,
                        WalletLine::REFUNDING,
                        WalletLine::REFUNDED,
                    ];

                    if (in_array($walletLine->getState(), $protectedStates)) {
                        continue;
                    }

                    // If pickup time has passed, change to AVAILABLE
                    if ($pickupTo->isPast()) {
                        if ($walletLine->getState() !== WalletLine::AVAILABLE) {
                            $this->walletRepository->setState($walletLine, WalletLine::AVAILABLE);

                            // Update order status if needed
                            if ($order->status !== Order::STATUS_GEANNULEERD &&
                                $order->status !== Order::STATUS_VERLOPEN) {
                                $order->update(['status' => Order::STATUS_VERLOPEN]);
                            }
                        }
                    } else {
                        // Keep in PROCESSING until pickup time, don't change status during preparation/pickup
                        if ($walletLine->getState() !== WalletLine::PROCESSING) {
                            $this->walletRepository->setState($walletLine, WalletLine::PROCESSING);
                        }
                    }
                }
            }
        }
    }

    public function getTransactionCosts($walletUuid): float
    {
        // Bereken alleen de ECHTE transactiekosten (CANCELLATION_COST wallet lines)
        $transactionCosts = $this->walletLine
            ->where('wallet_uuid', $walletUuid)
            ->where('state', WalletLine::CANCELLATION_COST)
            ->sum('amount');

        // Return de absolute waarde (transactiekosten zijn negatief opgeslagen)
        return abs($transactionCosts);
    }

    public function getUnpaidTransactionCosts($walletUuid): float
    {
        $wallet = $this->wallet->find($walletUuid);

        // Als de wallet bestaat en een negatief saldo heeft, dat is wat er betaald moet worden
        if ($wallet && $wallet->total_available < 0) {
            return abs($wallet->total_available);
        }

        // Anders zijn er geen openstaande transactiekosten
        return 0.00;
    }

    public function payTransactionCosts()
    {
        $mollie = new \Mollie\Api\MollieApiClient;
        $mollie->setApiKey(env('MOLLIE_API_KEY'));

        $wallet = $this->walletRepository->findWalletForUser(
            $this->request->user()->getUuid()
        );

        // Gebruik de nieuwe functie voor het berekenen van ALLEEN de onbetaalde transactiekosten
        // (of gewoon het negatieve saldo in absolute waarde)
        $transactionCost = abs($wallet->total_available);

        if ($transactionCost == 0) {
            return redirect()->route('dashboard.wallet.iban');
        }

        try {
            $amount = $transactionCost.'';
            $explodedAmount = explode('.', $amount);
            $decimalAmount = isset($explodedAmount[1]) ? $explodedAmount[1] : '';

            if (strlen($decimalAmount) === 1) {
                $amount = $amount.'0';
            } elseif ($decimalAmount == '') {
                $amount = $amount.'.00';
            }

            // Maak een betaling aan met de gegevens van de bestelling
            $payment = $mollie->payments->create([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => $amount, // Bedrag dat moet worden betaald
                ],
                'description' => 'Transactiekosten', // Beschrijving van de betaling
                'redirectUrl' => route('dashboard.wallet.pay.transaction.confirm'), // URL om naar door te verwijzen na betaling
                'cancelUrl' => route('dashboard.wallet.pay.transaction.cancel'), // URL om naar door te verwijzen na annuleren van de betaling
            ]);

            session()->flash('paymentId', $payment->id);

            // Redirect naar de betaalpagina van Mollie
            return redirect($payment->getCheckoutUrl());

        } catch (ApiException $e) {
            // Als er een fout optreedt bij het aanmaken van de betaling, geef een foutmelding weer
            return redirect()->route('dashboard.wallet.home')->with('message', 'Er is iets fout gegaan');
        }
    }

    public function getTransactionCostsWalletLines($walletUuid)
    {
        return $this->walletLine
            ->where('wallet_uuid', $walletUuid)
            ->where('state', WalletLine::CANCELLATION_COST)
            ->get();
    }

    public function updateWalletAfterTransaction(string $walletUuid): void
    {
        DB::beginTransaction();
        try {
            $wallet = $this->wallet->where('uuid', $walletUuid)->lockForUpdate()->first();

            if (! $wallet) {
                DB::rollBack();

                return;
            }

            // Tel het aantal openstaande transactiekosten
            $remainingCosts = $this->walletLine
                ->where('wallet_uuid', $walletUuid)
                ->where('state', WalletLine::CANCELLATION_COST)
                ->count();

            // Als er geen openstaande kosten meer zijn en het saldo is negatief, reset naar 0
            if ($remainingCosts === 0 && $wallet->total_available < 0) {
                $wallet->update(['total_available' => 0]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating wallet after transaction: '.$e->getMessage());
        }
    }
}
