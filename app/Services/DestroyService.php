<?php

namespace App\Services;

use App\Mail\DishDeleteCookMail;
use App\Mail\DishDeleteCustomerMail;
use App\Models\Advert;
use App\Models\Banking;
use App\Models\Cook;
use App\Models\CookProfileDescription;
use App\Models\Dish;
use App\Models\Image;
use App\Models\Order;
use App\Models\Privacy;
use App\Models\Review;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Wallet;
use App\Repositories\AdvertRepository;
use App\Repositories\DishRepository;
use App\Repositories\OrderRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class DestroyService
{
    private DishRepository $dishRepository;

    private AdvertRepository $advertRepository;

    private OrderRepository $orderRepository;

    public function __construct(
        DishRepository $dishRepository,
        AdvertRepository $advertRepository,
        OrderRepository $orderRepository
    ) {
        $this->dishRepository = $dishRepository;
        $this->advertRepository = $advertRepository;
        $this->orderRepository = $orderRepository;
    }

    public function deleteDish(
        string $dishUuid,
        User $user,
        ?string $editText = '',
        ?bool $accountRemoval = false
    ): ?bool {
        $dish = $this->dishRepository->find($dishUuid);

        if (is_null($dish) || $dish->getUserUuid() !== $user->getUuid()) {
            return false;
        }

        if (! $accountRemoval) {
            Mail::to(
                $user->getEmail(),
                $user->getUsername()
            )->send(new DishDeleteCookMail(
                $user,
                $dish
            ));
        }

        /** @var Advert $advert */
        foreach ($this->advertRepository->findAdvertsForDish($dish->getUuid()) as $advert) {
            /** @var Order $order */
            foreach ($advert->order as $order) {
                $this->orderRepository->setReviewSend($order);
                $this->orderRepository->setDeletedProfile($order);
            }

            $this->advertRepository->profileDelete($advert);
            $this->advertRepository->delete($advert->getUuid());
        }

        return $this->dishRepository->delete($dishUuid);
    }

    public function reactivateUser(User $user): bool
    {
        /** @var Cook $cook */
        $cook = $user->trashedCook;
        /** @var UserProfile $userProfile */
        $userProfile = $user->trashedUserProfile;
        /** @var Privacy $privacy */
        $privacy = $user->trashedPrivacy;
        /** @var Banking $banking */
        $banking = $user->trashedBanking;
        /** @var Wallet $wallet */
        $wallet = $user->trashedWallet;
        /** @var CookProfileDescription $profileDescription */
        $profileDescription = $user->trashedProfileDescription;
        /** @var Collection $reviews */
        $reviews = $user->trashedReviews;
        /** @var Collection $image */
        $image = $user->trashedImages;
        /** @var Collection $dishes */
        $dishes = $user->trashedDish;
        /** @var Collection $order */
        $orders = $user->trashedOrder;

        $cook?->restore();
        $userProfile?->restore();
        $privacy?->restore();
        $banking?->restore();
        $wallet?->restore();
        $profileDescription?->restore();

        /** @var Review $review */
        foreach ($reviews as $review) {
            $review->restore();
        }

        // ← NIEUWE CODE: Alleen hoofdafbeelding restoren
        /** @var Image $item */
        foreach ($image as $item) {
            if ($item->main_picture && $item->type_id == Image::PROFILE_IMAGE) {
                $item->restore();
            }
        }

        /** @var Dish $dish */
        foreach ($dishes as $dish) {
            $dish->restore();

            foreach ($dish->trashedAdverts as $advert) {
                if ($advert->profileDeleted()) {
                    $advert->update([
                        'profile_deleted' => false,
                    ]);

                    $advert->save();
                    $advert->restore();
                }
            }
        }

        /** @var Order $order */
        foreach ($orders as $order) {
            if ($order->profileDeleted()) {
                $order->update(['profile_deleted' => false]);
                $order->save();
                $order->restore();
            }
        }

        return true;
    }
}
