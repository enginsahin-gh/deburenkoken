@extends('layout.main')

@section('content')
    <section class="clearfix mt-3">
        <div class="container">
            <div class="row">
                <div class="col-2">
                    <a href="{{route('search.cooks.detail', $cook->getUuid())}}" class="btn-light order-button text-center">Terug</a>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-10">
                    <div class="row">
                        <div class="col-4">
                            <div class="row">
                                <div class="col-4">
                                    <div class="round-image-container">
                                        <img src="{{ $cook->user->image?->getCompletePath() ?? url('/img/kok.png') }}">
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="row">{{$cook->user->getUsername()}}</div>
                                    <div class="row d-inline star-font">
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
                                        ({{$cook->user->reviews->count()}})
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <h2>{{$dish->getTitle()}}</h2>
                        </div>
                        <div class="col-3">
                            @for($i = 0; $i < $dish->getSpiceLevel(); $i++)
                                <i class="fa-solid fa-pepper-hot"></i>
                            @endfor
                            @for($i = 0 + $dish->getSpiceLevel(); $i < 3; $i++)
                                <i class="fa-thin fa-pepper-hot"></i>
                            @endfor
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <img src="{{ $dish->image?->getCompletePath() ?? url('/img/pasta.jpg') }}">
                        </div>
                    </div>
                    <div class="row">
                        @if ($dish->isVegetarian()) <span class="round-item round-grey float-right">Vegetarisch</span> @endif
                        @if ($dish->isVegan()) <span class="round-item round-grey float-right">Veganistisch</span> @endif
                        @if ($dish->isHalal()) <span class="round-item round-grey float-right">Halal</span> @endif
                        @if ($dish->hasAlcohol()) <span class="round-item round-grey float-right">Alcohol</span> @endif
                        @if ($dish->hasGluten()) <span class="round-item round-grey float-right">Glutenvrij</span> @endif
                        @if ($dish->hasLactose()) <span class="round-item round-grey float-right">Lactosevrij</span> @endif
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <h5>Omschrijving</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            {{$dish->getDescription()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection