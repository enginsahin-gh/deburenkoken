@extends('layout.main')
@section('content')
<style>

.fa-solid,
.fa-circle-info,
.fa-info-circle,
i[class*="fa-"] {
    text-decoration: none !important;
    border: none !important;
    border-bottom: none !important;
    box-shadow: none !important;
    outline: none !important;
}

.fa-solid::before,
.fa-solid::after,
.fa-circle-info::before,
.fa-circle-info::after,
.fa-info-circle::before,
.fa-info-circle::after,
i[class*="fa-"]::before,
i[class*="fa-"]::after {
    border: none !important;
    border-bottom: none !important;
    text-decoration: none !important;
    box-shadow: none !important;
}

.tooltip,
.tooltip *,
.ml-2,
.ml-2 * {
    text-decoration: none !important;
    border-bottom: none !important;
    box-shadow: none !important;
}

/* Verbeterde tooltip styling */
.tooltip {
    position: relative;
    display: inline-block;
}

.tooltip .tooltiptext {
    visibility: hidden;
    min-width: 200px;
    max-width: 300px;
    width: max-content;
    background-color: #333;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 10px;
    position: absolute;
    z-index: 1000;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%);
    opacity: 0;
    transition: opacity 0.3s;
    word-wrap: break-word;
    box-sizing: border-box;
}

/* Zorg ervoor dat tooltip binnen scherm blijft */
.tooltip .tooltiptext::before {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #333 transparent transparent transparent;
}

/* Responsieve tooltip positionering */
@media (max-width: 768px) {
    .tooltip .tooltiptext {
        max-width: 250px;
        width: 90vw;
        left: 50%;
        transform: translateX(-50%);
    }
}

/* Tooltip voor elementen aan de rechterkant van het scherm */
.tooltip.tooltip-left .tooltiptext {
    left: auto;
    right: 0;
    transform: none;
}

.tooltip.tooltip-left .tooltiptext::before {
    left: auto;
    right: 20px;
    margin-left: 0;
}

/* Tooltip voor elementen aan de linkerkant van het scherm */
.tooltip.tooltip-right .tooltiptext {
    left: 0;
    transform: none;
}

.tooltip.tooltip-right .tooltiptext::before {
    left: 20px;
    margin-left: 0;
}

.tooltip:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
}

/* Minimale CSS voor correcte positionering */
#timeSelector {
    position: relative;
}

#openTimeSelector {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
}

#selectTime {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    background: white;
    border: 1px solid #ccc;
    display: flex;
}

#stListHour, #stListMinutes {
    overflow-y: auto;
}

/* Styling voor uitgeschakelde knop */
button[type="submit"]:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    background-color: #ccc !important;
    border-color: #aaa !important;
}

/* Normaliseer alle input styling */
.input-normalize {
    outline: none !important;
    box-shadow: none !important;
}

/* Verwijder rode outlines en blauwe achtergronden */
.input-normalize:focus,
.input-normalize:active,
.input-normalize:focus-visible {
    border-color: #ced4da !important;
    outline: none !important;
    box-shadow: none !important;
}

/* Verwijder blauwe achtergrond bij autofill */
input:-webkit-autofill,
input:-webkit-autofill:hover, 
input:-webkit-autofill:focus,
input:-webkit-autofill:active {
    -webkit-box-shadow: 0 0 0 30px white inset !important;
    transition: background-color 5000s ease-in-out 0s;
}

textarea:-webkit-autofill,
textarea:-webkit-autofill:hover, 
textarea:-webkit-autofill:focus,
textarea:-webkit-autofill:active {
    -webkit-box-shadow: 0 0 0 30px white inset !important;
    transition: background-color 5000s ease-in-out 0s;
}

