<?php

namespace App\Repositories;

use App\Dtos\BankingDto;
use App\Dtos\PaymentDto;
use App\Models\Banking;
use App\Models\Payment;
use App\Models\Wallet;
use App\Models\WalletLine;
use Illuminate\Support\Collection;

class BankingRepository
{
    private Banking $banking;

    private Payment $payment;

    private WalletLine $walletLine;

    private Wallet $wallet;

    public function __construct(
        Banking $banking,
        Payment $payment,
        WalletLine $walletLine,
        Wallet $wallet
    ) {
        $this->banking = $banking;
        $this->payment = $payment;
        $this->walletLine = $walletLine;
        $this->wallet = $wallet;

    }

    public function find(string $bankingUuid): ?Banking
    {
        return $this->banking->find($bankingUuid);
    }

    public function findByUserUuid(string $userUuid): ?Banking
    {
        return $this->banking->where('user_uuid', $userUuid)->first();
    }

    public function findUsersByBanking(string $iban): Collection
    {
        return $this->banking->where('iban', $iban)->get();
    }

    public function create(
        BankingDto $bankingDto
    ): Banking {
        return $this->banking->create([
            'user_uuid' => $bankingDto->getUserUuid(),
            'account_holder' => $bankingDto->getAccountHolder(),
            'iban' => $bankingDto->getIban(),
            'validated' => $bankingDto->isValidated(),
        ]);
    }

    public function isIbanVerified(string $userUuid): bool
    {
        $banking = $this->findByUserUuid($userUuid);

        if (! $banking) {
            return false;
        }

        return $banking->isVerified();
    }

    public function update(
        BankingDto $bankingDto,
        string $bankingUuid
    ): ?Banking {
        $banking = $this->find($bankingUuid);

        if (is_null($banking)) {
            return null;
        }

        $banking->update([
            'account_holder' => $bankingDto->getAccountHolder(),
            'iban' => $bankingDto->getIban(),
            'validated' => $bankingDto->isValidated(),
        ]);

        return $banking;
    }

    public function validateIban(
        string $bankingUuid,
        bool $validate
    ): ?Banking {
        $banking = $this->find($bankingUuid);

        if (is_null($banking)) {
            return null;
        }

        $banking->update([
            'validated' => $validate,
        ]);

        return $banking;
    }

    public function verified(
        string $bankingUuid,
        string $idLocation,
        bool $verified
    ): ?Banking {
        $banking = $this->find($bankingUuid);

        if (is_null($banking)) {
            return null;
        }

        $banking->update([
            'verified' => $verified,
            'id_location' => $idLocation,
        ]);

        return $banking;
    }

    public function delete(string $bankingUuid): ?bool
    {
        return $this->find($bankingUuid)?->delete();
    }

    public function createPayment(PaymentDto $paymentDto): Payment
    {
        return $this->payment->create([
            'user_uuid' => $paymentDto->getUserUuid(),
            'banking_uuid' => $paymentDto->getBankingUuid(),
            'amount' => $paymentDto->getAmount(),
            'state' => $paymentDto->getState(),
            'payment_date' => $paymentDto->getPaymentDate(),
        ]);
    }

    // check if last payment date == today
    public function checkLastPaymentDate(string $userUuid): bool
    {
        $lastPayment = $this->payment->where('user_uuid', $userUuid)->latest()->first();

        if ($lastPayment) {
            return $lastPayment->created_at->isToday();
        }

        return false;
    }

    public function findPaymentForUser(string $paymentUuid): Collection
    {
        return $this->payment->where('uuid', $paymentUuid)->get();
    }

    public function findPaymentsForUser(string $userUuid): Collection
    {
        return $this->payment->where('user_uuid', $userUuid)->get();
    }

    public function findPaymentByUuid(string $uuid): ?Payment
    {
        return Payment::where('uuid', $uuid)->first();
    }

