@extends('layout.main')
@section('content')
<div class="dbk-page-header">
    <div class="container"><h1>{{$cook->user->getUsername()}}</h1></div>
</div>

<div class="container dbk-cook-detail">
    <div class="dbk-back-link">
        <a href="{{url()->previous()}}" class="tarug"><i class="fa-solid fa-arrow-left"></i> Terug</a>
    </div>

    <div class="dbk-cook-profile">
        <div class="dbk-cook-profile-left">
            <div class="product-showcase">
                <div class="ps-icon"
                    data-icon="{{ $cook->user->image?->getCompletePath() ?? url('/img/kok.png') }}"
                    data-image="{{ $cook->user->image?->getCompletePath() ?? url('/img/kok.png') }}"
                    data-selected="true"></div>
                @foreach($cook->user->images as $image)
                    @if ($image->isMainPicture())
                        @continue;
                    @endif
                    <div class="ps-icon"
                        data-icon="{{$image->getCompletePath()}}" alt="{{$image->getDescription()}}"
                        data-image="{{$image->getCompletePath()}}" alt="{{$image->getDescription()}}"></div>
                @endforeach
            </div>
        </div>

        <div class="dbk-cook-profile-right">
            <div class="dbk-cook-profile-meta">
                <small>Actief sinds {{$cook->user->getCreatedAt()->translatedFormat('d F Y')}}</small>
            </div>
            <div class="dbk-cook-profile-rating">
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
            <h2 class="dbk-cook-profile-name">{{$cook->user->getUsername()}}</h2>

            <div class="dbk-cook-details-list">
                @if ($privacy?->showStreet() === 3)
                    <div class="dbk-detail-item">
                        <span class="dbk-detail-label">Adres:</span>
                        <span>{{$cook->getStreet()}} @endif @if($privacy?->showHouseNumber() === 3) {{$cook->getHouseNumber()}} </span>
                    </div>
                @endif

                <div class="dbk-detail-item">
                    <span class="dbk-detail-label">Postcode:</span>
                    <span>{{$cook->getPostalCode()}} {{$cook->getCity()}}</span>
                </div>
                @if($privacy?->showSoldPortions() === 3)
                    <div class="dbk-detail-item">
                        <span class="dbk-detail-label">Aantal verkochte porties:</span>
                        <span>{{ $cook->getSoldPortions() }}</span>
                    </div>
                @endif
                @if($privacy?->showEmail() === 3)
                    <div class="dbk-detail-item">
                        <span class="dbk-detail-label">E-mailadres:</span>
                        <span>{{$cook->user->getEmail()}}</span>
                    </div>
                @endif
                @if($privacy?->showPhone() === 3)
                    <div class="dbk-detail-item">
                        <span class="dbk-detail-label">Telefoonnummer:</span>
                        <span>{{$cook->user->userProfile->getPhoneNumber()}}</span>
                    </div>
                @endif
            </div>

            <form action="{{route('customer.cook.subscribe', $cook->getUuid())}}" method="POST" class="dbk-subscribe-form" id="subscribeForm">
                @csrf
                <label for="subscriberEmail"><strong>Ik ontvang graag een e-mail als {{$cook->user->getUsername()}} nieuwe advertenties plaatst.</strong></label>
                <div class="dbk-subscribe-row">
                    <div class="dbk-subscribe-input-wrap">
                        <input type="email" name="email" id="subscriberEmail" placeholder="Vul je e-mailadres in" value="{{ old('email') }}" required>
                        <span id="emailError" class="email-error"></span>
                        @error('email')
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="dbk-subscribe-btn">Aanmelden</button>
                </div>
            </form>

            <div class="dbk-cook-description">
                <strong>Thuiskok omschrijving:</strong>
                <p>{{$description?->getDescription()}}</p>
            </div>
        </div>
    </div>
</div>

