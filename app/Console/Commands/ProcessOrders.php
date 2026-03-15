<?php

namespace App\Console\Commands;

use App\Models\Advert;
use App\Models\Order;
use App\Models\WalletLine;
use App\Repositories\AdvertRepository;
use App\Repositories\WalletRepository;
use Illuminate\Console\Command;

class ProcessOrders extends Command
{
    protected $signature = 'process:payment-on-orders';

    protected $description = 'process payments on orders';

    private AdvertRepository $advertRepository;

    private WalletRepository $walletRepository;

    public function __construct(
        AdvertRepository $advertRepository,
        WalletRepository $walletRepository
    ) {
        $this->advertRepository = $advertRepository;
        $this->walletRepository = $walletRepository;
        parent::__construct();
    }

    public function handle(): void
    {
        $adverts = $this->advertRepository->getAdvertsAfterOrderTime();

        /** @var Advert $advert */
        foreach ($adverts as $advert) {
            /** @var Order $order */
            foreach ($advert->order as $order) {
                /** @var WalletLine $walletLine */
                $walletLine = $order->walletLine;

                if (
                    in_array($walletLine->getState(), [
                        WalletLine::ON_HOLD,
                        WalletLine::PROCESSING,
                        WalletLine::AVAILABLE,
                    ])
                ) {
                    $this->walletRepository->updateWalletLine(
                        WalletLine::COMPLETED,
                        $walletLine->getUuid()
                    );

                    $this->walletRepository->processAvailable(
                        $walletLine->wallet,
                        $walletLine->getAmount()
                    );
                }
            }
        }
    }
}
