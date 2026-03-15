@extends('layout.dashboard')

@section('dashboard')
<style>
@media (max-width: 768px) {
    .hide-on-mobile {
        display: none !important;
    }
    
    .remarks-counter {
        position: absolute;
        right: -5%;
        top: -50px;
        transform: translateY(-50%);
        z-index: 10;
    }

    .table-row {
        position: relative; 
    }
    
    .mobile-field {
        display: block;
        /* margin-bottom: 0.75rem; */
    }

    .mobile-field span.mobile-label {
        display: inline;
        /* margin-right: 0.25rem; */
        color: #f3723b; 
        font-weight: bold; 
    }

    .mobile-field a.orange-link-bold {
        display: inline;
        margin-left: 0; 
        text-decoration: underline; 
        color: #f3723b; 
        /* font-weight: bold;  */
    }

    .introjs-tooltip {
        position: fixed;
        top: 25%;
        left: 65% !important;
        transform: translate(-50%, -50%);
        width: 50%;
        max-width: 90vw;
        text-align: center;
    }

    .introjs-tooltip-buttons {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        background-color: rgba(255, 255, 255, 0.9);
        padding: 10px 0;
        display: flex;
        justify-content: center;
    }

    body {
        padding-top: 50px; 
    }
}

@media (min-width: 769px) {
    .mobile-label {
        display: none;
    }
    
    .remarks-counter {
        position: absolute;
        right: 50px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
    }
}

/* Positioning for the dropdown icon and counter */
.dropdown-container {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: flex-end;
}

.remarks-counter {
    margin-right: 10px;
}

/* Stijl voor geannuleerde bestellingen zonder rode rand links */
.cancelled-order {
    opacity: 0.7;
    background-color: #f8f9fa;
}

