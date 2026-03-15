@extends('layout.main')
@section('content')
    <div class="dbk-page-header">
        <div class="container"><h1><i class="fa-solid fa-users"></i> Thuiskoks</h1></div>
    </div>
    <div class="container dbk-search-page">
        <div class="dbk-filter-bar">
            <form class="dbk-filter-form" action="{{route('search.cooks.distance')}}" method="GET" id="searchCook">
                @csrf
                <div class="dbk-filter-input-wrap">
                    <input type="text" name="place" id="autocomplete" placeholder="Zoek een Thuiskok op locatie" value="{{$city}}" class="dbk-filter-input">
                    <button type="button" class="dbk-filter-search-icon" id="searchButton"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>

                <div class="form-group d-none" id="lat_area">
                    <label for="latitude">Latitude</label>
                    <input type="text" name="latitude" id="latitude" class="form-control" value="{{$lat}}">
                </div>
                <input type="hidden" name="client" id="client" value="false">
                <div class="form-group d-none" id="long_area">
                    <label for="longitude">Longitude</label>
                    <input type="text" name="longitude" id="longitude" class="form-control" value="{{$long}}">
                </div>
                <div class="form-group d-none">
                    <input type="text" name="city" id="city" value="{{$city}}">
                </div>

                <select name="distance" id="distance" class="dbk-filter-select">
                    <option value="10000" @if($distance === '10000') selected @endif>Alle afstanden</option>
                    <option value="1" @if($distance === '1') selected @endif>&lt;1km</option>
                    <option value="5" @if($distance === '5') selected @endif>&lt;5km</option>
                    <option value="10" @if($distance === '10') selected @endif>&lt;10km</option>
                    <option value="25" @if($distance === '25') selected @endif>&lt;25km</option>
                    <option value="50" @if($distance === '50') selected @endif>&lt;50km</option>
                    <option value="100" @if($distance === '100') selected @endif>&lt;100km</option>
                </select>

                <button type="submit" id="zoeken" class="dbk-filter-btn" style="display: none;">Zoeken</button>

                <div class="dbk-filter-links">
                    <a href="{{route('search.cooks')}}">Of zoek op naam →</a>
                    <a href="{{route('home')}}">Of zoek advertenties →</a>
                </div>
            </form>
        </div>

        @if((request()->has('searching') && request()->has('place') && $cooks->isEmpty()) || 
            (request()->has('place') && !request()->has('searching') && $cooks->isEmpty()))
            <div class="dbk-empty-state">
                <i class="fa-solid fa-map-location-dot"></i>
                <h3>Geen koks gevonden in deze buurt</h3>
                <p>Probeer een andere locatie of vergroot je zoekafstand!</p>
            </div>
        @endif

        <div class="dbk-cook-grid">
            @foreach($cooks as $cook)
                <a href="{{route('search.cooks.detail', ['uuid' => $cook->getUuid(), 'distance-from-user' => $cook->getDistance()])}}" class="dbk-cook-card">
                    <div class="dbk-cook-card-avatar">
                        <img src="{{ $cook->user->image?->getCompletePath() ?? url('/img/kok.png') }}" alt="{{ $cook->user->getUsername() }}">
                    </div>
                    <div class="dbk-cook-card-info">
                        <h4>{{$cook->user->getUsername()}}</h4>
                        <div class="dbk-cook-card-rating">
                            @php $rating = $cook->user->reviews->avg('rating') ?? 0; @endphp
                            @foreach(range(1,5) as $i)
                                <span class="fa-stack" style="width:1em">
                                    @if($rating <= 0)
                                        <i class="far fa-star fa-stack-1x"></i>
                                    @endif
                                    @if($rating > 0)
                                        @if($rating >0.5)
                                            <i class="fas fa-star fa-stack-1x"></i>
                                        @else
                                            <i class="fas fa-star-half fa-stack-1x"></i>
                                        @endif
                                    @endif
                                    @php $rating--; @endphp
                                </span>
                            @endforeach
                            <span class="review-count">({{$cook->user->reviews->count()}})</span>
                        </div>
                        <p class="dbk-cook-card-desc">{{(strlen($cook->user->profileDescription?->getDescription()) > 150) ? substr($cook->user->profileDescription?->getDescription(),0,150).'...' : $cook->user->profileDescription?->getDescription()}}</p>
                        <div class="dbk-cook-card-meta">
                            <span class="dbk-meta-location"><i class="fa-solid fa-location-dot"></i> {{$cook->getCity()}}</span>
                            @if(!is_null($cook->getDistance()))
                                <span class="dbk-meta-distance"><i class="fa-solid fa-route"></i> @if ($cook->getDistance() < 1) &lt; @endif {{ceil($cook->getDistance())}} km</span>
                            @endif
                            <span class="dbk-meta-adverts dbk-pill-orange">
                                <i class="fa-solid fa-utensils"></i>
                                {{count(array_filter(
                                    $cook->adverts->map(function (\App\Models\Advert $advert) {
                                        return !$advert->isPublished() && $advert->getParsedPickupTo() > \Carbon\Carbon::now();
                                    })->toArray()))
                                }} advertenties online
                            </span>
                        </div>
                        <span class="dbk-view-profile">Bekijk profiel <i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="dbk-pagination">
            {{$cooks->links()}}
        </div>
    </div>
@endsection

@section('page.scripts')
    @include('layout.scripts.google')
@endsection
