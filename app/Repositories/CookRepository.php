<?php

namespace App\Repositories;

use App\Dtos\CommonDto;
use App\Dtos\CookDto;
use App\Models\Advert;
use App\Models\Cook;
use App\Models\Dish;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CookRepository
{
    private Cook $cook;

    public function __construct(Cook $cook)
    {
        $this->cook = $cook;
    }

    public function get(): Collection
    {
        return $this->cook->get();
    }

    public function find(string $uuid): ?Cook
    {
        return $this->cook->find($uuid);
    }

    public function findByUserUuid(string $uuid): ?Cook
    {
        return $this->cook->where('user_uuid', $uuid)->first();
    }

    public function create(CookDto $cookDto): Cook
    {
        $this->cook->where('user_uuid', $cookDto->getUser()->getUuid())->first()?->delete();

        return $this->cook->create([
            'user_uuid' => $cookDto->getUser()->getUuid(),
            'lat' => $cookDto->getLatitude(),
            'long' => $cookDto->getLongitude(),
            'street' => $cookDto->getStreet(),
            'house_number' => $cookDto->getHouseNumber(),
            'addition' => $cookDto->getAddition(),
            'postal_code' => $cookDto->getPostalCode(),
            'city' => $cookDto->getCity(),
            'country' => $cookDto->getCountry(),
            'description' => $cookDto->getDescription(),
            'mail_order' => $cookDto->getMailOrder(),
            'mail_cancel' => $cookDto->getMailCancel(),
            'mail_self' => $cookDto->getMailSelf(),
        ]);
    }

    public function update(
        CookDto $cookDto,
        string $cookUuid
    ): ?Cook {
        $cook = $this->find($cookUuid);

        if (is_null($cook)) {
            return null;
        }

        $cook->update([
            'lat' => $cookDto->getLatitude(),
            'long' => $cookDto->getLongitude(),
            'street' => $cookDto->getStreet(),
            'house_number' => $cookDto->getHouseNumber(),
            'addition' => $cookDto->getAddition(),
            'postal_code' => $cookDto->getPostalCode(),
            'city' => $cookDto->getCity(),
            'country' => $cookDto->getCountry(),
            'description' => $cookDto->getDescription(),
            'mail_order' => $cookDto->getMailOrder(),
            'mail_cancel' => $cookDto->getMailCancel(),
            'mail_self' => $cookDto->getMailSelf(),
        ]);

        return $cook;
    }

    public function updateReportOrDescription(
        CommonDto $commonDto,
        string $cookUuid
    ): ?Cook {
        $cook = $this->find($cookUuid);

        if (is_null($cook)) {
            return null;
        }

        $cook->update([
            'description' => $commonDto->getItem(),
            'mail_order' => $commonDto->getOne(),
            'mail_cancel' => $commonDto->getTwo(),
            'mail_self' => $commonDto->getThree(),
        ]);

        return $cook;
    }

    public function delete(string $uuid): ?Cook
    {
        $cook = $this->cook->find($uuid);

        if (is_null($cook)) {
            return null;
        }

        return $cook->delete();
    }

    public function cookDishes(
        string $cookUuid
    ): null|Cook|Model {
        return $this->cook->with('dishes')->find($cookUuid);
    }

    public function findCookByUserName(
        ?string $userName = '',
        ?int $page = 0
    ): LengthAwarePaginator {
        return $this->cook
            ->whereHas('user', function ($query) use ($userName) {
                $query->where('username', 'like', "%$userName%");
            })
            ->with('user')
            ->with('adverts.dish')
            ->with('adverts.order')
            ->offset($page * 25)
            ->paginate(25);
    }

    public function findCookByCoordinates(
        float $latitude,
        float $longitude,
        int $distance
    ): Collection {
        $sqlNew = "SELECT *,(((acos(sin(($latitude*pi()/180)) * sin((`lat`*pi()/180))+cos((".$latitude."*pi()/180)) * cos((`lat`*pi()/180)) * cos((($longitude-`long`)*pi()/180))))*180/pi())*60*1.1515*1.609344) as distance FROM cooks HAVING distance < $distance ORDER BY distance";

        // Fix: Remove DB::raw() wrapper - DB::select() expects a string, not an Expression object
        $results = DB::select($sqlNew);

        $cooks = new Collection;

        foreach ($results as $result) {
            /** @var Cook $cook */
            $cook = $this->cook
                ->with('adverts.cook.user.reviews')
                ->with('adverts.dish.image')
                ->with('adverts.order')
                ->with('adverts.dish.images')
                ->with('user.profileDescription')
                ->find($result->uuid);
            if (is_null($cook)) {
                continue;
            }

            $cook->setDistance($result->distance);
            $cooks->add($cook);
        }

        return $cooks;
    }

    public function findCookWithDishes(
        float $latitude,
        float $longitude,
        int $distance,
        ?string $from = null,
        ?string $to = null,
        ?float $minPrice = null,
        ?float $maxPrice = null,
        ?array $specs = [],
        ?array $status = [],
        ?bool $filters = false
    ): Collection {
        // $sqlNew = "SELECT *,(((acos(sin(($latitude*pi()/180)) * sin((`lat`*pi()/180))+cos((".$latitude."*pi()/180)) * cos((`lat`*pi()/180)) * cos((($longitude-`long`)*pi()/180))))*180/pi())*60*1.1515*1.609344) as distance FROM cooks HAVING distance < $distance ORDER BY distance";

        // $results = DB::select(
        //     DB::raw(
        //         $sqlNew
        //     )
        // );

        // Bereken de afstand en selecteer kolommen
        $cooksQuery = $this->cook->select('*')
            ->selectRaw(
                '(((acos(sin(('.$latitude.'*pi()/180)) * sin((`lat`*pi()/180))+cos(('.$latitude.'*pi()/180)) * cos((`lat`*pi()/180)) * cos((('.$longitude.'-`long`)*pi()/180))))*180/pi())*60*1.1515*1.609344) as distance'
            )
            ->having('distance', '<', $distance)
            ->orderBy('distance');

        // Haal de resultaten op
        $results = $cooksQuery->get();

        $resultCollection = new Collection;

        if (is_null($from)) {
            $from = Carbon::now();
        } else {
            $from = Carbon::parse($from);
        }

        if (is_null($to)) {
            $to = Carbon::now()->addMonths(2);
        } else {
            $to = Carbon::parse($to);
        }

        foreach ($results as $result) {
            $cook = $this->cook
                ->whereHas('adverts', function ($query) use ($from, $to, $filters) {
                    $query
                        ->whereNotNull('published');
                    if ($filters) {
                        $query->where(function ($query) use ($from, $to) {
                            $query->where('pickup_date', '>=', $from->format('Y-m-d'))
                                ->where('pickup_date', '<=', $to->format('Y-m-d'));
                        });
                    } else {
                        $query->
                            where(function ($query) use ($from, $to) {
                                $query->where('order_date', '>=', $from->format('Y-m-d'))
                                    ->where('order_date', '<=', $to->format('Y-m-d'));
                            })
                                ->orWhere(function ($query) use ($from, $to) {
                                    $query->where('pickup_date', '>=', $from->format('Y-m-d'))
                                        ->where('pickup_date', '<=', $to->format('Y-m-d'));
                                });
                    }
                })
                ->with('adverts.cook.user.reviews')
                ->with('adverts.dish.image')
                ->with('adverts.order')
                ->with('adverts.dish.images')
                ->find($result->uuid);

            if (! is_null($cook)) {
                /** @var Advert $advert */
                foreach ($cook->adverts as $key => $advert) {
                    $keepDish = Carbon::parse($advert->getPickupDate())->between($from, $to);

                    if (! is_null($specs)) {
                        $specTotal = count($specs);
                        $keep = 0;

                        /** @var Dish $dish */
                        $dish = $advert->dish;

                        foreach ($specs as $spec) {
                            if (
                                $spec == 'vegetarisch' &&
                                $dish->isVegetarian()
                            ) {
                                $keep++;
                            }

                            if (
                                $spec == 'glutenvrij' &&
                                $dish->hasGluten()
                            ) {
                                $keep++;
                            }

                            if (
                                $spec == 'veganistisch' &&
                                $dish->isVegan()
                            ) {
                                $keep++;
                            }

                            if (
                                $spec == 'halal' &&
                                $dish->isHalal()
                            ) {
                                $keep++;
                            }

                            if (
                                $spec == 'lactosevrij' &&
                                $dish->hasLactose()
                            ) {
                                $keep++;
                            }

                            if (
                                $spec == 'bevat alcohol' &&
                                $dish->hasAlcohol()
                            ) {
                                $keep++;
                            }
                        }

                        if ($specTotal !== $keep) {
                            $keepDish = false;
                        }

                        if (
                            $specTotal != 0 &&
                            $keep == 0
                        ) {
                            $keepDish = false;
                        }
                    }

                    if (
                        is_null($advert->published()) ||
                        ! is_null($minPrice) && $advert->getPortionPrice() < $minPrice ||
                        ! $keepDish
                    ) {
                        $cook->adverts->forget($key);

                        continue;
                    }

                    if (! is_null($maxPrice) && $advert->getPortionPrice() > $maxPrice) {
                        $cook->adverts->forget($key);

                        continue;
                    }

                    // Hieronder wordt gekeken of de advertentie wel of niet beschikbaar is en of de advertentie wel of niet gepubliceerd is.
                    // Ik heb het zo gedaan omdat ik het overzichtelijker vond dan in een keer een hele lange if statement te maken.
                    // Als het door de if statement komt, dan wordt de advertentie toegevoegd aan de Advertentie lijst.

                    if ($status !== null && count($status) === 1 && in_array('expired', $status)) {
                        // geef advertentie die over die over de uiterlijke bestelmoment zijn

                        if ($advert->published() && $advert->getParsedOrderTo()->isPast()) {
                            // voeg de advertentie toe aan de resultCollection
                            $advert->setDistance($result->distance);
                            $advert->cook->setDistance($result->distance);
                            $resultCollection->add($advert);
                        }
                    } elseif ($status !== null && count($status) === 1 && in_array('available', $status)) {
                        if ($advert->published() && $advert->getParsedOrderTo()->isFuture() && $advert->getLeftOverAmount() > 0) {
                            // voeg de advertentie toe aan de resultCollection
                            $advert->setDistance($result->distance);
                            $advert->cook->setDistance($result->distance);
                            $resultCollection->add($advert);
                        }

                    } elseif ($status !== null && count($status) === 1 && in_array('soldout', $status)) {
                        if ($advert->published() && $advert->getParsedOrderTo()->isFuture() && $advert->getLeftOverAmount() === 0) {
                            // voeg de advertentie toe aan de resultCollection
                            $advert->setDistance($result->distance);
                            $advert->cook->setDistance($result->distance);
                            $resultCollection->add($advert);
                        }

                    } else {
                        // voeg de advertentie toe aan de resultCollection
                        $advert->setDistance($result->distance);
                        $advert->cook->setDistance($result->distance);
                        $resultCollection->add($advert);

                    }

                }
            }
        }

        return $resultCollection;
    }

    public function findCookSubscribersByCookUuid(string $cookUuid): ?Collection
    {
        $cook = $this->cook->find($cookUuid);

        if (is_null($cook)) {
            return null;
        }

        return $cook->clients;
    }
}
