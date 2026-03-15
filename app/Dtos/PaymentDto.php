<?php

namespace App\Dtos;

use Carbon\Carbon;

class PaymentDto
{
    private string $userUuid;

    private string $bankingUuid;

    private float $amount;

    private string $state;

    private string $paymentType;

    private ?string $paymentDate;

    public function __construct(
        string $userUuid,
        string $bankingUuid,
        float $amount,
        string $state,
        string $paymentType,
        ?string $paymentDate = null
    ) {
        $this->userUuid = $userUuid;
        $this->bankingUuid = $bankingUuid;
        $this->amount = $amount;
        $this->state = $state;
        $this->paymentType = $paymentType;
        $this->paymentDate = $paymentDate;
    }

    public function getUserUuid(): string
    {
        return $this->userUuid;
    }

    public function getBankingUuid(): string
    {
        return $this->bankingUuid;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function getPaymentDate(): ?string
    {
        return $this->paymentDate;
    }

    public function getPaymentType(): string
    {
        return $this->paymentType;
    }
}