</style>
    <div class="page-header">
        <div class="container"><h1>Bestel nu</h1></div>
    </div>
    <div class="container">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('paymentError'))
            <div class="alert alert-danger">
                {{ session('paymentError') }}
            </div>
        @endif

        <div class="dish-top">
            <div class="row">
                <div class="col-3">
                    <div class="img-holder">
                        <img src="{{ $advert->dish->image?->getCompletePath() ?? url('/img/pasta.jpg') }}" />
                    </div>
                    <span class="nog">
                        Nog {{$advert->getLeftOverAmount()}} beschikbaar
                    </span>
                </div>


                <div class="col-5">
                    <a href="{{route('advert.details', $advert->getUuid())}}?{{$searchString}}">
                        <div class="d-flex align-center mt-30">
                            <h4 class="mr-10">{{$advert->dish->getTitle()}}</h4>
                            <div>
                                @for($i = 0; $i < $advert->dish->getSpiceLevel(); $i++)
                                    <i class="fa-solid fa-pepper-hot" style="color: #dc3545; margin-right: 2px;"></i>
                                @endfor
                                @for($i = 0 + $advert->dish->getSpiceLevel(); $i < 3; $i++)
                                    <i class="fa-solid fa-pepper-hot" style="color: #ccc; opacity: 0.3; margin-right: 2px;"></i>
                                @endfor
                            </div>
                        </div>
                        <div class="descr">
                            <p>{{(strlen($advert->dish->getDescription()) > 150) ? substr($advert->dish->getDescription(),0,150).'...' : $advert->dish->getDescription()}}</p>
                            <!-- <p>Chicken ingredietentsts</p> -->
                        </div>
                        <div class="types">
                            @if ($advert->dish->isVegetarian()) <span class="round-item round-grey" title="Vegetarisch"><img src="{{asset('img/types/vegetarian.svg')}}" /></span> @endif
                            @if ($advert->dish->isVegan()) <span class="round-item round-grey" title="Veganistisch"><img src="{{asset('img/types/vegan.svg')}}" /></span> @endif
                            @if ($advert->dish->isHalal()) <span class="round-item round-grey" title="Halal"><img src="{{asset('img/types/halal.svg')}}" /></span> @endif
                            @if ($advert->dish->hasAlcohol()) <span class="round-item round-grey" title="Alcohol"><img src="{{asset('img/types/alcohol.svg')}}" /></span> @endif
                            @if ($advert->dish->hasGluten()) <span class="round-item round-grey" title="Glutenvrij"><img src="{{asset('img/types/gluten-free.svg')}}" /></span> @endif
                            @if ($advert->dish->hasLactose()) <span class="round-item round-grey" title="Lactosevrij"><img src="{{asset('img/types/dairy.svg')}}" /></span> @endif
                        </div>
                        <div class="price">€ {{$advert->getPortionPrice()}}</div>
                    </a>
                </div>


                <div class="col-4 line">
                    <div class="row">
                        <div class="col-12 d-flex align-center justify-center mb-10">                                    
                            <a href="javascript:void(0);" class="d-flex align-center justify-center pointer-none">
                                <div class="round-image-container">
                                    <img src="{{ $advert->cook->user->image?->getCompletePath() ?? url('/img/kok.png') }}">
                                    <!-- <img src="{{asset('img/eleven.jpg')}}" />  -->
                                </div>
                                <b>{{$advert->cook->user->getUsername()}}</b>
                            </a>
                        </div>
                    </div>
                    <div class="row d-inline star-font">
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
                    <div class="row">
                        <div class="col-12">
                            <div class="time-left">Nog {{$advert->orderTimeLeft()}} te bestellen!</div>
                            <div class="text-center afhalen">
                                <b>Afhalen:</b><br/>
                                {{$advert->getParsedPickupFrom()->translatedFormat('d F')}} ({{$advert->getParsedPickupFrom()->translatedFormat('H:i')}} - {{$advert->getParsedPickupTo()->translatedFormat('H:i')}})
                            </div>
                            <div class="text-center afhalen">
                                <b>Annuleren mogelijk tot:</b>
                                    <div class="tooltip ml-2" style='margin-left: 0px !important;'><i class="fa fa-info-circle"></i>
                                        <span class="tooltiptext">Let op! Er zullen transactiekosten in rekening gebracht worden bij het annuleren van een bestelling.</span>
                                    </div><br/>
                                
                                  {{ \Carbon\Carbon::parse($advert->order_date)->isoFormat('D MMMM') }}

                                {{implode(":", array_slice(explode(":", $advert->order_time ), 0, 2));}}
                            </div>
                        </div>
                    </div>
                    <div class="blue-clr">
                        <i class="fa fa-map-marker"></i>@if ($advert->getDistance() < 1) < @endif {{ceil($advert->getDistance())}} km
                    </div>
                </div>
            </div>
        </div>        
        

        <section class="clearfix mt-3">
            <div class="row">
                <div class="col-8 offset-2 page-title">
                    <h2>Bestel nu</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-8 offset-2">
                    <form action="{{route('advert.order.submit', $advert->getUuid())}}" method="POST" class="row form-box" id="orderForm">
                        @csrf
                        <input class="hide" name="searchString" value="{{$searchString}}">
                        
                        <div class="col-6">
                            <div class="form-group">
                                <label for="name">Naam </label>
                                <input type="text" name="name" class="form-control input-normalize" id="name" required maxlength="50" autoComplete="name" pattern="^[A-Za-zÀ-ÖØ-öø-ÿ\s\-'\.]+$" title="Alleen letters, spaties en speciale tekens zoals ' - . zijn toegestaan">
                                <p class="text-danger" id="name-error"></p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="email">Email </label>
                                <input type="email" name="email" class="form-control input-normalize" id="email" required maxlength="100" autoComplete="email">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="phone">Telefoonnummer </label>
                                <input type="tel" class="form-control input-normalize" id="phone" name="phone" required maxlength="15" autoComplete="tel">
                                <p class="text-danger" id="phone-error"></p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="amount">Aantal porties </label>
                                <input type="number" class="form-control input-normalize" onchange='calculatePrice("{{$advert->getPortionPrice()}}")' id="amount" name="amount" min="1" max="{{$advert->getLeftOverAmount()}}" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="time">Verwachte aankomsttijd </label>
                                <input hidden class="form-control" id="time" name="time">

                                <div id='timeSelector'> 
                                <input disabled id='selectedTime' class='form-control input-normalize' placeholder='--:--' />
                                <div id='openTimeSelector'><i class="far fa-clock"></i></div>
                                <div id='selectTime'>
                                    <div id='stListHour'></div>
                                    <div id='stListMinutes'></div>
                                </div>
                                <p class="text-danger" id="time-error"></p>
                            </div>

                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="description">Opmerking (optioneel)
                                    <div class="tooltip ml-2"><i class="fa fa-info-circle"></i>
                                        <span class="tooltiptext">Let op! De thuiskok is niet verplicht om invulling te geven aan je opmerking.</span>
                                    </div>
                                </label>
                                <textarea rows="1" cols="1" maxlength="250" name="description" id="description" class="input-normalize"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <input type="checkbox" name="inform" id="inform"> Ik wil in de toekomst een e-mail ontvangen wanneer {{$advert->cook->user->getUsername()}} een advertentie online plaatst.
                            </div>
                        </div>
                        <div class="d-none">
                            <input type="text" id="totalCalculated" name="total">
                        </div>
                        
                        <div class="col-12">
                            <div class="form-row">
                                <p>Door op 'BESTEL EN BETAAL' te klikken ga je akkoord met de <a href="{{ route('terms.conditions') }}" class="underline">algemene voorwaarden</a>.</p>
                                <button class="btn btn-light col-6 mx-auto" type="submit" id="submitButton">Bestel en betaal ( € <span id="total">0</span> )</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('page.scripts')