/* Stijl voor de saldo-melding */
.saldo-notification {
    background-color: #fff3cd;
    border: 1px solid #ffeeba;
    border-radius: 4px;
    color: #856404;
    padding: 10px;
    margin: 10px 0 15px 0;
    font-size: 14px;
}
</style>


    <div class="container mt-10">
    @if (!$past )
        <div class="row">
            @if($availableCount >= 0)
            <a href="{{route('dashboard.adverts.create')}}" class="dashboard-button">
                <h4>Nieuwe advertentie</h4>
                <span class="add-sign">+</span>
            </a>
            @else
            <div class="saldo-notification">
                Je kunt weer een advertentie online plaatsen wanneer de saldo van je portemonnee positief is.
            </div>
            @endif
            <div class="col f">
                <button id='filter' class="btn-outline"><i class="fa-solid fa-filter"></i></button> 
            </div>
        </div>
    @endif
        <!-- <div class="row">
            <div class="tbl-title">Afhaaldatum</div>
        </div> -->

    @if(!$past)
        <form  id='filter-options' action="{{route('dashboard.adverts.active.home')}}" method="GET" class="row w-100">
    @else
        <form  id='filter-options' action="{{route('dashboard.adverts.past.home')}}" method="GET" class="row w-100">
    @endif

                <div class="col-1">Van</div>
                <div class="col-3"><label for="from"></label><input type="date" name="from" id="from" value="{{$from}}"></div>
                <div class="col-1">Tot</div>
                <div class="col-3"><label for="to"></label><input type="date" name="to" id="to" min="{{$min}}" value="{{$to}}"></div>
                <div class="col-4"><button class="btn-outline">Filter toepassen</button> </div>
        </form>
    </div>
    <div class="container">
        <div class="row mt-20">
            <div class="col-12 table">
            <div class="row table-header hide-on-mobile">
                <div class="col-2 hide-on-mobile">
                    <h5>Advertentienummer</h5>
                </div>
                <div class="col-2 hide-on-mobile">
                    Naam gerecht
                </div>
                <div class="col-2 hide-on-mobile">
                    Porties verkocht
                </div>
                <div class="col-3 hide-on-mobile">
                    <strong>Datum + Tijdstip afhalen</strong>
                </div>
                <div class="col-2 hide-on-mobile">
                    <strong>Status</strong>
                </div>
                <div class="col-1"></div>
            </div>
            </div>
        </div>
        @foreach($adverts as $advert)
            @if (
                    !$advert->published() && $past && is_null($advert->deleted_at) ||
                    (!$advert->isCancelled() && $advert->getParsedPickupTo()->isFuture() && $past) ||
                    ($advert->published() && $advert->getParsedOrderTo()->isPast() && $advert->getParsedPickupTo()->isPast() && !$past)
            )
                @continue;
            @endif
            <div class="row border mb-3 p-2 w-100 table-row">
                <div class="col-12">
                    <div class="row align-center">
                    <div class="col-2">
                        <div class="mobile-field">
                            <span class="mobile-label">Advertentienummer:</span>
                            <a href="{{route('dashboard.adverts.show', ['uuid' => $advert->getUuid(), 'past' => $past])}}" class="orange-link-bold">
                                {{ str_replace('Advertentienummer: ', '', $advert->getParsedAdvertUuid()) }}
                            </a>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="mobile-field">
                            <span class="mobile-label"></span>
                            {{$advert->dish->getTitle()}}
                        </div>
                    </div>
                        <div class="col-2">
                                {{ $advert->getSucceedAmount() }} / {{ $advert->getPortionAmount() }}
                                @if ($advert->published() && $advert->getParsedOrderTo()->isFuture())
                                    <i class="fa-solid fa-lock-open"></i>
                                @else
                                    <i class="fa-solid fa-lock"></i>
                                @endif
                            </div>
                            <div class="col-3">
                                {{$advert->getParsedPickupFrom()->translatedFormat('d F / H:i')}}-{{$advert->getParsedPickupTo()->translatedFormat('H:i')}}
                            </div>
                            <div class="col-2 align-middle">
                            <span class="btn btn-round-max btn-secondary yellow btn-no-border no-hover">
                            @if ($advert->isCancelled())
                                Geannuleerd
                            @elseif ($advert->published() && $advert->getParsedOrderTo()->isFuture())
                                Online
                            @elseif(
                                !$advert->isCancelled() &&
                                $advert->published() &&
                                $advert->getParsedOrderTo()->isPast() &&
                                $advert->getParsedPickupFrom()->isFuture() &&
                                $advert->getParsedPickupTo()->isFuture()
                            )
                                Voorbereiden
                            @elseif(
                                !$advert->isCancelled() &&
                                $advert->published() &&
                                $advert->getParsedOrderTo()->isPast() &&
                                $advert->getParsedPickupTo()->isFuture()
                            ) 
                                Afhalen
                            @elseif(
                                $advert->published() &&
                                $advert->getParsedOrderTo()->isPast() &&
                                $advert->getParsedPickupTo()->isPast()
                            ) 
                                Verlopen
                            @else
                                Concept
                            @endif
                        </span>
                        </div>
                        <div class="col-1 dropdown-container">
                            @if($advert->getSucceedOrderAmount() != 0 && ($advert->published() && ($advert->getParsedOrderTo()->isPast() && $advert->getParsedPickupTo()->isPast() || !$past)))
                            <div class="remarks-counter">
                                <div class="center-number text-white">{{$advert->getSucceedOrderAmount()}}</div>
                            </div>
                            @endif
                            <a href="#" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOrder{{$advert->getUuid()}}" aria-expanded="false" aria-controls="collapseOrder{{$advert->getUuid()}}">
                                <i class="fa-solid fa-angles-down"></i>
                            </a>
                        </div>
                    </div>
                    <div class="row">
                    <div class="collapse collapse-horizontal col-12 mt-20" id="collapseOrder{{$advert->getUuid()}}">
                    <?php
                        // Inclusief betaalde & geannuleerde orders
                        $hasOrders = false;
                        $orderCount = DB::table('orders')
                            ->where('advert_uuid', $advert->getUuid())
                            ->where(function($query) {
                                $query->where('payment_state', 2)  // SUCCEED
                                      ->orWhere('payment_state', 6); // PAYOUT_PENDING
                            })
                            ->count();
                        $hasOrders = ($orderCount > 0);
                    ?>
                    
                    @if($hasOrders)
                        @php
                            // Sorteer orders: actieve bestellingen boven, geannuleerde onder
                            $directOrders = DB::table('orders')
                                ->join('clients', 'orders.client_uuid', '=', 'clients.uuid')
                                ->select('orders.*', 'clients.name as client_name')
                                ->where('orders.advert_uuid', $advert->getUuid())
                                ->where(function($query) {
                                    $query->where('orders.payment_state', 2)  // SUCCEED
                                          ->orWhere('orders.payment_state', 6); // PAYOUT_PENDING
                                })
                                ->orderByRaw("CASE WHEN orders.status = 'Geannuleerd' THEN 1 ELSE 0 END") // Geannuleerde onderaan
                                ->orderBy('orders.expected_pickup_time', 'asc') // Daarna sorteren op afhaaltijd
                                ->get();
                        @endphp
                        
                        @foreach($directOrders as $order)
                            <a href="{{route('dashboard.orders.show', $order->uuid)}}">
                                <div class="card card-body mt-1 mb-2 {{ $order->status === 'Geannuleerd' ? 'cancelled-order' : '' }}">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="row">
                                                Bestelnummer: {{substr($order->uuid, -6)}}
                                            </div>
                                            <div class="row">
                                                {{$order->client_name}}
                                            </div>
                                            <div class="row">
                                                {{Carbon\Carbon::parse($order->expected_pickup_time)->format('H:i')}} uur
                                            </div>
                                            <div class="row">
                                                {{$order->portion_amount}} porties
                                            </div>
                                            <div class="row">
                                                <strong>Status: </strong> 
                                                @if($order->status === 'Geannuleerd')
                                                    <span class="text-danger">{{$order->status}}</span>
                                                @else
                                                    {{$order->status}}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-8 border-left">
                                            {{$order->remarks}}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <div class="card card-body">
                            Geen bestelling gevonden.
                        </div>
                    @endif
                    </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{$adverts->links()}}
    </div>
