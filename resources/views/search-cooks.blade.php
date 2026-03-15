@extends('layout.main')
@section('content')
    <div class="dbk-page-header">
        <div class="container"><h1><i class="fa-solid fa-users"></i> Thuiskoks</h1></div>
    </div>

    <div class="container dbk-search-page">
        <div class="dbk-filter-bar">
            <form class="dbk-filter-form" action="{{route('search.cooks')}}" method="GET" id="searchCook">
                @csrf
                <div class="dbk-filter-input-wrap">
                    <label for="name"></label>
                    <input type="text" name="username" id="name" value="{{$userName}}" placeholder="Zoek een Thuiskok op gebruikersnaam" class="dbk-filter-input">
                    <button type="button" class="dbk-filter-search-icon"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
                <button type="submit" id="zoeken" class="dbk-filter-btn">Zoek</button>
                <div class="dbk-filter-links">
                    <a href="{{route('search.cooks.distance')}}">Of zoek een Thuiskok op locatie →</a>
                    <a href="{{route('home')}}">Of zoek advertenties →</a>
                </div>
            </form>
        </div>

        @if(request()->has('username') && $cooks->isEmpty())
            <div class="dbk-empty-state">
                <i class="fa-solid fa-search"></i>
                <h3>Geen zoekresultaten gevonden</h3>
                <p>Probeer een andere zoekterm of bekijk alle thuiskoks.</p>
            </div>
        @endif

        <div class="dbk-cook-grid">
            @foreach($cooks as $cook)
                <a href="{{route('search.cooks.detail', $cook->getUuid())}}" class="dbk-cook-card">
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
                            <span class="dbk-meta-location"><i class="fa fa-map-marker"></i> {{$cook->getCity()}}</span>
                            @if(!is_null($cook->getDistance()))
                                <span class="dbk-meta-distance"><i class="fa fa-location-dot"></i> @if ($cook->getDistance() < 1) &lt; @endif {{ceil($cook->getDistance())}} km</span>
                            @endif
                            <span class="dbk-meta-adverts">
                                {{count(array_filter(
                                    $cook->adverts->map(function (\App\Models\Advert $advert) {
                                        return !$advert->isPublished() && $advert->getParsedPickupTo() > \Carbon\Carbon::now();
                                    })->toArray()))
                                }} advertenties online
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="dbk-pagination">
            {{$cooks->links('pagination::bootstrap-4')}}
        </div>
    </div>
@endsection

@section('page.scripts')
    @include('layout.scripts.google')
@endsection