    public function findUnpaidPayoutsForAllUsers(): Collection
    {
        $payments = $this->payment
            ->with(['user.UserProfile', 'user.banking', 'user.dac7Information', 'user.orders.advert'])
            ->orderBy('created_at', 'desc')
            ->get();

        $result = $payments->filter(function ($payment) {
            return $payment->user !== null;
        })->map(function ($payment) {
            $user = $payment->user;
            $userProfile = $user->UserProfile;
            $banking = $user->banking;

            // Only get DAC7 information provided status directly from the model
            // This is fine because it's just reading data, not calculating thresholds
            $dac7InfoProvided = $user->dac7Information ? $user->dac7Information->information_provided : false;

            return [
                'username' => $user->username ?? 'N/A',
                'email' => $user->email ?? 'N/A',
                'firstname' => $userProfile->firstname ?? 'N/A',
                'lastname' => $userProfile->lastname ?? 'N/A',
                'phone_number' => $userProfile->phone_number ?? 'N/A',
                'iban' => $banking->iban ?? 'N/A',
                'amount' => $payment->amount,
                'payment_state' => $payment->state,
                'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                'payment_date' => $payment->payment_date,
                'payment_type' => $payment->payment_type ?? 'manual',
                'uuid' => $payment->uuid,
                'user_uuid' => $user->uuid,
                'dac7_information_provided' => $dac7InfoProvided,
                // 'dac7_exceeded' line removed - will be calculated in controller
            ];
        })->sortByDesc('created_at')->values();

        return $result;
    }

    public function updatePaymentState(string $paymentUuid, string $state): ?Payment
    {
        DB::beginTransaction();

        try {
            $payment = $this->payment->where('uuid', $paymentUuid)->lockForUpdate()->first();

            if (is_null($payment)) {
                DB::rollBack();

                return null;
            }

            $payment->update([
                'state' => $state,
                'payment_date' => now(),
            ]);

            // If payment is being completed, ensure wallet lines are properly marked as paid out
            if ($state == Payment::COMPLETED) {
                $userUuid = $payment->user_uuid;
                $wallet = $this->wallet->where('user_uuid', $userUuid)->first();

                if ($wallet) {
                    $this->walletLine->where('wallet_uuid', $wallet->uuid)
                        ->where('state', WalletLine::PAYOUT_INITIATED)
                        ->update(['state' => WalletLine::PAID_OUT]);

                    // Ensure wallet balance remains at 0
                    $wallet->update(['total_available' => 0]);
                }
            }

            DB::commit();

            return $payment;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to update payment state: '.$e->getMessage());

            return null;
        }
    }

    public function findPaymentWalletlines(string $paymentUuid): Collection
    {
        $payment = $this->payment->where('uuid', $paymentUuid)->select('payment_date', 'user_uuid')->first();
        $userUuid = $payment->user_uuid;
        $walletUuid = $this->wallet->where('user_uuid', $userUuid)->select('uuid')->first()->uuid;
        $paymentDate = $payment->payment_date;

        return $this->walletLine->where('wallet_uuid', $walletUuid)
            ->where('state', WalletLine::AVAILABLE)->where('updated_at', '<', $paymentDate)->get();
    }

    public function findTransactionCostWalletlines(string $userUuid): Collection
    {

        $walletUuid = $this->wallet->where('user_uuid', $userUuid)->select('uuid')->first()->uuid;

        return $this->walletLine->where('wallet_uuid', $walletUuid)
            ->where('state', WalletLine::CANCELLATION_COST)->get();
    }

    public function findTransactionCostWalletlinesPaid(string $paymentUuid): Collection
    {
        $payment = $this->payment->where('uuid', $paymentUuid)->select('payment_date', 'user_uuid')->first();
        $userUuid = $payment->user_uuid;

        $walletUuid = $this->wallet->where('user_uuid', $userUuid)->select('uuid')->first()->uuid;
        $paymentDate = $payment->payment_date;

        return $this->walletLine->where('wallet_uuid', $walletUuid)
            ->where('state', WalletLine::CANCELLATION_COST_PAID)->where('updated_at', '<', $paymentDate)->get();
    }
}