@endsection
@section('page.scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    var intro = introJs().onexit(function() {
        if (this._currentStep !== undefined) {
            this._currentStep = this._currentStep || 0;
            this.goToStep(this._currentStep);
        }
    }).onchange(function(targetElement) {
        setTimeout(function() {
            var tooltip = document.querySelector('.introjs-tooltip');
            if (tooltip) {
                tooltip.style.position = 'fixed';
                tooltip.style.top = '50%';
                tooltip.style.left = '50%';
                tooltip.style.transform = 'translate(-50%, -50%)';
                tooltip.style.zIndex = '9999';
            }
        }, 50);
    });
});
    document.addEventListener('DOMContentLoaded', function() {
        const filterButton = document.getElementById('filter');
        const filterOptions = document.getElementById('filter-options');
        // Voeg een click event listener toe aan de filterknop
        filterButton.addEventListener('click', function(event) {
            event.stopPropagation(); // Stop de gebeurtenis van het doorgeven aan het document
            // Controleer of de filteropties momenteel verborgen zijn
            if (filterOptions.style.display === 'none' || filterOptions.style.display === '') {
                // Toon de filteropties als ze verborgen zijn
                filterOptions.style.display = 'inline-flex';
                // Pas de tekst van de knop aan
                filterButton.innerHTML = '<i class="fa-solid fa-times"></i>';
            } else {
                // Verberg de filteropties als ze zichtbaar zijn
                filterOptions.style.display = 'none';
                // Pas de tekst van de knop aan
               filterButton.innerHTML = '<i class="fa-solid fa-filter" aria-hidden="true"></i>';
            }
        });
        // Voeg een click event listener toe aan het document om te luisteren naar klikken buiten de filteropties
        document.addEventListener('click', function(event) {
            const isClickInsideFilterOptions = filterOptions.contains(event.target);
            const isClickInsideFilterButton = filterButton.contains(event.target);
            // Controleer of er buiten de filteropties en de filterknop is geklikt
            if (!isClickInsideFilterOptions && !isClickInsideFilterButton) {
                // Verberg de filteropties als ze zichtbaar zijn
                filterOptions.style.display = 'none';
                // Pas de tekst van de knop aan
               filterButton.innerHTML = '<i class="fa-solid fa-filter" aria-hidden="true"></i>';
            }
        });
    });
</script>
@endsection