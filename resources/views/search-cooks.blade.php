@extends('layout.main')
@section('content')
<style>
    .main-container {
        position: relative;
        min-height: 400px;
        margin-bottom: 60px;
    }
    .content-wrapper {
        padding-bottom: 40px;
    }
    .pagination-wrapper-custom {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 20px;
    }
    .pagination-custom {
        display: flex;
        justify-content: center;
        gap: 8px;
    }
</style>

<div class="page-header">
    <div class="container"><h1><i class="fa-solid fa-users" style="margin-right: 8px; opacity: 0.7;"></i> Thuiskoks</h1></div>
</div>

<div class="main-container">
    <div class="container">
        <div class="content-wrapper">
            <div class="cook-searchFilter">
                <form class="row" action="{{route('search.cooks')}}" method="GET" id="searchCook">
                    @csrf
                    <div class="col-md-6 col-12">
                        <label for="name"></label>
                        <input type="text" name="username" id="name" value="{{$userName}}" placeholder="Zoek een Thuiskok op gebruikersnaam">
                        <i class="fa-solid fa-magnifying-glass magnifying-glass pointer" aria-hidden="true" style="margin-top: -41px;"></i>
                    </div>
                    <div class="col-md-2 col-12">
                        <button type="submit" id="zoeken" class="btn btn-light">Zoek</button>
                    </div>
                    <div class="col-md-4 col-12 text-right">
                        <a href="{{route('search.cooks.distance')}}"><small>Of zoek een Thuiskok op locatie →</small></a><br>
                        <a href="{{route('home')}}"><small>Of zoek advertenties →</small></a>
                    </div>
                </form>
            </div>

            @if(request()->has('username') && $cooks->isEmpty())
                <div class="alert alert-warning text-center mt-4" role="alert">
                    <i class="fa-solid fa-circle-info" style="margin-right: 6px;"></i>
                    <strong>Geen zoekresultaten gevonden</strong> voor de ingevoerde zoekopdracht.
                </div>
            @endif

            @foreach($cooks as $cook)
                <a href="{{route('search.cooks.detail', $cook->getUuid())}}" class="cook-row">
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
        </div>
        
        <div class="pagination-wrapper-custom">
            {{$cooks->links('pagination::bootstrap-4')}}
        </div>
    </div>
</div>
@endsection

@section('page.scripts')
    @include('layout.scripts.google')
@endsection
