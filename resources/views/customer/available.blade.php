@extends('layout.main')
@section('content')

<button id="filters" class="mobile-only" onclick="showFilters()"><i class="fa fa-bars" aria-hidden="true"></i> FILTERS</button>
<div class="filter sidenav" id="filter-sidebar">
    <div class="filter-content" id="filter-content">
        <div class="hide-button mobile-only font-25" id="hide-filters" onclick="hideFilters()">X</div>
        <form action="{{route('search.coordinates')}}" method="get">
            @csrf
            <div class="row">
                <div class="col-12">
                    <h2 class="mar-t">Afstand</h2>
                </div>
            </div>
            <div class="form-group d-none">
                <input type="text" id="latitude" name="latitude" value="{{$latitude}}" >
                <input type="text" id="longitude" name="longitude" value="{{$longitude}}">
                <input type="text" id="sortFormInput" name="sorting" value="distance">
                <input type="text" id="place" name="plaats" value="{{$place}}">
                <input type="text" id="place" name="city" value="{{$place}}">
                <input type="text" id="search_string" name="search_string" value="{{$searchString}}">
            </div>
            <div class="row">
                <div class="col-12">
                    <label for="distance">Maximale afstand</label>
                    <select id="distance" name="distance" class="form-control">
                        <option value="1" @if($distance === '1')selected @endif>1km</option>
                        <option value="2" @if($distance === '2')selected @endif>2km</option>
                        <option value="5" @if($distance === '5')selected @endif>5km</option>
                        <option value="10" @if($distance === '10')selected @endif>10km</option>
                        <option value="25" @if($distance === '25')selected @endif>25km</option>
                        <option value="50" @if($distance === '50')selected @endif>50km</option>
                        <option value="100" @if($distance === '100')selected @endif>100km</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h2 class="mar-t">Afhaaldatum</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label for="date_from">Van</label>
                    <input type="date" id="date_from" name="from" value="{{$from}}" class="form-control" min="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-12">
                    <label for="date_to">Tot</label>
                    <input type="date" id="date_to" name="to" value="{{$to}}" class="form-control" min="<?= date('Y-m-d') ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h2 class="mar-t">Prijs per portie</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label for="min">Min</label>
                    <input type="number" id="min" name="price_from" value="{{$price_from}}" step="0.01" class="form-control">
                </div>
                <div class="col-12">
                    <label for="max">Max</label>
                    <input type="number" id="max" name="price_to" value="{{$price_to}}" step="0.01" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h2 class="mar-t">Kenmerken</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <input type="checkbox" id="vegetarisch" name="specs[]" value="vegetarisch" @if(!is_null($specs) && in_array('vegetarisch', $specs)) checked @endif>
                    <label for="vegetarisch" class="checkbox-inline">Vegetarisch</label>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <input type="checkbox" id="glutenvrij" name="specs[]" value="glutenvrij" @if(!is_null($specs) && in_array('glutenvrij', $specs)) checked @endif>
                    <label for="glutenvrij" class="checkbox-inline">Glutenvrij</label>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <input type="checkbox" id="veganistisch" name="specs[]" value="veganistisch" @if(!is_null($specs) && in_array('veganistisch', $specs)) checked @endif>
                    <label for="veganistisch" class="checkbox-inline">Veganistisch</label>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <input type="checkbox" id="halal" name="specs[]" value="halal" @if(!is_null($specs) && in_array('halal', $specs)) checked @endif>
                    <label for="halal" class="checkbox-inline">Halal</label>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <input type="checkbox" id="lactosevrij" name="specs[]" value="lactosevrij" @if(!is_null($specs) && in_array('lactosevrij', $specs)) checked @endif>
                    <label for="lactosevrij" class="checkbox-inline">Lactosevrij</label>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <input type="checkbox" id="alcohol" name="specs[]" value="bevat alcohol" @if(!is_null($specs) && in_array('bevat alcohol', $specs)) checked @endif>
                    <label for="alcohol" class="checkbox-inline">Bevat alcohol</label>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <h2 class="mar-t">Status</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <input type="checkbox" id="beschikbaar" name="status[]" value="available" @if(!is_null($status) && in_array('available', $status)) checked @endif>
                    <label for="beschikbaar" class="checkbox-inline">Beschikbaar</label>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <input type="checkbox" id="uitverkocht" name="status[]" value="soldout" @if(!is_null($status) && in_array('soldout', $status)) checked @endif>
                    <label for="uitverkocht" class="checkbox-inline">Uitverkocht</label>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <label style='width: 150px;' for="bestelmomentVerlopen" class="checkbox-inline"><input style='margin-right: 5px;' type="checkbox" id="bestelmomentVerlopen" name="status[]" value="expired" @if(!is_null($status) && in_array('expired', $status)) checked @endif>Uiterste bestelmoment verlopen</label>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="submit" id="formSubmit" class="btn-light order-button">Filters toepassen</button>
                    
                    <a href="{{route('search.coordinates', [$searchString, 'reset' => true])}}" class="mb-20 btn-light order-button text-center">Reset filters</a>
                </div>
            </div>
        </form>
    </div>
