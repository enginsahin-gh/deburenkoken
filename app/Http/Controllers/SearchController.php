<?php

namespace App\Http\Controllers;

use App\Repositories\CookRepository;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SearchController extends Controller
{
    private Request $request;

    private CookRepository $cookRepository;

    public function __construct(
        Request $request,
        CookRepository $cookRepository
    ) {
        $this->request = $request;
        $this->cookRepository = $cookRepository;
    }

    public function searchByCoordinates(): View|RedirectResponse
    {
        $this->request->validate([
            'plaats' => ['required'],
            'distance' => ['nullable', 'numeric'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'city' => ['nullable', 'string'],
            'to' => ['nullable', 'date'],
            'price_from' => ['nullable', 'numeric'],
            'price_to' => ['nullable', 'numeric'],
            'sorting' => ['nullable', 'string'],
            'specs' => ['nullable', 'array'],
            'status' => ['nullable', 'array'],
            'reset' => ['nullable', 'boolean'],
            'searching' => ['nullable', 'string'],
        ]);

        $place = $this->request->input('plaats');
        $distance = $this->request->input('distance');
        $latitude = $this->request->input('latitude');
        $longitude = $this->request->input('longitude');
        $city = $this->request->input('city');
        $from = $this->request->input('from', Carbon::now()->format('Y-m-d'));
        $to = $this->request->input('to');
        $fromPrice = $this->request->input('price_from', 0);
        $toPrice = $this->request->input('price_to');
        $sorting = $this->request->input('sorting', 'distance');
        $specs = $this->request->input('specs');
        $status = $this->request->input('status');

        if ((bool) $this->request->query('reset')) {
            $fromPrice = 0;
            $toPrice = null;
            $to = null;
            $distance = '100';
        }

        if (is_null($latitude) || is_null($longitude)) {
            $this->request->validate(['searching' => 'required']);
        }

        $this->request->session()->put('latitude', $latitude);
        $this->request->session()->put('longitude', $longitude);

        $filters = false;
        if (! is_null($to)) {
            $filters = true;
        }

        $cooksAdverts = $this->cookRepository->findCookWithDishes(
            $latitude,
            $longitude,
            $distance,
            $from,
            $to,
            $fromPrice,
            $toPrice,
            $specs,
            $status,
            $filters
        );

        $sortedAdverts = $cooksAdverts->sortBy($sorting);

        return view('customer.available', [
            'adverts' => $this->paginate($sortedAdverts)
                ->withPath(route('search.coordinates'))
                ->appends($this->request->except(['page', '_token', '_method'])),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'distance' => $distance,
            'place' => $place,
            'selected' => $sorting,
            'from' => $from,
            'to' => $to,
            'price_from' => $fromPrice,
            'price_to' => $toPrice,
            'specs' => $specs,
            'status' => $status,
            'searchString' => http_build_query([
                'plaats' => $place,
                'distance' => $distance,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'city' => $city,
                'from' => $from,
                'to' => $to,
                'price_from' => $fromPrice,
                'price_to' => $toPrice,
                'sorting' => $sorting,
            ]),
        ]);
    }

    public function searchByCookName(): View
    {
        $username = $this->request->input('username', '');
        $cooks = $this->cookRepository->findCookByUserName($username, $this->request->query('page'));

        $noResults = $cooks->isEmpty();

        return view('search-cooks', [
            'cooks' => $cooks,
            'userName' => $username,
            'place' => $this->request->input('city'),
            'noResults' => $noResults,
        ]);
    }

    public function searchCookByDistance(): View
    {
        $lat = $this->request->query('latitude', 52.09073739999999);
        $long = $this->request->query('longitude', 5.1214201);
        $distance = $this->request->query('distance', 10000);
        $city = $this->request->query('city');

        $this->request->session()->put('latitude', $lat);
        $this->request->session()->put('longitude', $long);

        $cooks = $this->cookRepository->findCookByCoordinates($lat, $long, $distance);

        $noResults = $cooks->isEmpty();

        $sortedCooks = $cooks->sortBy('distance');

        return view('search-cooks-distance', [
            'cooks' => $this->paginate($sortedCooks)
                ->withPath(route('search.cooks.distance'))
                ->appends($this->request->except(['page', '_token', '_method'])),
            'city' => $city,
            'lat' => $lat,
            'long' => $long,
            'distance' => $distance,
            'noResults' => $noResults,
        ]);
    }

    public function showCookDetails(string $uuid): View
    {
        $cook = $this->cookRepository->find($uuid);

        $showSoldPortions = ($cook->user->privacy ?? null) ? $cook->user->privacy->showSoldPortions() : false;

        $filteredAdverts = $cook->adverts->filter(function ($advert) {
            return $advert->published() &&
                   $advert->getParsedPickupTo()->isFuture();
        });

        return view('detail-cook', [
            'cook' => $cook,
            'privacy' => $cook->user->privacy,
            'description' => $cook->user->profileDescription,
            'distanceFromUser' => $this->request->query('distance-from-user'),
            'searchString' => $this->request->getQueryString(),
            'showSoldPortions' => $showSoldPortions,
            'adverts' => $this->paginate($filteredAdverts, 10)
                ->withPath(route('search.cooks.detail', ['uuid' => $uuid]))
                ->appends($this->request->except(['page', '_token', '_method'])),
        ]);
    }

    public function showCookReviews(string $uuid): View
    {
        return view('review-cook', [
            'cook' => $this->cookRepository->find($uuid),
        ]);
    }

    public function paginate($items, $perPage = 20, $page = null, $options = []): LengthAwarePaginator
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
