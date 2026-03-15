@extends('layout.main')
@section('content')
    <div class="page-header">
        <div class="container"><h1>Thuiskok</h1></div>
    </div>
    <div class="container">
        <div class="cook-searchFilter">
            <form class="row" action="{{route('search.cooks.distance')}}" method="GET" id="searchCook">
                @csrf
                <div class="col-4">
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

                <div class="col-2">
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
                <div class="col-2">
                    <button type="submit" id="zoeken" class="btn btn-light" style="display: none;">Zoeken</button>
                </div>

                <div class="col-4 text-right">
                    <a href="{{route('search.cooks')}}"><small>Of zoek op naam -></small></a><br>
                    <a href="{{route('home')}}"><small>Of zoek advertenties -></small></a>
                </div>
            </form>
        </div>
        <div class="container">
            @if((request()->has('searching') && request()->has('place') && $cooks->isEmpty()) || 
                (request()->has('place') && !request()->has('searching') && $cooks->isEmpty()))
                <div class="alert alert-warning text-center mt-4">
                    <strong>Er zijn geen resultaten gevonden voor deze zoekopdracht.</strong>
                </div>
            @endif
        </div>
        @foreach($cooks as $cook)
            <a href="{{route('search.cooks.detail', ['uuid' => $cook->getUuid(), 'distance-from-user' => $cook->getDistance()])}}" class="cook-row">
                <div class="row">
                    <div class="col-2">
                        <img src="{{ $cook->user->image?->getCompletePath() ?? url('/img/kok.png') }}">
                        <!-- <img src="../../public/img/eleven.jpg" /> -->
                    </div>
                    <div class="col-8">
                        <div class="row">
                            <div class="col-6">
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
                            <div class="col-3">
                                <h4 class="mb-1">{{$cook->user->getUsername()}}</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-8">
                                <p>{{(strlen($cook->user->profileDescription?->getDescription()) > 150) ? substr($cook->user->profileDescription?->getDescription(),0,150).'...' : $cook->user->profileDescription?->getDescription()}}</p>
                                <!-- <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</p> -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4 font-13">
                                <i class="fa fa-map-marker"></i> {{$cook->getCity()}}
                            </div>
                            <div class="col-2">
                                @if(!is_null($cook->getDistance()))
                                    <i class="fa fa-map-marker"></i> @if ($cook->getDistance() < 1) < @endif {{ceil($cook->getDistance())}} km
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
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