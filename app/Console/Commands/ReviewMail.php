<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Repositories\OrderRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ReviewMail extends Command
{
    protected $signature = 'review:send-mail';

    protected $description = 'Send review mail';

    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
        parent::__construct();
    }

    public function handle(): void
    {
        $orders = $this->orderRepository->getReviewOrders();

        foreach ($orders as $order) {
            Mail::to(
                $order->client->getEmail(),
                $order->client->getName()
            )->send(new \App\Mail\ReviewMail(
                $order->client,
                $order->getUuid(),
                $order->user->cook
            ));

            $this->orderRepository->setReviewSend($order);
        }
    }
}