</div>

    <div class="dashboard">
        <div class="page-header">
            <div class="container"><h1>Gerechten</h1></div>
        </div>

        <div class="main filter-wrapper">
            <div class="with-filter container mt-20">
                <div class="row">
                    <div class="col-8"><b>Zoekresultaten voor:</b> {{$place}}</div>
                    <div class="col-4 sort-by-zoe">
                        <label for="sort_by">Sorteren op:&nbsp;&nbsp;&nbsp; </label>
                        <select id="sort_by" onchange="sorting();" name="sort_by">
                            <option value="distance" @if($selected === 'distance') selected @endif>Afstand</option>
                            <option value="pickup_date" @if($selected ==='pickup_date') selected @endif>Datum</option>
                            <option value="portion_price" @if($selected === 'portion_price') selected @endif>Prijs</option>
                        </select>
                    </div>
                </div>

                
                @if($adverts->isEmpty())
                    <div class="row mt-20 mb-20">
                        <div class="col-12">
                            <div class="no-dish">Geen zoekresultaten gevonden</div>
                        </div>
                    </div>
                @endif

                <div class="dish-collection">
                    @foreach($adverts as $advert)
                        @if ($advert->getParsedPickupTo()->isPast())
                            @continue
                        @endif


                        <div class="dish-row">
                            <div class="row top-bgrd">
                                <div class="col-6">
                                    <a href="javascript:void(0);" onclick='checkDish("{{$advert->getUuid()}}", "{{ceil($advert->getDistance())}}")'>
                                        <!-- <img src="{{asset('img/eleven.jpg')}}" class="dish-img" />  -->
                                        <img src="{{ $advert->dish->image?->getCompletePath() ?? url('/img/pasta.jpg') }}" class="dish-img" />
                                        <span class="d-none" id="available-{{$advert->getUuid()}}">@if ($advert->getParsedOrderTo()->isFuture()){{$advert->getLeftOverAmount()}}@else 0 @endif</span>
                                        <span class="nog">                                            
                                            @if ($advert->getParsedOrderTo()->isFuture() && $advert->published() && $advert->getLeftOverAmount() !== 0)
                                                <span class="d-none" id="available-{{$advert->getUuid()}}">
                                            @if ($advert->getParsedOrderTo()->isFuture()){{$advert->getLeftOverAmount()}}@else 0 @endif</span>
                                                Nog {{$advert->getLeftOverAmount()}} beschikbaar
                                            @elseif ($advert->getParsedOrderTo()->isFuture() && $advert->getLeftOverAmount() === 0)
                                                Uitverkocht
                                            @else
                                                Uiterste bestelmoment verlopen
                                            @endif
                                        </span>
                                    </a>
                                </div>

                                <div class="col-6 line">
                                    <div class="row">
                                        <div class="col-4 d-flex align-center justify-center mb-10">                                    
                                            <a href="javascript:void" onclick='checkCook("{{$advert->cook->getUuid()}}", "{{$advert->getDistance()}}")' class="d-flex align-center justify-center">
                                                <div class="round-image-container">
                                                    <img src="{{ $advert->cook->user->image?->getCompletePath() ?? url('/img/kok.png') }}" />
                                                    <!-- <img src="{{asset('img/eleven.jpg')}}" />  -->
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-8">
                                            <a href="javascript:void" onclick='checkCook("{{$advert->cook->getUuid()}}", "{{$advert->getDistance()}}")'>
                                                <b>{{$advert->cook->user->getUsername()}}</b>
                                                <div class="row m-0 star-font">
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
                                            </a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                        @if($advert->getLeftOverAmount() != 0 && $advert->getParsedOrderTo()->isFuture())
                                            <a href="javascript:void(0);" class="order-button text-center rounded-lg" onclick='orderAdvert("{{$advert->getUuid()}}", "{{ceil($advert->getDistance())}}")'>
                                            <span class="button-title">Bestel</span><br>
                                            <span>Nog {{$advert->orderTimeLeft()}} te bestellen!</span>
                                            </a>
                                            @endif
                                            <div class="text-center afhalen">
                                                <b>Afhalen:</b><br/>
                                                {{$advert->getParsedPickupFrom()->translatedFormat('l d F')}} ({{$advert->getParsedPickupFrom()->translatedFormat('H:i')}} - {{$advert->getParsedPickupTo()->translatedFormat('H:i')}})
                                            </div>

                                            <div class="d-flex justify-space-between">
                                                <div class="price">
                                                    € {{$advert->getPortionPrice()}}
                                                </div>
                                                <div class="distance">
                                                    <i class="fa fa-map-marker"></i> @if ($advert->getDistance() < 1) < @endif {{ceil($advert->getDistance())}} km
                                                </div>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row white-bgrd">
                                <div class="col-12">
                                    <div class="d-flex align-center">
                                        <a href="javascript:void(0);" onclick='checkDish("{{$advert->getUuid()}}", "{{ceil($advert->getDistance())}}")'>
                                            <h4 class="mb-1 mr-10">{{$advert->dish->getTitle()}}</h4>
                                        </a>
                                       <div>
                                        @for($i = 0; $i < $advert->dish->getSpiceLevel(); $i++)
                                            <i class="fa-solid fa-pepper-hot" style="color: #dc3545; margin-right: 2px;"></i>
                                        @endfor
                                        @for($i = 0 + $advert->dish->getSpiceLevel(); $i < 3; $i++)
                                            <i class="fa-solid fa-pepper-hot" style="color: #ccc; opacity: 0.3; margin-right: 2px;"></i>
                                        @endfor
                                    </div>
                                    </div>
                                    <div class="types">
                                        @if ($advert->dish->isVegetarian()) <span class="round-item round-grey float-left" title="Vegetarisch"><img src="{{asset('img/types/vegetarian.svg')}}" /></span> @endif
                                        @if ($advert->dish->isVegan()) <span class="round-item round-grey float-left" title="Veganistisch"><img src="{{asset('img/types/vegan.svg')}}" /></span> @endif
                                        @if ($advert->dish->isHalal()) <span class="round-item round-grey float-left" title="Halal"><img src="{{asset('img/types/halal.svg')}}" /></span> @endif
                                        @if ($advert->dish->hasAlcohol()) <span class="round-item round-grey float-left" title="Alcohol"><img src="{{asset('img/types/alcohol.svg')}}" /></span> @endif
                                        @if ($advert->dish->hasGluten()) <span class="round-item round-grey float-left" title="Glutenvrij"><img src="{{asset('img/types/gluten-free.svg')}}" /></span> @endif
                                        @if ($advert->dish->hasLactose()) <span class="round-item round-grey float-left" title="Lactosevrij"><img src="{{asset('img/types/dairy.svg')}}" /></span> @endif
                                    </div>
                                    <div class="description">
                                        {{(strlen($advert->dish->getDescription()) > 150) ? substr($advert->dish->getDescription(),0,150).'...' : $advert->dish->getDescription()}}
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                    @endforeach
                    {{$adverts->links()}}
                </div>
            </div>
        </div>
        
    </div>
