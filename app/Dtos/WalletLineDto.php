<?php

namespace App\Dtos;

class WalletLineDto
{
    private string $walletUuid;

    private ?string $orderUuid;

    private float $amount;

    private int $state;

    public function __construct(
        string $walletUuid,
        ?string $orderUuid,
        float $amount,
        int $state
    ) {
        $this->walletUuid = $walletUuid;
        $this->orderUuid = $orderUuid;
        $this->amount = $amount;
        $this->state = $state;
    }

    public function getWalletUuid(): string
    {
        return $this->walletUuid;
    }

    public function getOrderUuid(): ?string
    {
        return $this->orderUuid;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getState(): int
    {
        return $this->state;
    }
}
