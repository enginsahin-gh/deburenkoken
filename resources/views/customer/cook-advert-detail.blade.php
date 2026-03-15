@extends('layout.main')

@section('content')
<style>
    .order-button {
    border-radius: 6px !important;
}
</style>
    <div class="page-header">
        <div class="container"><h1>{{$advert->dish->getTitle()}}</h1></div>
    </div>
    <section class="clearfix single-dish">
        
        <div class="breadcrumbs">
            <div class="container">
                <a href="{{route('home')}}">Home</a> 
                <a href="{{route('search.cooks.detail', $cook->getUuid())}}">Terug</a>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-4">
                    <div class="single-img">
                        <img src="{{ $advert->dish->image?->getCompletePath() ?? url('/img/pasta.jpg') }}" />
                        <!-- <img src="{{asset('img/five.jpg')}}" />  -->
                        <div class="nog-content">
                            @if($advert->getParsedOrderTo()->isPast())
                                Uiterste bestelmoment verlopen
                            @else
                                @if($advert->getLeftOverAmount() == 0)
                                    Uitverkocht
                                @else
                                    Nog {{$advert->getLeftOverAmount()}} beschikbaar
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-5"> 
                    <div class="details">
                        <div class="row mb-10">
                            <div class="col-12">
                                <p class="m-0">{{$advert->getParsedAdvertUuid()}}</p>
                            </div>
                        </div>
                    </div>            
                    <div class="col-12 cook-title">
                        <h2>{{$advert->dish->getTitle()}}</h2>
                        <div>
                            @for($i = 0; $i < $advert->dish->getSpiceLevel(); $i++)
                                <i class="fa-solid fa-pepper-hot" style="color: #dc3545; margin-right: 2px;"></i>
                            @endfor
                            @for($i = 0 + $advert->dish->getSpiceLevel(); $i < 3; $i++)
                                <i class="fa-solid fa-pepper-hot" style="color: #ccc; opacity: 0.3; margin-right: 2px;"></i>
                            @endfor
                        </div>
                    </div>   
                    
                    <div class="row mt-10">
                        <div class="col-12">
                            <div class="types">
                                @if ($advert->dish->isVegetarian()) <span class="round-item round-grey float-left" title="Vegetarisch"><img src="{{asset('img/types/vegetarian.svg')}}" /></span> @endif
                                @if ($advert->dish->isVegan()) <span class="round-item round-grey float-left" title="Veganistisch"><img src="{{asset('img/types/vegan.svg')}}" /></span> @endif
                                @if ($advert->dish->isHalal()) <span class="round-item round-grey float-left" title="Halal"><img src="{{asset('img/types/halal.svg')}}" /></span> @endif
                                @if ($advert->dish->hasAlcohol()) <span class="round-item round-grey float-left" title="Alcohol"><img src="{{asset('img/types/alcohol.svg')}}" /></span> @endif
                                @if ($advert->dish->hasGluten()) <span class="round-item round-grey float-left" title="Glutenvrij"><img src="{{asset('img/types/gluten-free.svg')}}" /></span> @endif
                                @if ($advert->dish->hasLactose()) <span class="round-item round-grey float-left" title="Lactosevrij"><img src="{{asset('img/types/dairy.svg')}}" /></span> @endif
                            </div>
                        </div>
                    </div>  

                        <div class="row mt-10">
                            <div class="col-12">
                                <div class="price">
                                    <span>Prijs:</span> <b>€ {{$advert->getPortionPrice()}}</b>
                                </div>
                            </div>
                        </div>

                    <div class="row mt-10">
                        <div class="col-12 descr">
                            <b>Omschrijving:</b>
                            <p>{{$advert->dish->getDescription()}}</p>
                            <!-- <p>Creame cheese, sushi rice, fish, avacado, raw salmon</p> -->
                        </div>
                    </div>
                           
                    
                    @if (isset($user))
                        @if ($user->cook?->getUuid() === $advert->cook->getUuid())
                            <div class="row">
                                @if (!$advert->isPublished() && $advert->getParsedOrderTo()->isFuture())
                                    <div class="col-6">
                                    <a href="{{route('dashboard.adverts.update', $advert->getUuid())}}" class="order-button text-center" >
                                        Wijzig advertentie
                                    </a>
                                    </div>
                                @else
                                    <div class="col-12">
                                        <span class="alert alert-warning" style="display: block;">Een advertentie kan niet gewijzigd/geannuleerd worden na het uiterste bestelmoment.</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>

                
                <div class="col-3 line">       
                    <a href="{{route('search.cooks.detail', $cook->getUuid())}}" class="d-block align-center justify-center">
                        <div class="row">
                            <div class="col-4 d-flex align-center justify-center mb-10">
                                <div class="round-image-container">
                                    <img src="{{ $cook->user->image?->getCompletePath() ?? url('/img/kok.png') }}">
                                    <!-- <img src="{{asset('img/eleven.jpg')}}" />  -->
                                </div>
                            </div>
                            <div class="col-8 text-left">
                                <b>{{$advert->cook->user->getUsername()}}</b>
                                <div class="row d-inline star-font m-0">
                                    @php $rating = $advert->cook->user->reviews->avg('rating') ?? 0; @endphp
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
                                    <span class="review-count">({{$advert->cook->user->reviews->count()}})</span>
                                </div>
                            </div>
                        </div>    
                    </a>  


                    <div class="row">
                        <div class="col-12">
                            @if (($advert->getLeftOverAmount() != 0) && \Carbon\Carbon::parse($advert->getParsedOrderTo())->isFuture())
                                <form action="{{route('advert.order', $advert->getUuid())}}">
                                    <span class="d-none">
                                        <input type="text" name="searchString" id="searchString" value="{{\Illuminate\Support\Facades\Cookie::get('searchString')}}">
                                    </span>
                                    <button class="order-button text-center">
                                        <span class="button-title">Bestel</span><br>
                                        <span><i>Nog {{$advert->orderTimeLeft()}} te bestellen!</i></span>
                                    </button>
                                </form>
                            @elseif ($advert->getLeftOverAmount() != 0)
                                <button class="order-button text-center">
                                    <span class="button-title">Uiterste bestelmoment verlopen</span>
                                </button>
                            @else
                                <button class="order-button out-of-order">
                                    Uitverkocht
                                </button>
                            @endif
                            <div class="text-center afhalen">
                                <b>Afhaaldatum:</b><br/>
                                {{$advert->getParsedPickupFrom()->translatedFormat('l d-m-Y / H:i -')}} {{$advert->getParsedPickupTo()->translatedFormat('H:i')}}
                            </div>
                           
                            <div class="d-flex justify-center">                       
                                @if (!is_null($distanceFromUser))
                                    <div class="distance">
                                        <i class="fa fa-map-marker"></i> 0 km
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


<!-- 
@section('page.scripts')
    <script src="{{asset('js/product-showcase.js')}}"></script>
    <link rel="stylesheet" href="{{asset('css/product-showcase.css')}}">

    <script>
    $(document).ready(function(){
        $(".product-showcase").productShowcase({
            maxHeight:"630px",	
            width:"100%"
        });
    });
    </script>
@endsection -->