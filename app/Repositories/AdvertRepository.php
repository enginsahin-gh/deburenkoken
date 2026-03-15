<?php

namespace App\Repositories;

use App\Dtos\AdvertDto;
use App\Models\Advert;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AdvertRepository
{
    private Advert $advert;

    public function __construct(Advert $advert)
    {
        $this->advert = $advert;
    }

    public function find(string $uuid): ?Advert
    {
        return $this->advert
            ->withTrashed()
            ->with('dish') // BELANGRIJK: Dish laden voor prijsinformatie
            ->find($uuid);
    }

    public function findAdvertsForDish(string $dishUuid): Collection
    {
        return $this->advert->whereHas('dish', function ($query) use ($dishUuid) {
            $query->where('dish_uuid', $dishUuid);
        })
            ->with(['order', 'dish']) // Ook dish laden
            ->get();
    }

    public function get(?string $search = null): Collection
    {
        $query = $this->advert
            ->where('created_at', '<', Carbon::now())
            ->with('dish'); // Dish laden voor prijsinformatie

        if (! is_null($search)) {
            $query->whereHas('dish', function ($query) use ($search) {
                $query->where('title', 'LIKE', '%'.$search.'%');
            });
        }

        return $query->get();
    }

    public function getActiveAdverts(): Collection
    {
        return $this->advert
            ->where('order_date', '<', Carbon::now())
            ->orWhere('pickup_date', '<', Carbon::now())
            ->with('dish')
            ->get();
    }

    public function getActiveAdvertsCount(): int
    {
        return $this->advert
            ->whereNotNull('published')
            ->where('pickup_date', '>', Carbon::now())
            ->count();
    }

    public function getActiveAndPublishedAdverts(string $userUuid): Collection
    {
        return $this->advert
            ->whereNotNull('published') // Only published adverts
            ->whereNull('deleted_at')   // Not deleted
            ->whereHas('dish', function ($query) use ($userUuid) {
                $query->where('user_uuid', $userUuid);
            })
            ->where(function ($query) {
                // Either pickup date is in the future
                $query->where('pickup_date', '>', Carbon::now()->toDateString())
                    ->orWhere(function ($q) {
                        // Or same day but pickup time is still in the future
                        $q->where('pickup_date', '=', Carbon::now()->toDateString())
                            ->where('pickup_to', '>', Carbon::now()->toTimeString());
                    });
            })
            ->with('dish')
            ->get();
    }

    public function getPastAdverts(): Collection
    {
        return $this->advert
            ->where('pickup_date', '<', Carbon::now())
            ->with(['order.walletLine', 'dish'])
            ->get();
    }

    public function getAdvertsAfterOrderTime(): Collection
    {
        return $this->advert
            ->where('order_date', '<', Carbon::now())
            ->with(['order.walletLine', 'dish'])
            ->get();
    }

    public function getExpiringAdverts(): Collection
    {
        $now = Carbon::now();

        return $this->advert
            ->where('preparation_email_sent', false)
            ->where('published', '!=', null)
            ->where(function ($query) use ($now) {
                $query->whereDate('order_date', '=', $now->toDateString())
                    ->whereTime('order_time', '<=', $now->toTimeString());
            })
            ->orWhere(function ($query) use ($now) {
                $query->whereDate('order_date', '<', $now->toDateString())
                    ->where('preparation_email_sent', false);
            })
            ->with(['cook.user', 'dish'])
            ->get();
    }

    public function markPreparationEmailSent(string $advertUuid): bool
    {
        $advert = $this->find($advertUuid);
        if (! $advert) {
            return false;
        }
        $advert->preparation_email_sent = true;

        return $advert->save();
    }

    public function getExpiredAdvertsForUser(string $userUuid): Collection
    {
        return $this->advert
            ->whereHas('dish', function ($query) use ($userUuid) {
                $query->where('user_uuid', $userUuid);
            })
            ->where(function ($query) {
                $query->where('pickup_date', '<', Carbon::now()->toDateString())
                    ->orWhere(function ($query) {
                        $query->where('pickup_date', '=', Carbon::now()->toDateString())
                            ->where('pickup_to', '<', Carbon::now()->toTimeString());
                    });
            })
            ->whereNull('deleted_at')
            ->with(['order.walletLine', 'dish'])
            ->get();
    }

    public function getUsersAdverts(
        string $userUuid,
        ?string $from = null,
        ?string $to = null
    ): Collection {
        return $this->advert
            ->where(function ($query) use ($from, $to) {
                if (! is_null($from)) {
                    $query->where('pickup_date', '>=', Carbon::parse($from)->format('Y-m-d'));

                    if (is_null($to)) {
                        $to = $from;
                    }

                    $query->where('pickup_date', '<=', Carbon::parse($to)->format('Y-m-d'));
                } else {
                    $query->whereDate('pickup_date', '>=', Carbon::now()->startOfDay());
                    $query->where('pickup_from', '>=', Carbon::now()->format('H:i:s'))
                        ->orWhere('pickup_to', '<=', Carbon::now()->format('H:i:s'));
                }
            })->whereHas('dish', function ($query) use ($userUuid) {
                $query->where('user_uuid', $userUuid);
            })
            ->orderBy('pickup_date')
            ->orderBy('pickup_from', 'asc')
            ->with(['dish', 'order.client'])
            ->get();
    }

    public function getPastUserAdverts(
        string $userUuid,
        ?string $from = null,
        ?string $to = null
    ): Collection {
        $query = $this->advert
            ->withTrashed() // Include soft-deleted adverts
            ->whereHas('dish', function ($query) use ($userUuid) {
                $query->where('user_uuid', $userUuid);
            });

        // Apply date filtering only if dates are provided
        if (! is_null($from)) {
            $query->where(function ($q) use ($from, $to) {
                if (is_null($to)) {
                    $to = $from;
                }
                $q->where('pickup_date', '>=', Carbon::parse($from)->format('Y-m-d'))
                    ->where('pickup_date', '<=', Carbon::parse($to)->format('Y-m-d'));
            });
        } else {
            $query->where(function ($q) {
                // Where pickup is in the past OR advert is cancelled
                $q->where('pickup_date', '<', Carbon::now()->format('Y-m-d'))
                    ->orWhere(function ($sq) {
                        $sq->where('pickup_date', '=', Carbon::now()->format('Y-m-d'))
                            ->where('pickup_to', '<', Carbon::now()->format('H:i:s'));
                    })
                    ->orWhereNotNull('deleted_at');
            });
        }

        return $query->orderBy('pickup_date', 'desc')
            ->with(['dish', 'order.client'])
            ->get();
    }

    public function create(AdvertDto $advertDto): Advert
    {
        return $this->advert->create([
            'dish_uuid' => $advertDto->getDish()->getUuid(),
            'portion_amount' => $advertDto->getPortionAmount(),
            // VERWIJDERD: 'portion_price' => $advertDto->getPortionPrice(),
            'pickup_date' => $advertDto->getPickupDate(),
            'pickup_from' => $advertDto->getPickupFrom(),
            'pickup_to' => $advertDto->getPickupTo(),
            'order_date' => $advertDto->getOrderDate(),
            'order_time' => $advertDto->getOrderTime(),
            'published' => $advertDto->publish() ? Carbon::now() : null,
        ]);
    }

    public function publishAdvert(string $uuid): ?Advert
    {
        $advert = $this->find($uuid);

        if (is_null($advert)) {
            return null;
        }

        $advert->update(['published' => Carbon::now()]);

        return $advert;
    }

    public function update(
        AdvertDto $advertDto,
        string $advertUuid
    ): ?Advert {
        $advert = $this->find($advertUuid);

        if (is_null($advert)) {
            return null;
        }

        $advert->update([
            'dish_uuid' => $advertDto->getDish()->getUuid(),
            'portion_amount' => $advertDto->getPortionAmount(),
            // VERWIJDERD: 'portion_price' => $advertDto->getPortionPrice(),
            'pickup_date' => $advertDto->getPickupDate(),
            'pickup_from' => $advertDto->getPickupFrom(),
            'pickup_to' => $advertDto->getPickupTo(),
            'order_date' => $advertDto->getOrderDate(),
            'order_time' => $advertDto->getOrderTime(),
            'published' => $advertDto->publish() ? Carbon::now() : null,
        ]);

        return $advert;
    }

    public function delete(string $advertUuid): ?bool
    {
        return $this->find($advertUuid)?->delete();
    }

    public function findAdvertForOrder(string $advertUuid): null|Advert|Model
    {
        return $this->advert
            ->with(['order', 'dish', 'cook'])
            ->find($advertUuid);
    }

    public function profileDelete(Advert $advert): ?Advert
    {
        $advert->update([
            'profile_deleted' => true,
        ]);
        $advert->save();

        return $advert;
    }

    public function getCancelledAdvertsByUser(string $userUuid): Collection
    {
        return $this->advert
            ->onlyTrashed()
            ->whereHas('dish', function ($query) use ($userUuid) {
                $query->where('user_uuid', $userUuid);
            })
            ->with('dish')
            ->get();
    }
}
