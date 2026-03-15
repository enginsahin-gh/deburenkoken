<?php

namespace App\Dtos;

class WalletDto
{
    private string $walletUuid;

    private float $total;

    public function __construct(
        string $walletUuid,
        float $total
    ) {
        $this->walletUuid = $walletUuid;
        $this->total = $total;
    }

    public function getWalletUuid(): string
    {
        return $this->walletUuid;
    }

    public function getTotal(): float
    {
        return $this->total;
    }
}
