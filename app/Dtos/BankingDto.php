<?php

namespace App\Dtos;

class BankingDto
{
    private string $userUuid;

    private string $accountHolder;

    private string $iban;

    private bool $validated;

    private string $bic;

    private string $paymentId;

    public function __construct(
        string $userUuid,
        string $accountHolder,
        string $iban,
        ?bool $validated = true,
        ?string $bic = null,
        ?string $paymentId = null
    ) {
        $this->userUuid = $userUuid;
        $this->accountHolder = $accountHolder;
        $this->iban = $iban;
        $this->validated = $validated;
        $this->bic = $bic;
        $this->paymentId = $paymentId;
    }

    public function getUserUuid(): string
    {
        return $this->userUuid;
    }

    public function getAccountHolder(): string
    {
        return $this->accountHolder;
    }

    public function getIban(): string
    {
        return $this->iban;
    }

    public function isValidated(): bool
    {
        return $this->validated;
    }

    public function getBic(): string
    {
        return $this->bic;
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }
}
