@extends('layout.main')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>.pagination {
    margin-top: 20px;
    text-align: center;
}
/* Stijlen voor validatie feedback */
.email-error {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
    display: block;
}
.form-control.is-invalid {
    border-color: #dc3545;
    padding-right: calc(1.5em + 0.75rem);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}
</style>
    <div class="page-header">
        <div class="container"><h1>{{$cook->user->getUsername()}}</h1></div>
    </div>
    <div class="container single-cook">
        <div class="row">
            <div class="col-1">
                <a href="{{url()->previous()}}" class="tarug">< Terug</a>
            </div>
        </div>

        <div class="row">
            <div class="col-4">
                <div class="product-showcase">
                    <!-- <img src="{{$cook->user->image?->getCompletePath()}}"> -->
                    <div class="ps-icon"
                    data-icon="{{ $cook->user->image?->getCompletePath() ?? url('/img/kok.png') }}"
                    data-image="{{ $cook->user->image?->getCompletePath() ?? url('/img/kok.png') }}"
                    data-selected="true"></div>

                    @foreach($cook->user->images as $image)
                        @if ($image->isMainPicture())
                            @continue;
                        @endif
                        <!-- <img src="{{$image->getCompletePath()}}" alt="{{$image->getDescription()}}"> -->
                        <div class="ps-icon"
                        data-icon="{{$image->getCompletePath()}}" alt="{{$image->getDescription()}}"
                        data-image="{{$image->getCompletePath()}}" alt="{{$image->getDescription()}}"></div>
                    @endforeach
                </div>
            </div>


            <div class="col-8">   
                <div class="row mt-n3">
                    <div class="col-12">
                        <small class="blue-clr">Actief sinds {{$cook->user->getCreatedAt()->translatedFormat('d F Y')}}</small>
                    </div>
                </div>      
                <div class="col-8">
                    <div class="row">
                        <a href="{{route('search.cooks.detail.review', $cook->getUuid())}}">
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
                        </a>                    
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 cook-title">
                        <h2>{{$cook->user->getUsername()}}</h2>
                    </div>                    
                </div>
                
                <div class="details">
                    @if ($privacy?->showStreet() === 3)
                        <div class="row mb-10 single-detail">
                            <p>Adres:</p>
                            <span>{{$cook->getStreet()}} @endif @if($privacy?->showHouseNumber() === 3) {{$cook->getHouseNumber()}} </span>
                        </div>
                    @endif

                    <div class="row mb-10 single-detail">
                        <p>Postcode:</p>
                        <span>{{$cook->getPostalCode()}} {{$cook->getCity()}}</span>
                    </div>
                    @if($privacy?->showSoldPortions() === 3)
                        <div class="row ml-0">
                            <p>Aantal verkochte porties:</p>
                            <span class='pl-1'>{{ $cook->getSoldPortions() }}</span>
                        </div>
                    @endif     
                    @if($privacy?->showEmail() === 3)
                        <div class="row mb-10 single-detail">
                            <p>E-mailaders:</p>
                            <span>{{$cook->user->getEmail()}}</span>
                        </div>
                    @endif
                    @if($privacy?->showPhone() === 3)
                        <div class="row single-detail">
                            <p>Telefoonnummer:</p>
                            <span>{{$cook->user->userProfile->getPhoneNumber()}}</span>
                        </div>
                    @endif
                </div>

                <form action="{{route('customer.cook.subscribe', $cook->getUuid())}}" method="POST" class="subscribe-cook" id="subscribeForm">
                    @csrf
                    <div class="row mt-20">
                        <div class="col-8">
                            <label for="subscriberEmail"><b>Ik ontvang graag een e-mail als {{$cook->user->getUsername()}} nieuwe advertenties plaatst.</b></label>
                        </div>
                        <div class="col-12 form-inline">
                            <div class="col-7 p-0">
                                <input type="email" name="email" id="subscriberEmail" placeholder="vul je e-mailadres in" value="{{ old('email') }}" required>
                                <span id="emailError" class="email-error"></span>
                                @error('email')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-4 ">
                                <button type="submit" class="btn btn-light pos-10">Aanmelden</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="row mt-20">
                    <div class="col-12 descr">
                        <b>Thuiskok omschrijving:</b>
                        <p>{{$description?->getDescription()}}</p>
                        <!-- <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p> -->
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Offers -->
    <div class="container offers">
        
        <div class="form-group d-none">
            <input type="text" id="sortFormInput" name="sorting" value="distance">
            <input type="text" id="search_string" name="search_string" value="{{$searchString}}">
        </div>
        <h1 class="aanbod">Aanbod</h1>
        <div class="dish-collection">
            @foreach($adverts as $advert)
                @if(
                    !$advert->published() ||
                    !$advert->getParsedPickupTo()->isFuture() && !$advert->published() ||
                    $advert->getParsedPickupTo()->isPast()
                )
                    @continue
                @endif

                <div class="dish-row" onclick='checkDish("{{$advert->getUuid()}}", "{{$distanceFromUser}}")'>
                    <div class="row top-bgrd">
                        <div class="col-6">
                            <a href="{{route('search.cooks.detail.advert', [$cook->getUuid(), $advert->getUuid(), $searchString])}}">
                                <!-- <img src="{{asset('img/eleven.jpg')}}" class="dish-img" />  -->
                                <img src="{{ $advert->dish->image?->getCompletePath() ?? url('/img/pasta.jpg') }}" class="dish-img" />
                                <span class="d-none" id="available">{{$advert->getLeftOverAmount()}}</span>
                                <span class="nog">
                                    @if ($advert->getParsedOrderTo()->isFuture() && $advert->published() && $advert->getLeftOverAmount() !== 0)
                                        <span class="d-none" id="available-{{$advert->getUuid()}}">
                                        @if ($advert->getParsedOrderTo()->isFuture()){{$advert->getLeftOverAmount()}}@else 0 @endif</span>
                                        Nog {{$advert->getLeftOverAmount()}} beschikbaar
                                    @elseif ($advert->getParsedOrderTo()->isFuture() && $advert->getLeftOverAmount() === 0)
                                        Uitverkocht
                                    @else
                                        uiterlijke bestelmoment verlopen
                                    @endif
                                </span>
                            </a>
                        </div>

                        <div class="col-6 line">
                            <div class="row" onclick='checkCook("{{$advert->cook->getUuid()}}", "{{$advert->getDistance()}}")'>
                                <div class="col-4 d-flex align-center justify-center mb-10">                                    
                                    <a href="{{route('search.cooks.detail', $cook->getUuid())}}" class="d-flex align-center justify-center">
                                        <div class="round-image-container">
                                            <img src="{{ $advert->cook->user->image?->getCompletePath() ?? url('/img/kok.png') }}" />
                                            <!-- <img src="{{asset('img/eleven.jpg')}}" />  -->
                                        </div>                                        
                                    </a>
                                </div>
                                <div class="col-8">
                                    <a href="{{route('search.cooks.detail', $cook->getUuid())}}">
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
                                @if($advert->getParsedOrderTo()->isFuture() && $advert->published() && $advert->getLeftOverAmount() !== 0)
                                    <div class="order-section">
                                        <a href="{{route('advert.order', $advert->getUuid())}}" class="order-button text-left rounded-lg">
                                            <span class="button-title">Bestel</span><br>
                                            <span>Nog {{$advert->orderTimeLeft()}} te bestellen!</span>
                                        </a>
                                        <div class="pickup-details text-left">
                                            <b>Afhalen:</b><br/>
                                            {{$advert->getParsedPickupFrom()->translatedFormat('l d F')}} ({{$advert->getParsedPickupFrom()->translatedFormat('H:i')}} - {{$advert->getParsedPickupTo()->translatedFormat('H:i')}})
                                        </div>
                                    </div>
                                @endif
                                    <div class="d-flex justify-space-between">
                                        <div class="price">
                                            € {{$advert->getPortionPrice()}}
                                        </div>
                                        <div class="distance">
                                            <i class="fa fa-map-marker"></i>@if (is_null($distanceFromUser)) {{$cook->getCity()}} @elseif ($distanceFromUser < 1) 1< km @else {{ceil($distanceFromUser)}} km @endif
                                        </div>
                                    </div>                                  
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row white-bgrd">
                        <div class="col-12">
                            <div class="d-flex align-center">
                                <a href="{{route('search.cooks.detail.advert', [$cook->getUuid(), $advert->getUuid(), $searchString])}}">
                                    <h4 class="mb-1 mr-10">{{$advert->dish->getTitle()}}</h4>
                                </a>
                                <div>
                                @for($i = 0; $i < $advert->dish->getSpiceLevel(); $i++)
                                    <i class="fa-solid fa-pepper-hot" style="color: #dc3545;"></i>
                                @endfor
                                @for($i = 0 + $advert->dish->getSpiceLevel(); $i < 3; $i++)
                                    <i class="fa-solid fa-pepper-hot" style="opacity: 0.3; color: black;"></i>
                                @endfor
                                </div>
                            </div>
                            <div class="types">
                                @if ($advert->dish->isVegetarian()) <span class="round-item round-grey float-left" title="Vegetarisch"><img src="{{asset('img/types/vegetarian.svg')}}" /></span> @endif
                                @if ($advert->dish->isVegan()) <span class="round-item round-grey float-left" title="Veganistisch"><img src="{{asset('img/types/vegan.svg')}}" /></span> @endif
                                @if ($advert->dish->isHalal()) <span class="round-item round-grey float-left" title="Helal"><img src="{{asset('img/types/halal.svg')}}" /></span> @endif
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
        </div>
        <div class="pagination">
        {{ $adverts->links() }}
    </div>
    </div>