<!-- Offers -->
<div class="container dbk-offers-section">
    <div class="form-group d-none">
        <input type="text" id="sortFormInput" name="sorting" value="distance">
        <input type="text" id="search_string" name="search_string" value="{{$searchString}}">
    </div>
    <h2 class="dbk-section-title">Aanbod</h2>
    <div class="dbk-dish-grid">
        @foreach($adverts as $advert)
            @if(
                !$advert->published() ||
                !$advert->getParsedPickupTo()->isFuture() && !$advert->published() ||
                $advert->getParsedPickupTo()->isPast()
            )
                @continue
            @endif

            <div class="dbk-dish-card" onclick='checkDish("{{$advert->getUuid()}}", "{{$distanceFromUser}}")'>
                <div class="dbk-dish-card-image">
                    <a href="{{route('search.cooks.detail.advert', [$cook->getUuid(), $advert->getUuid(), $searchString])}}">
                        <img src="{{ $advert->dish->image?->getCompletePath() ?? url('/img/pasta.jpg') }}" class="dish-img" alt="{{$advert->dish->getTitle()}}" />
                        <span class="d-none" id="available">{{$advert->getLeftOverAmount()}}</span>
                        <span class="dbk-dish-badge">
                            @if ($advert->getParsedOrderTo()->isFuture() && $advert->published() && $advert->getLeftOverAmount() !== 0)
                                <span class="d-none" id="available-{{$advert->getUuid()}}">
                                @if ($advert->getParsedOrderTo()->isFuture()){{$advert->getLeftOverAmount()}}@else 0 @endif</span>
                                Nog {{$advert->getLeftOverAmount()}} beschikbaar
                            @elseif ($advert->getParsedOrderTo()->isFuture() && $advert->getLeftOverAmount() === 0)
                                Uitverkocht
                            @else
                                Bestelmoment verlopen
                            @endif
                        </span>
                    </a>
                </div>

                <div class="dbk-dish-card-body">
                    <div class="dbk-dish-card-cook" onclick='checkCook("{{$advert->cook->getUuid()}}", "{{$advert->getDistance()}}")'>
                        <a href="{{route('search.cooks.detail', $cook->getUuid())}}" class="dbk-dish-cook-link">
                            <div class="dbk-dish-cook-avatar">
                                <img src="{{ $advert->cook->user->image?->getCompletePath() ?? url('/img/kok.png') }}" alt="{{$advert->cook->user->getUsername()}}" />
                            </div>
                            <div class="dbk-dish-cook-info">
                                <strong>{{$advert->cook->user->getUsername()}}</strong>
                                <div class="dbk-dish-cook-stars">
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
                        </a>
                    </div>

                    <div class="dbk-dish-card-title">
                        <a href="{{route('search.cooks.detail.advert', [$cook->getUuid(), $advert->getUuid(), $searchString])}}">
                            <h4>{{$advert->dish->getTitle()}}</h4>
                        </a>
                        <div class="dbk-spice-level">
                            @for($i = 0; $i < $advert->dish->getSpiceLevel(); $i++)
                                <i class="fa-solid fa-pepper-hot" style="color: #dc3545;"></i>
                            @endfor
                            @for($i = 0 + $advert->dish->getSpiceLevel(); $i < 3; $i++)
                                <i class="fa-solid fa-pepper-hot" style="opacity: 0.3;"></i>
                            @endfor
                        </div>
                    </div>

                    <div class="dbk-dish-types">
                        @if ($advert->dish->isVegetarian()) <span class="dbk-diet-badge" title="Vegetarisch"><img src="{{asset('img/types/vegetarian.svg')}}" /> Vegetarisch</span> @endif
                        @if ($advert->dish->isVegan()) <span class="dbk-diet-badge" title="Veganistisch"><img src="{{asset('img/types/vegan.svg')}}" /> Vegan</span> @endif
                        @if ($advert->dish->isHalal()) <span class="dbk-diet-badge" title="Helal"><img src="{{asset('img/types/halal.svg')}}" /> Halal</span> @endif
                        @if ($advert->dish->hasAlcohol()) <span class="dbk-diet-badge" title="Alcohol"><img src="{{asset('img/types/alcohol.svg')}}" /> Alcohol</span> @endif
                        @if ($advert->dish->hasGluten()) <span class="dbk-diet-badge" title="Glutenvrij"><img src="{{asset('img/types/gluten-free.svg')}}" /> Glutenvrij</span> @endif
                        @if ($advert->dish->hasLactose()) <span class="dbk-diet-badge" title="Lactosevrij"><img src="{{asset('img/types/dairy.svg')}}" /> Lactosevrij</span> @endif
                    </div>

                    <p class="dbk-dish-desc">{{(strlen($advert->dish->getDescription()) > 150) ? substr($advert->dish->getDescription(),0,150).'...' : $advert->dish->getDescription()}}</p>

                    <div class="dbk-dish-footer">
                        <div class="dbk-dish-price">€ {{$advert->getPortionPrice()}}</div>
                        <div class="dbk-dish-distance">
                            <i class="fa fa-map-marker"></i> @if (is_null($distanceFromUser)) {{$cook->getCity()}} @elseif ($distanceFromUser < 1) &lt;1 km @else {{ceil($distanceFromUser)}} km @endif
                        </div>
                    </div>

                    @if($advert->getParsedOrderTo()->isFuture() && $advert->published() && $advert->getLeftOverAmount() !== 0)
                        <div class="dbk-dish-order">
                            <a href="{{route('advert.order', $advert->getUuid())}}" class="dbk-order-btn">
                                <span class="dbk-order-title">Bestel nu</span>
                                <span class="dbk-order-sub">Nog {{$advert->orderTimeLeft()}} te bestellen!</span>
                            </a>
                            <div class="dbk-dish-pickup">
                                <i class="fa-solid fa-clock"></i> Afhalen: {{$advert->getParsedPickupFrom()->translatedFormat('l d F')}} ({{$advert->getParsedPickupFrom()->translatedFormat('H:i')}} - {{$advert->getParsedPickupTo()->translatedFormat('H:i')}})
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    <div class="dbk-pagination">
        {{ $adverts->links() }}
    </div>
