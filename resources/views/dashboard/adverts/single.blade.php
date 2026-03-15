@extends('layout.dashboard')

@section('dashboard')
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

    .tooltip {
        position: relative;
        display: inline-block;
        cursor: pointer;
    }

    .tooltip .tooltiptext {
        visibility: hidden;
        width: 300px;
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
    }

    .tooltip .tooltiptext::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #333 transparent transparent transparent;
        visibility: hidden;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .tooltip:hover .tooltiptext {
        visibility: visible;
        opacity: 1;
    }

    .tooltip:hover .tooltiptext::after {
        visibility: visible;
        opacity: 1;
    }

    @media (max-width: 767px) {
        .tooltip {
            margin-left: 5px !important;
            vertical-align: middle !important;
        }

        .tooltip .tooltiptext {
            width: 250px !important;
            max-width: 90vw !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            bottom: 130% !important;
            font-size: 14px !important;
        }
    }

    .alert-warning {
        background-color: #fff3cd;
        border: 1px solid #ffeeba;
        color: #856404;
        padding: 1rem;
        margin: 1rem 0;
        border-radius: 0.25rem;
        font-weight: bold;
    }

    .row.mt-30.text-center.justify-center {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        text-align: center !important;
    }

    .row.mt-30.text-center.justify-center .btn {
        margin-left: auto !important;
        margin-right: auto !important;
        margin-bottom: 10px !important;
        display: block !important;
    }

    @media (max-width: 767px) {
        .row.mt-30.text-center.justify-center .btn {
            max-width: 300px !important;
        }
    }

    @media (min-width: 992px) {
        .row.mt-30.text-center.justify-center {
            flex-direction: row !important;
            justify-content: center !important;
        }
        .row.mt-30.text-center.justify-center .btn {
            margin: 0 5px !important;
            display: inline-block !important;
        }
    }

    select.form-control {
        -webkit-appearance: auto !important;
        appearance: auto !important;
    }

    /* Zorg ervoor dat alle form-control inputs geen border-radius hebben */
    .form-control,
    input[type="date"],
    input[type="time"],
    input[type="number"],
    input[type="text"],
    input[disabled],
    select.form-control {
        border-radius: 0 !important;
    }

    input[data-pickup-field="true"] {
        pointer-events: none !important;
        -webkit-user-select: none !important;
        -moz-user-select: none !important;
        -ms-user-select: none !important;
        user-select: none !important;
        background-color: #f8f9fa !important;
        color: #6c757d !important;
        cursor: not-allowed !important;
    }

    /* AANGEPASTE PRICE DISPLAY - Nu hetzelfde als disabled form-control inputs */
    .price-display {
        background-color: #f8f9fa !important;
        border: 1px solid #ced4da !important;
        border-radius: 0 !important;
        padding: 0.375rem 0.75rem !important;
        font-size: 1rem !important;
        font-weight: 400 !important;
        line-height: 1.5 !important;
        color: #6c757d !important;
        width: 100% !important;
        height: calc(1.5em + 0.75rem + 2px) !important;
        display: flex !important;
        align-items: center !important;
        cursor: not-allowed !important;
        opacity: 1 !important;
        box-sizing: border-box !important;
    }


    @media (max-width: 767px) {
        input[type="date"],
        input[type="time"],
        input[type="number"],
        input[type="text"],
        input[disabled],
        .form-control {
            -webkit-appearance: none !important;
            appearance: none !important;
            background-color: #fff !important;
            height: 40px !important;
            width: 100% !important;
            font-size: 16px !important;
            padding: 0 8px !important;
            box-sizing: border-box !important;
            border: 1px solid #ced4da !important;
            border-radius: 0 !important;
        }

        select.form-control {
            -webkit-appearance: auto !important;
            appearance: auto !important;
            padding-right: 20px !important;
        }

        input::-webkit-date-and-time-value {
            margin-left: 0 !important;
            text-align: left !important;
        }

        input[type="time"]::-webkit-datetime-edit {
            padding: 0 !important;
        }

        .col-4 {
            width: 100% !important;
            max-width: 100% !important;
            flex: 0 0 100% !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            margin-bottom: 10px !important;
        }

        .col-8, .col-12 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        input[disabled],
        input[data-pickup-field="true"] {
            background-color: #f8f9fa !important;
            color: #6c757d !important;
        }

        /* MOBILE: Price display styling - aangepast om overeen te komen met andere disabled inputs */
        .price-display {
            height: 40px !important;
            background-color: #f8f9fa !important;
            color: #6c757d !important;
            border: 1px solid #ced4da !important;
            border-radius: 0 !important;
            padding: 0 8px !important;
            font-size: 16px !important;
        }

        label {
            display: block !important;
            margin-bottom: 5px !important;
            font-weight: normal !important;
        }

        .row.mt-10 {
            margin-top: 15px !important;
        }

        /* Mobile date input enhancements */
        input[type="date"] {
            font-size: 16px !important; /* Prevents zoom on iOS */
            padding: 8px !important;
        }
    }

    button[type="submit"]:disabled,
    button[onclick*="publishAdvert"]:disabled,
    .btn.disabled {
        opacity: 0.7 !important;
        cursor: not-allowed !important;
        background-color: #ccc !important;
        border-color: #aaa !important;
        pointer-events: none !important;
    }

    .field-error {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }

    /* Advertentie disclaimer lijst styling */
    .advert-disclaimer-list {
        list-style-type: disc;
        padding-left: 1.5rem;
        margin: 0;
    }

    .advert-disclaimer-list li {
        margin-bottom: 0.5rem;
        line-height: 1.5;
    }

    .advert-disclaimer-list li:last-child {
        margin-bottom: 0;
    }

    .advert-disclaimer-list a {
        text-decoration: underline;
    }
