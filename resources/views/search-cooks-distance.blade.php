@extends('layout.main')
@section('content')
    <div class="page-header">
        <div class="container"><h1><i class="fa-solid fa-users" style="margin-right: 8px; opacity: 0.7;"></i> Thuiskoks</h1></div>
    </div>
    <div class="container">
        <div class="cook-searchFilter">
            <form class="row" action="{{route('search.cooks.distance')}}" method="GET" id="searchCook">
                @csrf
                <div class="col-md-4 col-12">
                    <input type="text" name="place" id="autocomplete" placeholder="Zoek een Thuiskok op locatie" value="{{$city}}">
                    <i class="fa-solid fa-magnifying-glass magnifying-glass pointer" aria-hidden="true" style="margin-top: -41px;" id='searchButton'></i>
                </div>

                <div class="form-group d-none" id="lat_area">
                    <label for="latitude">Latitude </label>
                    <input type="text" name="latitude" id="latitude" class="form-control" value="{{$lat}}">
                </div>

                <input type="hidden" name="client" id="client" value="false">
                <div class="form-group d-none" id="long_area">
                    <label for="longitude">Longitude </label>
                    <input type="text" name="longitude" id="longitude" class="form-control" value="{{$long}}">
                </div>

                <div class="form-group d-none">
                    <input type="text" name="city" id="city" value="{{$city}}">
                </div>

                <div class="col-md-2 col-12">
                    <select name="distance" id="distance" class="form-control mt-10">
                        <option value="10000"  @if($distance === '10000') selected @endif>Alle afstanden</option>
                        <option value="1" @if($distance === '1') selected @endif><1km</option>
                        <option value="5" @if($distance === '5') selected @endif><5km</option>
                        <option value="10" @if($distance === '10') selected @endif><10km</option>
                        <option value="25" @if($distance === '25') selected @endif><25km</option>
                        <option value="50" @if($distance === '50') selected @endif><50km</option>
                        <option value="100" @if($distance === '100') selected @endif><100km</option>
                    </select>
                </div>
                <div class="col-md-2 col-12">
                    <button type="submit" id="zoeken" class="btn btn-light" style="display: none;">Zoeken</button>
                </div>

                <div class="col-md-4 col-12 text-right">
                    <a href="{{route('search.cooks')}}"><small>Of zoek op naam →</small></a><br>
                    <a href="{{route('home')}}"><small>Of zoek advertenties →</small></a>
                </div>
            </form>
        </div>
        <div class="container">
            @if((request()->has('searching') && request()->has('place') && $cooks->isEmpty()) || 
                (request()->has('place') && !request()->has('searching') && $cooks->isEmpty()))
                <div class="alert alert-warning text-center mt-4">
                    <i class="fa-solid fa-circle-info" style="margin-right: 6px;"></i>
                    <strong>Er zijn geen resultaten gevonden voor deze zoekopdracht.</strong>
                </div>
            @endif
        </div>
        @foreach($cooks as $cook)
            <a href="{{route('search.cooks.detail', ['uuid' => $cook->getUuid(), 'distance-from-user' => $cook->getDistance()])}}" class="cook-row">
                <div class="row align-center">
                    <div class="col-md-2 col-12 text-center">
                        <img src="{{ $cook->user->image?->getCompletePath() ?? url('/img/kok.png') }}" alt="{{ $cook->user->getUsername() }}">
                    </div>
                    <div class="col-md-8 col-12">
                        <div class="row mb-1">
                            <div class="col-12">
                                <h4 class="mb-1">{{$cook->user->getUsername()}}</h4>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-12">
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
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <p>{{(strlen($cook->user->profileDescription?->getDescription()) > 150) ? substr($cook->user->profileDescription?->getDescription(),0,150).'...' : $cook->user->profileDescription?->getDescription()}}</p>
                            </div>
                        </div>
                        <div class="row align-center" style="gap: 12px;">
                            <div class="font-13">
                                <i class="fa fa-map-marker" style="color: var(--dbk-accent);"></i> {{$cook->getCity()}}
                            </div>
                            <div>
                                @if(!is_null($cook->getDistance()))
                                    <span style="background: rgba(45,106,79,0.1); padding: 2px 10px; border-radius: 24px; font-size: 0.8rem; color: var(--dbk-primary); font-weight: 600;">
                                        <i class="fa fa-location-dot"></i> @if ($cook->getDistance() < 1) < @endif {{ceil($cook->getDistance())}} km
                                    </span>
                                @endif
                            </div>
                            <div>
                                <span class="round-item orange">
                                    {{count(array_filter(
                                        $cook->adverts->map(function (\App\Models\Advert $advert) {
                                            return !$advert->isPublished() && $advert->getParsedPickupTo() > \Carbon\Carbon::now();
                                        })->toArray()))
                                    }} advertenties online
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
        {{$cooks->links()}}
    </div>
@endsection

@section('page.scripts')
    @include('layout.scripts.google')
@endsection