@endsection



@section('page.scripts')
    <script>
        const searchString = document.getElementById('search_string').value;
        function orderAdvert(uuid, distance)
        {
            const available = Number(document.getElementById('available-' + uuid).innerText);

            if (available !== 0) {
                order = true;
                window.location.href = '/details/' + uuid + '/order?calculatedDistance=' + distance + '&' + searchString;
            }
        }

        // Email validatie voor inschrijfformulier
        document.addEventListener('DOMContentLoaded', function() {
            const subscribeForm = document.getElementById('subscribeForm');
            const emailInput = document.getElementById('subscriberEmail');
            const emailError = document.getElementById('emailError');

            // Functie voor email validatie
            function validateEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            // Validatie bij het verlaten van het veld
            emailInput.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    emailError.textContent = 'E-mailadres is verplicht';
                    this.classList.add('is-invalid');
                } else if (!validateEmail(this.value)) {
                    emailError.textContent = 'Voer een geldig e-mailadres in';
                    this.classList.add('is-invalid');
                } else {
                    emailError.textContent = '';
                    this.classList.remove('is-invalid');
                }
            });

            // Real-time validatie tijdens het typen
            emailInput.addEventListener('input', function() {
                if (this.value.trim() !== '' && validateEmail(this.value)) {
                    emailError.textContent = '';
                    this.classList.remove('is-invalid');
                }
            });

            // Formulier validatie bij verzenden
            subscribeForm.addEventListener('submit', function(e) {
                if (emailInput.value.trim() === '') {
                    emailError.textContent = 'E-mailadres is verplicht';
                    emailInput.classList.add('is-invalid');
                    e.preventDefault();
                } else if (!validateEmail(emailInput.value)) {
                    emailError.textContent = 'Voer een geldig e-mailadres in';
                    emailInput.classList.add('is-invalid');
                    e.preventDefault();
                }
            });
        });
    </script>

    <script src="{{asset('js/product-showcase.js')}}"></script>
    <link rel="stylesheet" href="{{asset('css/product-showcase.css')}}">

    <script>
    $(document).ready(function(){
        $(".product-showcase").productShowcase({
            maxHeight:"630px",	
            width:"100%"
        });

        //On click of white-bgrd click the dish title
        $(document).on("click", ".row.white-bgrd" , function() {
            $(this).find("a").click();
        });
    });
    </script>
@endsection