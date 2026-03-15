<?php

namespace App\Repositories;

use App\Dtos\OrderDto;
use App\Models\Order;
use App\Models\User;
use App\Models\WalletLine;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    private Order $order;

    public function __construct(
        Order $order
    ) {
        $this->order = $order;
    }

    public function find(string $uuid): ?Order
    {
        return $this->order
            ->withTrashed()
            ->with('walletLine')
            ->with('advert')
            ->find($uuid);
    }

    public function get(): Collection
    {
        return $this->order->get();
    }

    public function getActiveOrdersForUser(string $userUuid): Collection
    {
        $currentUser = User::find($userUuid);

        return $this->order
            ->withTrashed()
            ->where(function ($q) use ($userUuid, $currentUser) {
                // Huidige user orders
                $q->where('user_uuid', $userUuid)
                  // OF orders van oude accounts met zelfde email
                    ->orWhereHas('user', function ($userQuery) use ($currentUser) {
                        $userQuery->withTrashed()
                            ->where('email', $currentUser->email)
                            ->where('uuid', '!=', $currentUser->uuid);
                    });
            })
            ->where(function ($query) {
                $query->where('status', Order::STATUS_ACTIEF)
                    ->orWhere(function ($q) {
                        $q->where('payment_state', Order::SUCCEED)
                            ->where('status', '!=', Order::STATUS_GEANNULEERD);
                    });
            })
            ->with(['client', 'advert'])
            ->get();
    }

    public function collectByMonth(): array
    {
        $date = Carbon::now()->subMonths(11);
        $orderCollection = new Collection;
        $maxRevenue = 100.00;

        for ($i = 0; $i < 12; $i++) {
            // GEFIXED: Laad ook de dish relatie
            $orders = $this->order
                ->with('advert.dish') // TOEGEVOEGD: laad dish relatie
                ->whereBetween('created_at', [
                    $date->startOfMonth()->format('Y-m-d'),
                    $date->endOfMonth()->format('Y-m-d'),
                ])
                ->get();

            $revenue = 0.00;

            foreach ($orders as $order) {
                // GEFIXED: Nu via dish in plaats van advert
                $revenue += $order->advert->dish->getPortionPrice() * $order->getPortionAmount();
            }

            if ($revenue > $maxRevenue) {
                $maxRevenue = $revenue;
            }

            $orderCollection->add([$date->format('M Y') => [
                'orderAmount' => $orders->count(),
                'price' => $revenue,
            ],
            ]);

            $date = $date->startOfMonth()->addMonth();
        }

        return [
            'max' => $maxRevenue,
            'collection' => $orderCollection,
        ];
    }

    public function getTotalCancellationsForToday(): int
    {
        $today = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');

        return $this->order
            ->where('status', Order::STATUS_GEANNULEERD)
            ->where('updated_at', '>=', $today)
            ->count();
    }

    public function getForUser(string $userUuid, array $filters, ?int $page): LengthAwarePaginator
    {
        $currentUser = User::find($userUuid);

        $query = $this->order
            ->withTrashed()
            ->where(function ($q) use ($userUuid, $currentUser) {
                // Huidige user orders
                $q->where('user_uuid', $userUuid)
                  // OF orders van oude accounts met zelfde email
                    ->orWhereHas('user', function ($userQuery) use ($currentUser) {
                        $userQuery->withTrashed()
                            ->where('email', $currentUser->email)
                            ->where('uuid', '!=', $currentUser->uuid);
                    });
            });

        $query->where(function ($q) {
            $q->where('payment_state', Order::SUCCEED)
                ->orWhere('payment_state', Order::PAYOUT_PENDING)
                ->orWhere(function ($subQuery) {
                    $subQuery->where('status', Order::STATUS_GEANNULEERD)
                        ->where(function ($cancelQuery) {
                            $cancelQuery->where('payment_state', Order::SUCCEED)
                                ->orWhere('payment_state', Order::PAYOUT_PENDING);
                        });
                });
        });

        if (! empty($filters) && isset($filters['from']) && isset($filters['to'])) {
            $from = Carbon::parse($filters['from'])->startOfDay();
            $to = Carbon::parse($filters['to'])->endOfDay();

            $query->whereBetween('created_at', [$from, $to]);
        }

        $query->orderBy('created_at', 'desc');

        return $query->paginate(10, ['*'], 'page', $page);
    }

    public function create(OrderDto $orderDto): Order
    {
        return $this->order->create([
            'dish_uuid' => $orderDto->getDish()->getUuid(),
            'client_uuid' => $orderDto->getClient()->getUuid(),
            'user_uuid' => $orderDto->getUser()->getUuid(),
            'advert_uuid' => $orderDto->getAdvert()->getUuid(),
            'portion_amount' => $orderDto->getPortionAmount(),
            'expected_pickup_time' => $orderDto->getExpectedPickupTime(),
            'remarks' => $orderDto->getRemarks(),
            'payment_state' => $orderDto->getPaymentState(),
        ]);
    }

    public function update(
        OrderDto $orderDto,
        string $orderUuid
    ): ?Order {
        $order = $this->find($orderUuid);

        if (is_null($order)) {
            return null;
        }

        $order->update([
            'portion_amount' => $orderDto->getPortionAmount(),
            'expected_pickup_time' => $orderDto->getExpectedPickupTime(),
            'remarks' => $orderDto->getRemarks(),
            'payment_state' => $orderDto->getPaymentState(),
        ]);

        return $order;
    }

    public function setDeletedProfile(Order $order): bool
    {
        $order->update([
            'profile_deleted' => true,
            'deleted_at' => true,
        ]);

        return $order->save();
    }

    public function delete(string $orderUuid): ?bool
    {
        $order = $this->find($orderUuid);

        /** @var WalletLine $walletLine */
        $walletLine = $order->walletLine;

        $walletLine->update(['state' => WalletLine::REFUNDING]);
        $walletLine->save();

        return $order->delete();
    }

    public function getReviewOrders(): Collection
    {
        $timeStamp = Carbon::now()->subHours(2);

        return $this->order
            ->whereNull('review_send')
            ->where('expected_pickup_time', '<', $timeStamp->format('Y-m-d H:i:s'))
            ->where('status', '!=', Order::STATUS_GEANNULEERD)
            ->with('client')
            ->get();
    }

    public function setReviewSend(Order $order): bool
    {
        return $order->update(['review_send' => Carbon::now()]);
    }

    public function getOrdersByAdvertUuid(string $advertUuid): Collection
    {
        return $this->order
            ->where('advert_uuid', $advertUuid)
            ->get();
    }

    public function getPaidOrdersByAdvertUuid(string $advertUuid): Collection
    {
        return $this->order
            ->where('advert_uuid', $advertUuid)
            ->where('payment_state', Order::SUCCEED)
            ->where('status', Order::STATUS_ACTIEF)
            ->whereNotIn('status', [
                Order::STATUS_GEANNULEERD,
                Order::STATUS_VERLOPEN,
            ])
            ->get();
    }

    /**
     * Count cancellations made by a specific email today
     */
    public function getCancellationsByEmailToday(string $email): int
    {
        $today = Carbon::now()->startOfDay();

        return $this->order
            ->whereHas('client', function ($query) use ($email) {
                $query->where('email', $email);
            })
            ->where('status', Order::STATUS_GEANNULEERD)
            ->where('cancelled_by', Order::CANCELLED_BY_CLIENT)
            ->where('updated_at', '>=', $today)
            ->count();
    }

    public function getAllOrdersByAdvertUuid(string $uuid): Collection
    {
        return Order::where('advert_uuid', $uuid)
            ->whereNotIn('payment_state', [Order::IN_PROCESS, Order::FAILED])
            ->with(['client'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getClientsOrders()
    {
        $userUuid = auth()->user()->getUuid();
        $timeStamp = Carbon::now();

        return $this->order
            ->where('user_uuid', $userUuid)
            ->where('expected_pickup_time', '>', $timeStamp->format('Y-m-d H:i:s'))
            ->where('status', '!=', Order::STATUS_GEANNULEERD)
            ->get();
    }

    public function getClientsAdvertOrdersByClientId(string $clientUuid, string $advertUuid)
    {
        $timeStamp = Carbon::now();

        return $this->order
            ->where('client_uuid', $clientUuid)
            ->where('advert_uuid', $advertUuid)
            ->where('expected_pickup_time', '<', $timeStamp->format('Y-m-d H:i:s'))
            ->where('status', Order::STATUS_GEANNULEERD)
            ->where('cancelled_by', Order::CANCELLED_BY_CLIENT)
            ->whereDate('updated_at', $timeStamp->format('Y-m-d')) // Filter voor vandaag
            ->get();
    }

    public function getCanceledOrdersByUser(string $userUuid, string $filterType = 'day', ?string $cancelledBy = null): Collection
    {
        $now = Carbon::now();

        switch ($filterType) {
            case 'hour':
                $startOfPeriod = $now->startOfHour()->format('Y-m-d H:i:s');
                break;
            case 'day':
                $startOfPeriod = $now->startOfDay()->format('Y-m-d H:i:s');
                break;
            case 'week':
                $startOfPeriod = $now->startOfWeek()->format('Y-m-d H:i:s');
                break;
            case 'month':
                $startOfPeriod = $now->startOfMonth()->format('Y-m-d H:i:s');
                break;
            case 'year':
                $startOfPeriod = $now->startOfYear()->format('Y-m-d H:i:s');
                break;
            default:
                throw new \InvalidArgumentException("Invalid filter type provided. Use 'hour', 'day', 'week', 'month' or 'year'.");
        }

        // ✅ FIX: Only count cancelled orders that were successfully paid
        $query = $this->order
            ->with('client')
            ->where('user_uuid', $userUuid)
            ->where('status', Order::STATUS_GEANNULEERD)
            ->where('updated_at', '>=', $startOfPeriod)
            ->where(function ($q) {
                $q->where('payment_state', Order::SUCCEED)
                    ->orWhere('payment_state', Order::PAYOUT_PENDING);
            });

        if ($cancelledBy !== null) {
            $query->where('cancelled_by', $cancelledBy);
        }

        return $query->get();
    }
}