</style>

<?php $maxDate = new DateTime;
$maxDate = $maxDate->modify('+3 months')->format('Y-m-d'); ?>
<div class="container mt-20">
    <div class="row">
        <div class="col-8 offset-2">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="@if ($edit) {{route('dashboard.adverts.update.store', $advert->getUuid())}} @else {{route('dashboard.adverts.store')}} @endif" method="POST" id="form" enctype="multipart/form-data" class="form-box">
                @csrf
                @if ($edit)
                    @method('PATCH')
                @endif
                <div class="row">
                    <div class="col-12">
                        <label for="dishes">Selecteer gerecht</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <select id="dishes" name="dish" onchange="dishSelect()" class="form-control" @if($edit) disabled @endif @if(isset($advert)) style="-webkit-appearance: none;" @else style="-webkit-appearance: auto;" @endif>
                            @if ($dishes->isEmpty())
                                <option value="">Er zijn nog geen gerechten aangemaakt</option>
                            @else
                                <option value="">Selecteer gerecht</option>
                            @endif

                            @foreach($dishes as $dish)
                                <option value="{{$dish->getUuid()}}" @if (  $dish->getUuid() == $selectedDish || (old('dish') === $dish->getUuid()) || isset($advert) && $advert->getDishUuid() === $dish->getUuid() ) selected @endif>{{$dish->getTitle()}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row" id="dishImageRow" style="@if(!$edit || !isset($advert)) display: none; @endif">
                    <div class="col-12">
                        <img src="@if($edit && isset($advert)) {{ $advert->dish?->image?->getCompletePath() ?? url('/img/pasta.jpg') }} @endif" id="dishImage" class="image gray-background" alt="">
                    </div>
                </div>
                <div class="row" id="dishInfoRow" style="@if(!$edit || !isset($advert)) display: none; @endif">
                    <div class="col-12">
                        <span id="dishTitle"></span><span id="dishUuid"></span>
                    </div>
                </div>
                <div class="row" id="dishDescriptionRow" style="@if(!$edit || !isset($advert)) display: none; @endif">
                    <div class="col-12">
                        <span id="dishDescription"></span>
                    </div>
                </div>
                <div class="row" id="dishTypesRow" style="@if(!$edit || !isset($advert)) display: none; @endif">
                    <div class="col-12">
                        <span id="dishTypes"></span>
                    </div>
                </div>
                <div class="row mb-10" id="dishSpicyRow" style="@if(!$edit || !isset($advert)) display: none; @endif">
                    <div class="col-12">
                        <span class="dishSpicy" id="dishSpicy"></span>
                    </div>
                </div>

                <div class="row mt-10" id="dishPriceRow" style="@if(!$edit || !isset($advert)) display: none; @endif">
                    <div class="col-12">
                        <label>
                            Prijs per portie
                            <span class="tooltip">
                                <i class="fa fa-info-circle"></i>
                                <span class="tooltiptext">Prijs wordt bepaald bij het gerecht en is inclusief BTW</span>
                            </span>
                        </label>
                        <div class="price-display" id="dishPriceDisplay">
                            @if($edit && isset($advert))
                                €{{ number_format($advert->getPortionPrice(), 2, ',', '.') }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <label for="available">Aantal porties beschikbaar<span class="required"></span></label>
                        <input type="number" id="available" step="1" name="available" min="1" max="25" class="form-control" value="{{ (isset($advert)) ? $advert->getPortionAmount() : old('available')}}" required>
                    </div>
                </div>

                <div class="row mt-10">
                    <div class="col-4">
                    <label for="order_date">
                        Uiterste bestelmoment
                        <span class="tooltip">
                            <i class="fa fa-info-circle"></i>
                                <span class="tooltiptext">Dit is tevens het laatste moment waarop een wijziging of annulering kan worden doorgevoerd door Thuiskok en/of de klant.</span>
                                </span>
                                <span class="required"></span>
                            </label>
                        <input type="date" id="order_date" name="order_date" value="{{ isset($advert) ? $advert->getOrderDate() : old('order_date') }}" min="{{ date('Y-m-d') }}" max="{{ $maxDate }}" class="form-control" required>
                    </div>
                    <div class="col-4">
                        <label for="order_time">Tot<span class="required"></span></label>
                        <input type="time" id="order_time" name="order_time" value="{{isset($advert) ? $advert->getOrderTime() : old('order_time')}}" class="form-control" required>
                    </div>
                </div>
                <div class="row mt-10">
                    <div class="col-4">
                        <label for="pickup_date">Afhaaldatum<span class="required"></span></label>
                        <input type="date"
                               id="pickup_date"
                               name="pickup_date"
                               value="{{ isset($advert) ? $advert->getPickupDate() : old('pickup_date') }}"
                               class="form-control"
                               min="<?= date('Y-m-d') ?>"
                               max="{{$maxDate}}"
                               required
                               @if($edit)
                                   readonly
                                   disabled
                                   data-pickup-field="true"
                                   tabindex="-1"
                               @endif>
                    </div>
                    <div class="col-4">
                        <label for="pickup_from">Van<span class="required"></span></label>
                        <input type="time"
                               id="pickup_from"
                               name="pickup_from"
                               value="{{isset($advert) ? $advert->getPickupFrom() : old('pickup_from')}}"
                               class="form-control"
                               required
                               @if($edit)
                                   readonly
                                   disabled
                                   data-pickup-field="true"
                                   tabindex="-1"
                               @endif>
                    </div>
                    <div class="col-4">
                        <label for="pickup_to">Tot<span class="required"></span></label>
                        <input type="time"
                               id="pickup_to"
                               name="pickup_to"
                               value="{{isset($advert) ? $advert->getPickupTo() : old('pickup_to')}}"
                               class="form-control"
                               required
                               @if($edit)
                                   readonly
                                   disabled
                                   data-pickup-field="true"
                                   tabindex="-1"
                               @endif>
                    </div>
                </div>
                <div class="row mt-10">
                    @if(isset($adress))
                        <div class="col-4">
                            <label for="adress">Adres<span class="required"></span></label>
                            <input disabled distype="date" id="adress" class="form-control"  value='{{$adress}}' required>
                        </div>
                    @endif
                    @if(isset($phone))
                        <div class="col-4">
                            <label for="tel">Telefoonnummer<span class="required"></span></label>
                            <input disabled id="tel" class="form-control" value="{{$phone}}"required>
                        </div>
                    @endif
                    @if(isset($email))
                        <div class="col-4">
                            <label for="email">E-mail<span class="required"></span></label>
                            <input  disabled id="email" class="form-control" value='{{$email}}' required>
                        </div>
                    @endif
                </div>
                <input type="text" id="concept" name="concept" class="hide">
                <div class="row mt-30 text-center justify-center">
                    <div class="advert-disclaimer text-left mb-3">
                        <p class="mb-2"><strong>Door deze advertentie te plaatsen verklaar ik dat:</strong></p>
                        <ul class="advert-disclaimer-list">
                            <li>Ik akkoord ga met de <a href="{{ route('terms.conditions') }}" target="_blank">algemene voorwaarden</a> en dat de informatie in deze advertentie juist is</li>
                            <li>De maaltijd is bereid volgens de geldende hygiëne- en voedselveiligheidsrichtlijnen (<a href="{{ route('cook.facts') }}" target="_blank">HACCP</a>)</li>
                            <li>Ingrediënten en allergenen volledig en correct zijn vermeld</li>
                            <li>Ik verantwoordelijk ben voor de voedselveiligheid van de aangeboden maaltijd</li>
                        </ul>
                    </div>

                    @if (isset($advert))
                        <button type="submit" class="btn btn-small btn-outline m-0" onclick="storeAdvert({{$concept}});">Advertentie wijzigen</button>
                        @if($active)
                            @if($published)
                                <button type="button" class="btn btn-small btn-orange" onclick="publishAdvert(true);">Plaats advertentie online</button>
                            @endif
                            <a href="{{route('dashboard.adverts.cancel', $advert->getUuid())}}" id="cancelAdvertBtn" class="btn btn-small btn-outline m-0">Annuleer advertentie</a>
                        @else
                        @endif
                    @else
                        @if($canCreateMoreAdverts)
                            @if($todayAdvertCount >= 25)
                                <div class="alert alert-warning text-center">
                                    <strong>Je hebt het maximum van 25 advertenties voor vandaag bereikt. Kom morgen terug om nieuwe advertenties te plaatsen.</strong>
                                </div>
                            @else
                                @if(!$hasRequiredInfo)
                                    <div class="alert alert-warning text-center">
                                        <strong>Je kunt een advertentie online plaatsen wanneer de volgende informatie is toegevoegd aan je account:</strong><br>
                                        1. Instellingen > Gegevens<br>
                                        2. Portemonnee > IBAN
                                    </div>
                                @else
                                    @if($active)
                                        <button type="button" class="btn btn-small btn-orange" onclick="publishAdvert(true);">Plaats advertentie online</button>
                                    @else
                                        <button type="button" class="btn btn-small btn-orange" onclick="publishAdvert(false);">Plaats advertentie online</button>
                                    @endif
                                @endif
                            @endif
                        @else
                            <div class="alert alert-warning text-center">
                                <strong>De limiet van 25 advertenties per dag is bereikt, morgen kan je weer advertenties aanmaken.</strong>
                            </div>
                        @endif
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('page.scripts')
<script>
   // Global variables
   let formSubmitted = false;
   let isProcessing = false;
   let formHasBeenSubmitted = false;

   document.addEventListener('DOMContentLoaded', function () {
       console.log('Script loaded');

       @if(auth()->user()->privacy)
           @if(auth()->user()->privacy->place && auth()->user()->privacy->street && auth()->user()->privacy->house_number)
               // FIXED: Add null check
               const addressRow = document.getElementById('addressRow');
               if (addressRow) {
                   addressRow.style.display = 'table-row';
               }
           @endif
           @if(auth()->user()->privacy->phone)
               // FIXED: Add null check
               const phoneRow = document.getElementById('phoneRow');
               if (phoneRow) {
                   phoneRow.style.display = 'table-row';
               }
           @endif
       @endif

       @if($edit)
           const pickupFields = document.querySelectorAll('[data-pickup-field="true"]');
           pickupFields.forEach(field => {
               field.tabIndex = -1;

               field.addEventListener('input', function(e) {
                   e.preventDefault();
                   e.stopPropagation();
                   return false;
               });

               field.addEventListener('change', function(e) {
                   e.preventDefault();
                   e.stopPropagation();
                   return false;
               });

               field.addEventListener('keydown', function(e) {
                   e.preventDefault();
                   e.stopPropagation();
                   return false;
               });

               field.addEventListener('keyup', function(e) {
                   e.preventDefault();
                   e.stopPropagation();
                   return false;
               });

               field.addEventListener('focus', function(e) {
                   e.target.blur();
               });

               field.addEventListener('touchstart', function(e) {
                   e.preventDefault();
                   e.stopPropagation();
                   return false;
               });

               field.addEventListener('touchend', function(e) {
                   e.preventDefault();
                   e.stopPropagation();
                   return false;
               });

               field.addEventListener('click', function(e) {
                   e.preventDefault();
                   e.stopPropagation();
                   return false;
               });
           });
       @endif

       const tooltipIcons = document.querySelectorAll('.tooltip i');
       tooltipIcons.forEach(icon => {
           icon.addEventListener('click', function(e) {
               e.preventDefault();
               e.stopPropagation();
               return false;
           });

           icon.addEventListener('touchend', function(e) {
               e.preventDefault();
               e.stopPropagation();
               return false;
           });
       });

       const dateTimeInputs = document.querySelectorAll('input[type="date"], input[type="time"]');
       dateTimeInputs.forEach(input => {
           input.style.width = '100%';
           setTimeout(() => {
               input.style.width = '100%';
           }, 100);
       });

       const cancelAdvertBtn = document.getElementById('cancelAdvertBtn');
       if (cancelAdvertBtn) {
           let cancelClicked = false;

           cancelAdvertBtn.addEventListener('click', function(e) {
               if (!cancelClicked) {
                   cancelClicked = true;
                   this.style.pointerEvents = 'none';
                   this.style.opacity = '0.7';
                   this.innerHTML = 'Bezig met verwerken...';
                   this.classList.add('disabled');

                   return true;
               } else {
                   e.preventDefault();
                   return false;
               }
           });
       }

       const orderDate = document.getElementById('order_date');
       const orderTime = document.getElementById('order_time');
       const pickupDate = document.getElementById('pickup_date');
       const pickupFrom = document.getElementById('pickup_from');

       // FIXED: Add null checks for all elements
       if (orderDate) {
           orderDate.addEventListener('change', function() {
               validateOrderDateTime();
               updatePickupDate();
           });
           orderDate.addEventListener('blur', validateOrderDateTime);
       }

       if (orderTime) {
           orderTime.addEventListener('change', validateOrderDateTime);
           orderTime.addEventListener('blur', validateOrderDateTime);
       }

       if (pickupDate && !pickupDate.hasAttribute('disabled')) {
           pickupDate.addEventListener('change', function() {
               validateOrderDateTime();
           });
       }

       // Mobile-specific enhancements
       if ('ontouchstart' in window) {
           console.log('Mobile device detected, adding touch support');

           if (orderDate) {
               orderDate.addEventListener('touchend', function() {
                   setTimeout(() => {
                       validateOrderDateTime();
                       updatePickupDate();
                   }, 100);
               });
           }

           if (pickupDate && !pickupDate.hasAttribute('disabled')) {
               pickupDate.addEventListener('touchend', function() {
                   setTimeout(() => {
                       validateOrderDateTime();
                   }, 100);
               });
           }

           window.addEventListener('orientationchange', function() {
               setTimeout(() => {
                   if (orderDate && orderDate.value && pickupDate && pickupDate.value) {
                       validateOrderDateTime();
                   }
               }, 500);
           });
       }

       // Solution 2: Page cache detection and validation
       window.addEventListener('pageshow', function(event) {
           if (event.persisted) {
               console.log('Page loaded from cache (back button), validating...');
               if (orderDate && orderDate.value && pickupDate && pickupDate.value) {
                   validateOrderDateTime();
               }
               // Reset form submission state
               formHasBeenSubmitted = false;
               formSubmitted = false;
               isProcessing = false;
           }
       });

       // Validate on page load
       window.addEventListener('load', function() {
           if (orderDate && orderDate.value && pickupDate && pickupDate.value) {
               validateOrderDateTime();
           }
       });

       function validateOrderDateTime() {
           // FIXED: Add null checks
           if (!orderDate || !pickupDate || !pickupFrom) {
               return true;
           }

           clearErrors();

           const orderDateValue = orderDate.value;
           const orderTimeValue = orderTime ? orderTime.value : '';
           const today = new Date().toISOString().split('T')[0];

           if (orderDateValue && orderDateValue < today) {
               showError('order_date');
               return false;
           }

           if (orderDateValue && orderTimeValue) {
               const orderDateTime = new Date(orderDateValue + ' ' + orderTimeValue);
               const now = new Date();

               if (orderDateTime <= now) {
                   showError('order_date');
                   return false;
               }

               const pickupDateValue = pickupDate.value;
               const pickupFromValue = pickupFrom.value;

               if (pickupDateValue && pickupFromValue) {
                   const pickupDateTime = new Date(pickupDateValue + ' ' + pickupFromValue);

                   // FIXED: Allow same day pickup if pickup time is after order time
                   if (orderDateValue === pickupDateValue) {
                       // Same day: pickup time must be after order time
                       if (pickupFromValue <= orderTimeValue) {
                           showError('order_date');
                           return false;
                       }
                   } else {
                       // Different days: pickup date must be after order date
                       if (orderDateTime >= pickupDateTime) {
                           showError('order_date');
                           return false;
                       }
                   }

                   // FIXED: Better 7-day validation check for same day scenarios
                   if (orderDateValue === pickupDateValue) {
                       // Same day is always valid if time validation passed
                       console.log('Same day pickup allowed');
                   } else {
                       // Different days: check if within 7 days
                       const orderDateOnly = new Date(orderDateValue + ' 00:00:00');
                       const pickupDateOnly = new Date(pickupDateValue + ' 00:00:00');
                       const daysDifference = Math.ceil((pickupDateOnly - orderDateOnly) / (1000 * 60 * 60 * 24));

                       console.log('Days difference:', daysDifference);
                       if (daysDifference > 7) {
                           showError('order_date');
                           return false;
                       }
                   }
               }
           }

           return true;
       }

       function showError(fieldId) {
           // No visual feedback - validation errors only shown via Laravel's error handling
       }

       function clearErrors() {
           // No visual feedback to clear
       }

       function updatePickupDate() {
           // FIXED: Add null checks
           if (!orderDate || !pickupDate) {
               return;
           }

           console.log('updatePickupDate called');
           const orderDateValue = orderDate.value;
           console.log('Order date value:', orderDateValue);

           const pickupDateField = document.getElementById('pickup_date');
           console.log('Pickup date field found:', pickupDateField);
           console.log('Pickup date field disabled:', pickupDateField ? pickupDateField.hasAttribute('disabled') : 'null');

           if (orderDateValue && pickupDateField && !pickupDateField.hasAttribute('disabled')) {
               const orderDateObj = new Date(orderDateValue);
               // Maximum date should be 7 days AFTER order date
               const maxDate = new Date(orderDateValue);
               maxDate.setDate(maxDate.getDate() + 7);

               const formattedMaxDate = maxDate.toISOString().split('T')[0];
               console.log('Setting pickup max date to:', formattedMaxDate);

               // FIXED: Allow same day pickup - minimum is the order date itself
               const minDate = new Date(orderDateValue);
               const formattedMinDate = minDate.toISOString().split('T')[0];

               pickupDateField.min = formattedMinDate; // Same day allowed
               pickupDateField.max = formattedMaxDate;

               // Mobile-specific: Force attribute update
               pickupDateField.setAttribute('min', formattedMinDate);
               pickupDateField.setAttribute('max', formattedMaxDate);

               console.log('Pickup field min:', pickupDateField.min);
               console.log('Pickup field max:', pickupDateField.max);

               // Clear pickup date if it's outside the allowed range
               if (pickupDateField.value) {
                   const currentPickupDate = new Date(pickupDateField.value);
                   if (currentPickupDate < orderDateObj || currentPickupDate > maxDate) {
                       pickupDateField.value = '';
                       console.log('Cleared pickup date as it was outside allowed range');
                   }
               }
           }
       }

       // Call updatePickupDate on initial load if order date exists
       if (orderDate && orderDate.value) {
           updatePickupDate();
       }
   });

   // Dish selection and display logic
   const selector = document.getElementById('dishes');
   const dishes = <?php echo json_encode($dishes); ?>;
   const dishTitle = document.getElementById('dishTitle');
   const dishImage = document.getElementById('dishImage');
   const dishId = document.getElementById('dishUuid');
   const dishDescription = document.getElementById('dishDescription');
   const dishTypes = document.getElementById('dishTypes');
   const dishSpicy = document.getElementById('dishSpicy');
   const concept = document.getElementById('concept');
   const form = document.getElementById('form');

   const dishPriceDisplay = document.getElementById('dishPriceDisplay');
   const dishPriceRow = document.getElementById('dishPriceRow');

   const dishImageRow = document.getElementById('dishImageRow');
   const dishInfoRow = document.getElementById('dishInfoRow');
   const dishDescriptionRow = document.getElementById('dishDescriptionRow');
   const dishTypesRow = document.getElementById('dishTypesRow');
   const dishSpicyRow = document.getElementById('dishSpicyRow');

   if (selector && selector.value !== '') {
       dishSelect();
   }

   function dishSelect() {
       if (!selector || selector.value === '') {
           if (dishImageRow) dishImageRow.style.display = 'none';
           if (dishInfoRow) dishInfoRow.style.display = 'none';
           if (dishDescriptionRow) dishDescriptionRow.style.display = 'none';
           if (dishTypesRow) dishTypesRow.style.display = 'none';
           if (dishSpicyRow) dishSpicyRow.style.display = 'none';
           if (dishPriceRow) dishPriceRow.style.display = 'none';
           return;
       }

       let dish = dishes.find(function (dish, index) {
           if (dish.uuid === selector.value) {
               return true;
           }
       });

       if (!dish) return;

       if (dishImageRow) dishImageRow.style.display = 'block';
       if (dishInfoRow) dishInfoRow.style.display = 'block';
       if (dishDescriptionRow) dishDescriptionRow.style.display = 'block';
       if (dishTypesRow) dishTypesRow.style.display = 'block';
       if (dishSpicyRow) dishSpicyRow.style.display = 'block';
       if (dishPriceRow) dishPriceRow.style.display = 'block';

       if (dishTitle) dishTitle.innerHTML = dish.title;
       if (dishId) dishId.innerHtml = dish.uuid;
       if (dishDescription) dishDescription.innerHTML = dish.description;

       if (dishPriceDisplay && dish.portion_price) {
           const formattedPrice = new Intl.NumberFormat('nl-NL', {
               style: 'currency',
               currency: 'EUR'
           }).format(dish.portion_price);
           dishPriceDisplay.innerHTML = formattedPrice;
       }

       let alcohol = dish.has_alcohol;
       let gluten = dish.has_gluten;
       let lactose = dish.has_lactose;
       let halal = dish.is_halal;
       let vegan = dish.is_vegan;
       let vegetarian = dish.is_vegetarian;
       let spice = dish.spice_level;
       let item = '';

       if (alcohol) {
           item = item + '<span class="has-marker alcohol">alcohol</span>';
       }

       if (gluten) {
           item = item + '<span class="has-marker gluten">gluten</span>';
       }

       if (lactose) {
           item = item + '<span class="has-marker lactose">lactose</span>';
       }

       if (halal) {
           item = item + '<span class="has-marker halal">halal</span>';
       }

       if (vegan) {
           item = item + '<span class="has-marker vegan">vegan</span>';
       }

       if (vegetarian) {
           item = item + '<span class="has-marker vegetarian">vegetarian</span>';
       }

       if (dishTypes) dishTypes.innerHTML = item;
       let spicyItems = '';

        for (let i = 0; i < spice; i++) {
           spicyItems = spicyItems + '<i class="fa-solid fa-pepper-hot" style="color: #dc3545; margin-right: 2px;"></i>';
       }
       for (let i = spice; i < 3; i++) {
           spicyItems = spicyItems + '<i class="fa-solid fa-pepper-hot" style="color: #ccc; opacity: 0.3; margin-right: 2px;"></i>';
       }
       if (dishSpicy) dishSpicy.innerHTML = spicyItems;

       if (dish.image && dish.image !== null && dishImage) {
           let imagePath = '';

           if (dish.image.path && dish.image.name) {
               imagePath = '/' + dish.image.path + '/' + dish.image.name;
           } else if (dish.image.complete_path) {
               imagePath = dish.image.complete_path;
           } else if (typeof dish.image === 'string') {
               imagePath = dish.image;
           }

           if (imagePath) {
               dishImage.src = imagePath;
           } else {
               dishImage.src = '{{ url('/img/pasta.jpg') }}';
           }
       } else if (dishImage) {
           dishImage.src = '{{ url('/img/pasta.jpg') }}';
       }
   }

   // Enhanced form submission with robust duplicate prevention
   function publishAdvert(publish) {
       if (isProcessing || formHasBeenSubmitted) {
           console.log('Form already submitted or processing, preventing duplicate');
           return false;
       }

       if (event) {
           event.preventDefault();
       }

       if (!formSubmitted) {
           formSubmitted = true;
           isProcessing = true;
           formHasBeenSubmitted = true;

           const allButtons = document.querySelectorAll('button[type="submit"], button[onclick*="publishAdvert"]');
           allButtons.forEach(btn => {
               btn.disabled = true;
               btn.innerHTML = 'Bezig met verwerken...';
               btn.style.opacity = '0.7';
               btn.style.cursor = 'not-allowed';
           });

           if (concept) concept.value = true;

           setTimeout(() => {
               if (form) form.submit();
           }, 100);

           // Longer timeout for reset to prevent rapid clicking
           setTimeout(() => {
               formSubmitted = false;
               isProcessing = false;
               allButtons.forEach(btn => {
                   btn.disabled = false;
                   btn.innerHTML = 'Plaats advertentie online';
                   btn.style.opacity = '1';
                   btn.style.cursor = 'pointer';
               });
           }, 5000);
       }
   }

   function storeAdvert(value) {
       if (isProcessing || formHasBeenSubmitted) {
           console.log('Form already submitted or processing, preventing duplicate');
           return false;
       }

       // Clear previous errors first
       clearErrors();

       // Validate form before submission
       if (!validateOrderDateTime()) {
           console.log('Validation failed, not submitting form');
           return false;
       }

       if (!formSubmitted) {
           formSubmitted = true;
           isProcessing = true;
           formHasBeenSubmitted = true;

           if (concept) concept.value = value;

           if (event && event.target) {
               const submitBtn = event.target;
               submitBtn.disabled = true;
               submitBtn.innerHTML = 'Bezig met verwerken...';
               submitBtn.style.opacity = '0.7';
           }

           setTimeout(() => {
               if (form) form.submit();
           }, 100);

           setTimeout(() => {
               if (event && event.target) {
                   const submitBtn = event.target;
                   submitBtn.disabled = false;
                   submitBtn.innerHTML = 'Advertentie wijzigen';
                   submitBtn.style.opacity = '1';
               }
               isProcessing = false;
               formSubmitted = false;
           }, 5000);

           return true;
       } else {
           if (event && event.preventDefault) {
               event.preventDefault();
           }
           return false;
       }
   }

   function showConfirmation() {
       if (typeof $ !== 'undefined' && $('#confirmationModal').length) {
           $('#confirmationModal').modal('show');
       }
   }

   function submitForm() {
       if (form) form.submit();
   }

   function cancelModal() {
       if (typeof $ !== 'undefined' && $('#confirmationModal').length) {
           $('#confirmationModal').modal('hide');
       }
   }

   // Solution 4: Reset state on page unload
   window.addEventListener('beforeunload', function() {
       formHasBeenSubmitted = false;
       formSubmitted = false;
       isProcessing = false;
   });

   // Clear browser history state
   if (window.history.replaceState) {
       window.history.replaceState(null, null, window.location.href);
   }
</script>
@endsection
