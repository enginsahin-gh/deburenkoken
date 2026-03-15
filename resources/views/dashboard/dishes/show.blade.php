@extends('layout.dashboard')

@section('dashboard')
    <div class="breadcrumbs">
        <div class="container">
            <a href="{{route('home')}}">Home</a> 
            <a href="{{url()->previous()}}">Terug</a>
        </div>
    </div>

    <div class="container pt-10 single-dish">
        <div class="row">
            <div class="col-4">
                <div class="img-holder">
                    <!-- <img src="{{asset('img/five.jpg')}}" /> -->
                    <img src="{{ $dish->image?->getCompletePath() ?? url('/img/pasta.jpg') }}">
                </div>  
            </div>

            <div class="col-4">
                <div class="row align-center m-0">
                    <h2 class="singledish-title">{{$dish->getTitle()}}</h2>
                                <div>
                    @for($i = 0; $i < $dish->getSpiceLevel(); $i++)
                        <i class="fa-solid fa-pepper-hot" style="color: #dc3545; margin-right: 2px;"></i>
                    @endfor
                    @for($i = 0 + $dish->getSpiceLevel(); $i < 3; $i++)
                        <i class="fa-solid fa-pepper-hot" style="color: #ccc; opacity: 0.3; margin-right: 2px;"></i>
                    @endfor
                </div>
                </div>
                    <div class="row">
                        <div class="col-12 single-descr">
                            <p><strong style="color: #000;">Prijs per portie</strong></p>
                            <p style="color: #000;">€{{ number_format($dish->getPortionPrice(), 2, ',', '.') }}</p>
                        </div>
                    </div>
                <div class="row">
                    <div class="col-12 single-descr">
                        <p><strong>Omschrijving</strong></p>
                        <p>{{$dish->getDescription()}}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        @if($dish->isVegetarian())<span class="has-marker vegetarian">Vegetarisch</span>@endif
                        @if($dish->isVegan())<span class="has-marker vegan">Vegan</span>@endif
                        @if($dish->isHalal())<span class="has-marker halal">Halal</span>@endif
                        @if($dish->hasAlcohol())<span class="has-marker alcohol">Alcohol</span>@endif
                        @if($dish->hasLactose())<span class="has-marker lactose">Lactosevrij</span>@endif
                        @if($dish->hasGluten())<span class="has-marker gluten">Glutenvrij</span>@endif
                    </div>
                </div>
                @if(request()->user()->hasRole('admin'))
                    <div class="row">
                        <div class="col-12">
                            <a href="{{route('dashboard.admin.dishes.edit', $dish->getUuid())}}" class="btn btn-light btn-small">Wijzig gerecht</a>
                        </div>
                    </div>
                @elseif(request()->user()->hasRole('cook') && $dish->getUserUuid() === request()->user()->getUuid())
                    <div class="row mt-3">
                        <div class="col-12">
                            @if($dish->hasActiveAdvert())
                                <p class="text-muted"><small>Aanpassen niet mogelijk: gerecht staat in een actieve advertentie.</small></p>
                            @else
                                <a href="{{ route('dashboard.dishes.edit', $dish->getUuid()) }}" class="btn btn-light btn-small">Aanpassen</a>
                            @endif
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-4">

            </div>
        </div>
    </div>
@endsection