<script>
    const amount = document.getElementById('amount');
    const total = document.getElementById('total');
    const formTotal = document.getElementById('totalCalculated');

    function calculatePrice(price){
        const calculate = (amount.value * price).toFixed(2);
        total.innerText = calculate;
        formTotal.value = calculate;
    }

    // Dinamische tooltip positionering
    document.addEventListener('DOMContentLoaded', function() {
        const tooltips = document.querySelectorAll('.tooltip');
        
        tooltips.forEach(tooltip => {
            const tooltipText = tooltip.querySelector('.tooltiptext');
            
            tooltip.addEventListener('mouseenter', function() {
                // Reset classes
                this.classList.remove('tooltip-left', 'tooltip-right');
                
                // Wacht even zodat de tooltip zich kan positioneren
                setTimeout(() => {
                    const tooltipRect = tooltipText.getBoundingClientRect();
                    const windowWidth = window.innerWidth;
                    
                    // Check of tooltip buiten het scherm valt
                    if (tooltipRect.left < 0) {
                        this.classList.add('tooltip-right');
                    } else if (tooltipRect.right > windowWidth) {
                        this.classList.add('tooltip-left');
                    }
                }, 10);
            });
        });
    });

    // Naam validatie - Voorkom getallen in het naam veld
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const nameError = document.getElementById('name-error');
        
        nameInput.addEventListener('input', function(e) {
            // Verwijder alle getallen uit de invoer
            const value = this.value;
            const filteredValue = value.replace(/[0-9]/g, '');
            
            if (value !== filteredValue) {
                this.value = filteredValue;
                nameError.textContent = 'Getallen zijn niet toegestaan in de naam';
            } else {
                nameError.textContent = '';
            }
            
            // Controleer ook op het patroon (alleen letters, spaties en speciale karakters)
            if (filteredValue && !isValidName(filteredValue)) {
                nameError.textContent = 'Alleen letters, spaties en speciale tekens zoals \' - . zijn toegestaan';
                this.setCustomValidity('Ongeldige naam');
            } else {
                this.setCustomValidity('');
            }
        });
        
        function isValidName(name) {
            const nameRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ\s\-'\.]+$/;
            return nameRegex.test(name);
        }
    });

    // Uitgebreide telefoonnummer validatie voor Nederlandse mobiele en vaste nummers
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.getElementById('phone');
        const phoneValidationMessage = document.getElementById('phone-error');
        
        if (phoneInput) {
            // Nederlandse telefoonnummer validatie functie
            function validateDutchPhoneNumber(phone) {
                const cleanedPhone = phone.replace(/[\s\-]/g, '');
                
                // Nederlandse mobiele nummers
                if (cleanedPhone.match(/^06\d{8}$/)) return true; // 06xxxxxxxx
                if (cleanedPhone.match(/^\+316\d{8}$/)) return true; // +316xxxxxxxx
                
                // Nederlandse vaste nummers
                if (cleanedPhone.match(/^0[1-9]\d{8}$/)) return true; // 0xxxxxxxxx
                if (cleanedPhone.match(/^\+31[1-9]\d{8}$/)) return true; // +31xxxxxxxxx
                
                return false;
            }
            
            phoneInput.addEventListener('keydown', function(e) {
                const key = e.key;
                
                // Sta navigatie toetsen toe
                if (key === 'Backspace' || key === 'Delete' || key === 'ArrowLeft' || 
                    key === 'ArrowRight' || key === 'Tab' || key === 'Enter' ||
                    e.ctrlKey || e.altKey || e.metaKey) {
                    return;
                }
                
                // Alleen cijfers, '+', spaties en '-' toestaan
                if (!/^[0-9+\s\-]$/.test(key)) {
                    e.preventDefault();
                    phoneValidationMessage.textContent = 'Alleen cijfers, +, spaties en - zijn toegestaan';
                    phoneValidationMessage.style.display = 'block';
                    return;
                }
                
                // Voorkom te lange invoer
                if (this.value.length >= 15 && key !== 'Backspace' && key !== 'Delete') {
                    e.preventDefault();
                    phoneValidationMessage.textContent = 'Telefoonnummer is te lang';
                    phoneValidationMessage.style.display = 'block';
                }
            });
            
            phoneInput.addEventListener('input', function(event) {
                // Forceer maximum lengte
                if (this.value.length > 15) {
                    this.value = this.value.substring(0, 15);
                }
                
                // Laat alleen geldige tekens toe in telefoonnummer
                if (event.inputType === 'insertText' || event.inputType === 'insertFromPaste') {
                    const value = this.value;
                    const filteredValue = value.replace(/[^0-9+\s\-]/g, '');
                    
                    if (value !== filteredValue) {
                        this.value = filteredValue;
                        phoneValidationMessage.textContent = 'Alleen cijfers, +, spaties en - zijn toegestaan';
                        phoneValidationMessage.style.display = 'block';
                    } else {
                        phoneValidationMessage.style.display = 'none';
                    }
                }
                
                // Valideer het formaat van het nummer als het lang genoeg is
                const cleanedNumber = this.value.replace(/[\s\-]/g, '');
                
                if (cleanedNumber.length >= 10) {
                    if (validateDutchPhoneNumber(this.value)) {
                        this.setCustomValidity('');
                        phoneValidationMessage.style.display = 'none';
                    } else {
                        phoneValidationMessage.textContent = 'Voer een geldig Nederlands telefoonnummer in (mobiel: 06xxxxxxxx of +316xxxxxxxx, vast: 0xxxxxxxxx of +31xxxxxxxxx)';
                        phoneValidationMessage.style.display = 'block';
                        this.setCustomValidity('Ongeldig telefoonnummer');
                    }
                } else if (cleanedNumber.length > 0) {
                    // Nog te kort, geen foutmelding tonen
                    this.setCustomValidity('');
                    phoneValidationMessage.style.display = 'none';
                } else {
                    this.setCustomValidity('');
                    phoneValidationMessage.style.display = 'none';
                }
            });
            
            phoneInput.addEventListener('blur', function() {
                const value = this.value;
                if (value && !validateDutchPhoneNumber(value)) {
                    phoneValidationMessage.textContent = 'Voer een geldig Nederlands telefoonnummer in (mobiel: 06xxxxxxxx of +316xxxxxxxx, vast: 0xxxxxxxxx of +31xxxxxxxxx)';
                    phoneValidationMessage.style.display = 'block';
                    this.setCustomValidity('Ongeldig telefoonnummer');
                }
            });
        }
    });

    // Eenvoudige mobiel-vriendelijke formulierinteractie voor autocomplete
    document.addEventListener('DOMContentLoaded', function() {
        // Array van veld-IDs in de gewenste tabvolgorde
        const fieldIds = ['name', 'email', 'phone', 'amount', 'selectedTime', 'description'];
        
        // Maak invoervelden keyboard-vriendelijk
        for (const fieldId of fieldIds) {
            const field = document.getElementById(fieldId);
            if (field && field.tagName === 'INPUT' && field.type !== 'hidden' && field.type !== 'checkbox') {
                // Zet autocomplete aan voor relevante velden
                if (fieldId === 'name') field.autocomplete = 'name';
                if (fieldId === 'email') field.autocomplete = 'email';
                if (fieldId === 'phone') field.autocomplete = 'tel';
                
                // Voorkom dat het toetsenbord verdwijnt bij het veranderen van focus
                field.addEventListener('blur', function(e) {
                    // Alleen voor mobiele apparaten
                    if (isMobileDevice()) {
                        // Controleer of er een ander invoerveld is geselecteerd
                        setTimeout(() => {
                            const activeElement = document.activeElement;
                            // Als het actieve element geen invoerveld is, focus terug op dit veld
                            // tenzij de gebruiker bewust het toetsenbord sluit
                            if (activeElement.tagName !== 'INPUT' && 
                                activeElement.tagName !== 'TEXTAREA' && 
                                activeElement.tagName !== 'SELECT') {
                                
                                // Vind het volgende veld in de tabvolgorde
                                const currentIndex = fieldIds.indexOf(fieldId);
                                if (currentIndex < fieldIds.length - 1) {
                                    const nextFieldId = fieldIds[currentIndex + 1];
                                    const nextField = document.getElementById(nextFieldId);
                                    if (nextField) {
                                        // Zorg dat we gefocust blijven in een invoerveld
                                        // Dit houdt het toetsenbord open
                                        nextField.focus();
                                        
                                        // Op sommige apparaten helpt het om de cursor te plaatsen
                                        if (nextField.type === 'text' || 
                                            nextField.type === 'tel' || 
                                            nextField.type === 'email') {
                                            const length = nextField.value.length;
                                            nextField.setSelectionRange(length, length);
                                        }
                                    }
                                }
                            }
                        }, 20); // Minimale vertraging om andere events te laten plaatsvinden
                    }
                });
                
                // Directe navigatie bij "Enter" toets
                field.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.keyCode === 13) {
                        e.preventDefault(); // Voorkom formulier verzenden
                        
                        // Vind het volgende veld
                        const currentIndex = fieldIds.indexOf(fieldId);
                        if (currentIndex < fieldIds.length - 1) {
                            const nextFieldId = fieldIds[currentIndex + 1];
                            const nextField = document.getElementById(nextFieldId);
                            if (nextField) nextField.focus();
                        }
                    }
                });
            }
        }
        
        // Alleen voor iOS apparaten - voorkom de keyboard autohide
        if (/iPhone|iPad|iPod/i.test(navigator.userAgent)) {
            // iOS heeft soms een probleem met het behouden van het toetsenbord
            document.addEventListener('touchend', function(e) {
                const target = e.target;
                // Controleer of de gebruiker op een invoerveld heeft getikt
                if (target.tagName === 'INPUT' && 
                    target.type !== 'hidden' && 
                    target.type !== 'checkbox') {
                    
                    // Op iOS helpt het soms om een kleine timeout te gebruiken
                    setTimeout(() => {
                        const activeElement = document.activeElement;
                        if (activeElement !== target) {
                            target.focus();
                        }
                    }, 100);
                }
            });
        }
        
        function isMobileDevice() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        }
    });

    const timeSelector = document.getElementById('timeSelector');
    var openTimeSelector = document.getElementById('openTimeSelector');
    var selectTime = document.getElementById('selectTime');

    selectTime.style.display = 'none';

    // Event listener toevoegen aan de opener van het tijdsselectievenster om het selectievenster te openen
    openTimeSelector.addEventListener('click', function(){
        selectTime.style.display = 'block';
    });

    // Variabelen voor het ophalen van het tijdsbereik uit PHP-variabelen
    var from = <?php echo json_encode($advert->getPickupFrom()); ?>.split(':')[0];
    var to = <?php echo json_encode($advert->getPickupTo()); ?>.split(':')[0];

    // Simuleer een lijst met uren en minuten
    var hours = Array.from({ length: 24 }, (_, i) => i.toString().padStart(2, '0'));
    const minutes = Array.from({ length: 60 }, (_, i) => i.toString().padStart(2, '0'));
    var selectedTime = document.getElementById('selectedTime');

    var isFirstTime = 0; // Teller voor eerste selectie
    var FirstSelect = false; // boolean voor eerste selectie
    let countInfineScroll = 0; // Teller voor oneindig scrollen

    // Filter de lijst met urn op basis van tijdbereik
    hours = hours.filter(hour => {
        const hourInt = parseInt(hour);
        return hourInt >= from && hourInt <= to;
    });

    // Functie om items aan de lijst toe te voegen
    function addItemsToTimeList(listContainer, items) {
        // Als de scrollpositie bovenaan is, voeg items toe aan de bovenkant van de lijst
        if (listContainer.scrollTop <= 0 ){
            for (let i = items.length - 1; i >= 0; i--) {
                const p = document.createElement("p");
                p.textContent = items[i];  
                p.value = items[i];    
                if (listContainer.id == 'stListHour'){
                    p.classList.add('hour');
                    if (selectedTime[0] == items[i] && FirstSelect){
                        p.id = 'selectedHour';
                    }

                } else if (listContainer.id == 'stListMinutes'){
                    p.classList.add('minutes');

                    if (selectedTime[1] == items[i] && FirstSelect){
                        p.id = 'selectedMinute';
                    }
                }

                p.addEventListener('click', function(){
                    addItemsToSelectedTime(listContainer ,this);
                });

                listContainer.insertBefore(p, listContainer.firstChild);
            }

            if(listContainer.childElementCount >= 180){
                for (let i = 0; i < items.length; i++) {
                    listContainer.removeChild(listContainer.lastChild);
                }  
            }
            
            if (listContainer.scrollTop == 0 && isFirstTime >= 2){
                const itemHeight = listContainer.firstElementChild.clientHeight;
                const itemsAdded = items.length;
                const newScrollTop = listContainer.scrollTop + (itemsAdded * itemHeight);
                listContainer.scrollTop = newScrollTop;                
            }
        } else {
            for (let i = 0; i < items.length; i++) {
                const p = document.createElement("p");

                p.textContent = items[i];
                p.value = items[i];

                if (listContainer.id == 'stListHour'){
                    p.classList.add('hour');

                    if (selectedTime[0] == items[i] && FirstSelect ){
                        p.id = 'selectedHour';

                    }

                } else if (listContainer.id == 'stListMinutes'){
                    p.classList.add('minutes');

                    if (selectedTime[1] == items[i] && FirstSelect){
                        p.id = 'selectedMinute';
                    }
                }
                p.addEventListener('click', function(){
                    addItemsToSelectedTime(listContainer, this);
                });
                listContainer.appendChild(p);
            }
            if(listContainer.childElementCount >= 180){
                for (let i = 0; i < items.length; i++) {
                    listContainer.removeChild(listContainer.firstChild);
                }
            } 
        }
        FirstSelect = false;

        if (isFirstTime <= 2){
            if (listContainer.id == 'stListHour'){
                listContainer.firstChild.id = 'selectedHour';
            } else if (listContainer.id == 'stListMinutes'){
                listContainer.firstChild.id = 'selectedMinute';                        
            }
        } 
        isFirstTime++;
    }

    // Functie om een geselecteerde tijd toe te voegen aan de lijst
    function addItemsToSelectedTime(listContain, element) {
        const timeInput = document.getElementById('time');
        const timeError = document.getElementById('time-error');

        if(element.className == 'hour'){
            // document.getElementsByClassName("selected")[0].classList.remove('selected');
            document.getElementById('selectedHour').removeAttribute("id")
            element.id = 'selectedHour';
        } else if(element.className == 'minutes'){
            // document.getElementsByClassName("selected")[1].classList.remove('selected');
            document.getElementById('selectedMinute').removeAttribute("id")
            element.id = 'selectedMinute';
        }
        
        time.value = document.getElementById('selectedHour').value +':'+ document.getElementById('selectedMinute').value;
        selectedTime.value = document.getElementById('selectedHour').value +':'+ document.getElementById('selectedMinute').value;
        FirstSelect = true;

        const selectedTimes = new Date('1970-01-01T' + timeInput.value + ':00');
        const minTime = new Date('1970-01-01T<?php echo $advert->getPickupFrom(); ?>');
        const maxTime = new Date('1970-01-01T<?php echo $advert->getPickupTo(); ?>');

        if (selectedTimes < minTime || selectedTimes > maxTime) {
            console.log('Invalid');
            timeError.textContent = 'Kies een tijd tussen {{substr($advert->getPickupFrom(), 0, -3)}} en {{substr($advert->getPickupTo(), 0, -3)}}.';
            timeInput.setCustomValidity('Invalid');
        } else {
            console.log('Valid');
            timeError.textContent = '';
            timeInput.setCustomValidity('');
        }
        
        // Na tijdselectie, verberg het selectie-venster en ga naar volgende invulveld
        if (selectTime) {
            selectTime.style.display = 'none';
        }
        
        // Ga naar volgende veld als deze bestaat (normaal: description)
        const nextField = document.getElementById('description');
        if (nextField) {
            // Eenvoudig focus op het volgende veld
            setTimeout(() => {
                nextField.focus();
            }, 50);
        }
    }
    
    // Functie om te controleren of de gebruiker het einde van de lijst heeft bereikt bij scrollen
    function checkScrollEnd(listContainer, items) {     
        if (listContainer.scrollTop == 0 || listContainer.scrollTop >= listContainer.scrollHeight - listContainer.clientHeight -2) {
            addItemsToTimeList(listContainer, items); 
        }
    }
    
    const stListHour = document.getElementById('stListHour');
    const stListMinutes = document.getElementById('stListMinutes');

    addItemsToTimeList(stListHour, hours);
    addItemsToTimeList(stListMinutes, minutes);

    // Event listeners toevoegen voor scrollen in de lijsten met uren en minuten
    stListHour.addEventListener("scroll", function() {
        checkScrollEnd(stListHour, hours);
    });

    stListMinutes.addEventListener("scroll", function() {
        checkScrollEnd(stListMinutes, minutes);
    });

    // Event listener toevoegen voor klikken buiten het tijdsselectievenster om het te verbergen
    document.addEventListener('click', function(event) {
        const selectTime = document.getElementById('selectTime');
        const openTimeSelector = document.getElementById('openTimeSelector');
        if (!selectTime.contains(event.target) && !openTimeSelector.contains(event.target)) {
            selectTime.style.display = 'none';
        }
    });

    // Eenvoudige knopbescherming die werkt zonder het formulier te blokkeren
    document.addEventListener('DOMContentLoaded', function() {
        const orderForm = document.getElementById('orderForm');
        const submitButton = document.getElementById('submitButton');
        
        if (orderForm && submitButton) {
            // Bijhouden of het formulier is verzonden
            let formSubmitted = false;
            
            orderForm.addEventListener('submit', function(e) {
                // Extra validatie voor telefoonnummer bij submit
                const phoneInput = document.getElementById('phone');
                const phoneError = document.getElementById('phone-error');
                
                if (phoneInput && phoneInput.value) {
                    // Gebruik de Nederlandse telefoonnummer validatie functie
                    function validateDutchPhoneNumber(phone) {
                        const cleanedPhone = phone.replace(/[\s\-]/g, '');
                        
                        if (cleanedPhone.match(/^06\d{8}$/)) return true;
                        if (cleanedPhone.match(/^\+316\d{8}$/)) return true;
                        if (cleanedPhone.match(/^0[1-9]\d{8}$/)) return true;
                        if (cleanedPhone.match(/^\+31[1-9]\d{8}$/)) return true;
                        
                        return false;
                    }
                    
                    if (!validateDutchPhoneNumber(phoneInput.value)) {
                        e.preventDefault();
                        phoneError.textContent = 'Voer een geldig Nederlands telefoonnummer in (mobiel: 06xxxxxxxx of +316xxxxxxxx, vast: 0xxxxxxxxx of +31xxxxxxxxx)';
                        phoneInput.setCustomValidity('Invalid phone');
                        phoneInput.focus();
                        return false;
                    }
                }
                
                if (!formSubmitted) {
                    formSubmitted = true;
                    submitButton.disabled = true;
                    submitButton.innerHTML = 'Bezig met verwerken...';
                }
            });
        }
        
        // Initialiseer de prijsberekening met de eerste waarde
        if (amount && amount.value) {
            calculatePrice("{{$advert->getPortionPrice()}}");
        }
    });
</script>
@endsection