</div>
@endsection

@section('page.scripts')
    <script>
        const searchString = document.getElementById('search_string').value;
        function orderAdvert(uuid, distance) {
            const available = Number(document.getElementById('available-' + uuid).innerText);
            if (available !== 0) {
                window.location.href = '/details/' + uuid + '/order?calculatedDistance=' + distance + '&' + searchString;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const subscribeForm = document.getElementById('subscribeForm');
            const emailInput = document.getElementById('subscriberEmail');
            const emailError = document.getElementById('emailError');
            function validateEmail(email) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email); }
            emailInput.addEventListener('blur', function() {
                if (this.value.trim() === '') { emailError.textContent = 'E-mailadres is verplicht'; this.classList.add('is-invalid'); }
                else if (!validateEmail(this.value)) { emailError.textContent = 'Voer een geldig e-mailadres in'; this.classList.add('is-invalid'); }
                else { emailError.textContent = ''; this.classList.remove('is-invalid'); }
            });
            emailInput.addEventListener('input', function() {
                if (this.value.trim() !== '' && validateEmail(this.value)) { emailError.textContent = ''; this.classList.remove('is-invalid'); }
            });
            subscribeForm.addEventListener('submit', function(e) {
                if (emailInput.value.trim() === '') { emailError.textContent = 'E-mailadres is verplicht'; emailInput.classList.add('is-invalid'); e.preventDefault(); }
                else if (!validateEmail(emailInput.value)) { emailError.textContent = 'Voer een geldig e-mailadres in'; emailInput.classList.add('is-invalid'); e.preventDefault(); }
            });
        });
    </script>

    <script src="{{asset('js/product-showcase.js')}}"></script>
    <link rel="stylesheet" href="{{asset('css/product-showcase.css')}}">

    <script>
    $(document).ready(function(){
        $(".product-showcase").productShowcase({ maxHeight:"630px", width:"100%" });
    });
    </script>
@endsection