@endsection

@section('page.scripts')
    <script>
        const sortingItem = document.getElementById('sort_by');
        const sortingForm = document.getElementById('sortFormInput');
        const form = document.getElementById('filterForm');
        const formSubmit = document.getElementById('formSubmit');
        const searchString = document.getElementById('search_string').value;
        const filterSideBar = document.getElementById('filter-content');
        const filterSideBar2 = document.getElementById('filter-sidebar');
        const filterButton = document.getElementById('filters');
        let order = false;

        function sorting()
        {
            sortingForm.value = sortingItem.value;

            formSubmit.click();
        }

        function orderAdvert(uuid, distance)
        {
            const available = Number(document.getElementById('available-' + uuid).innerText);

            if (available !== 0) {
                order = true;
                window.location.href = '/details/' + uuid + '/order?calculatedDistance=' + distance + '&' + searchString;
            }
        }

        function checkDish(uuid, distance)
        {
            if (!order) {
                window.location.href = '/details/' + uuid + '?calculatedDistance=' + distance + '&' + searchString;
            }
        }

        function checkCook(uuid, distance)
        {
            window.location.href = '/search/cooks/' + uuid + '/details?searchString=' + searchString + '&distance-from-user=' + distance;
        }
        function hideFilters()
        {
            filterSideBar2.classList.remove("slide");
            filterButton.style.display = 'block';
        }

        function showFilters()
        {
            filterButton.style.display = 'none';
            filterSideBar2.classList.add("slide");
        }
    </script>

    <script>
        $(document).ready(function(){
            //On click of white-bgrd click the dish title
            $(document).on("click", ".row.white-bgrd" , function() {
                $(this).find("a").click();
            });
            var dashboard = document.getElementsByClassName('dashboard')[0];
            var marginHeight = getComputedStyle(document.getElementsByClassName('dish-collection')[0]).getPropertyValue("margin-bottom");

            filterSideBar2.style.height = dashboard.offsetHeight+ 10 + parseInt(marginHeight) + 'px';
        });
    </script>
@endsection