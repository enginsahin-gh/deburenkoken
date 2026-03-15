@extends('layout.dashboard')
@section('dashboard')
    <div class="page-header neg-header">
        <div class="container"><h1>{{$advert->dish->getTitle()}}</h1></div>
    </div>
    <div class="breadcrumbs">
        <div class="container">
            <a href="{{route('home')}}">Home</a> 
            <a href="{{route('dashboard.adverts.active.home')}}">Terug</a>
        </div>
    </div>
    <section class="clearfix single-dish">
        <div class="container">
            <div class="row mt-2">
                <div class="col-3">
                    <div class="img-holder">
                        <img src="{{ $advert->dish->image?->getCompletePath() ?? url('/img/pasta.jpg') }}">
                        <div class="nog-content">
                            @if($advert->getParsedOrderTo()->isPast())
                                Uiterlijke bestelmoment verlopen
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
                                <p class="m-0"> {{$advert->getParsedAdvertUuid()}}</p>
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
                    
                    <div class="price">
                        <span>Prijs:</span> <b>€ {{$advert->getPortionPrice()}}</b>
                    </div>

                    <div class="row mt-10">
                        <div class="col-12 descr">
                            <b>Omschrijving:</b>
                            <p>{{$advert->dish->getDescription()}}</p>
                        </div>
                    </div>

                    @if(request()->user()->hasRole('admin'))
                        <div class="row">
                            <div class="col-12">
                                <a href="{{route('dashboard.admin.adverts.update', $advert->getUuid())}}" class="order-button text-center" style="background: linear-gradient(to right, #f3723b 0%, #e54750 100%); color: #fff; border: 2px solid #f3723b; padding: 8px 15px; width: 200px; margin: 10px auto 30px; border-radius: 6px; display: inline-block;">Wijzig advertentie</a>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-3 line">                
                    @if (!is_null($user->cook))
                    <a href="{{route('search.cooks.detail', $user->cook?->getUuid())}}" class="d-block align-center justify-center">
                        <div class="row">
                            <div class="col-4 d-flex text-left mb-10">
                                <div class="round-image-container">
                                    <img src="{{ $user->image?->getCompletePath() ?? url('/img/kok.png') }}" />
                                </div>
                            </div>
                            <div class="col-8 text-left">
                                <b>{{$user->getUsername()}}</b>
                                <div class="row d-inline star-font m-0">
                                    @php $rating = $user->reviews->avg('rating') ?? 0; @endphp
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
                                    <span class="review-count">({{$user->reviews->count()}})</span>
                                </div>
                            </div>
                        </div>    
                    </a>  
                    @endif

                    <div class="row">
                        <div class="col-12">
                            @if(request()->user()->hasRole('admin'))
                                <div class="row">
                                    <a href="{{route('dashboard.admin.adverts.update', $advert->getUuid())}}" class="order-button text-center" style="background: linear-gradient(to right, #f3723b 0%, #e54750 100%); color: #fff; border: 2px solid #f3723b; padding: 8px 15px; width: 200px; margin: 10px auto 30px; border-radius: 6px; display: inline-block;">
                                        Wijzig advertentie
                                    </a>
                                </div>
                            @elseif ($user?->getUuid() === $advert->dish->user?->getUuid() && !$past)
                                <div class="row">
                                    @if (
                                        $advert->isPublished() ||
                                        !$advert->isPublished() && $advert->getParsedOrderTo()->isFuture()
                                    )
                                        <a href="{{route('dashboard.adverts.update', $advert->getUuid())}}" class="order-button text-center" style="background: linear-gradient(to right, #f3723b 0%, #e54750 100%); color: #fff; border: 2px solid #f3723b; padding: 8px 15px; width: 200px; margin: 10px auto 30px; border-radius: 6px; display: inline-block;">
                                            Wijzig advertentie
                                        </a>
                                    @else
                                        <span class="alert alert-warning" style="line-height: 1.3;margin: 15px;">Een advertentie kan niet gewijzigd/geannuleerd worden na het uiterlijke bestelmoment.</span>
                                    @endif
                                </div>
                            @endif
                            <div class="text-left afhalen">
                                <b>Afhalen:</b> {{$advert->getParsedPickupFrom()->translatedFormat('l d-m-Y / H:i -')}} {{$advert->getParsedPickupTo()->translatedFormat('H:i')}}
                            </div>
                            <div class="d-flex justify-left">
                                <div class="distance">
                                    <i class="justify-left fa fa-map-marker"></i> 0 km
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Section with Updated Styling -->
            <div class="container mt-30">
                <h1 class="aanbod orange-heading">Bestellingen</h1>
                <div class="orange-line"></div>
                @if($orders->isEmpty())
                    <p>Geen bestelling aanwezig</p>
                @else
                    <div class="row">
                        <div class="col-12 table">
                            <!-- Desktop Header -->
                            <div class="row table-header hide-on-mobile">
                                <div class="col-2 no-right-padding-extreme"><h5>Bestelnummer</h5></div>
                                <div class="col-2 no-left-padding-extreme">Klantnaam</div>
                                <div class="col-1">Aantal porties</div>
                                <div class="col-2 verwacht-column compact-column">Verwacht<br>afhaalmoment</div>
                                <div class="col-4 no-left-padding compact-column">Opmerking</div>
                                <div class="col-1 text-right">Status</div>
                            </div>
                        </div>
                    </div>

                    @php
                        // Sorteer bestellingen met actieve eerst
                        $sortedOrders = $orders->sortBy(function($order) {
                            return $order->status === 'Actief' ? 0 : 1;
                        });
                    @endphp

                    @foreach($sortedOrders as $order)
                        <div class="row border p-2 w-100 table-row">
                            <div class="col-12">
                                <!-- Mobile View -->
                                <div class="d-block d-md-none">
                                    <div class="mobile-field-horizontal">
                                        <span class="mobile-label">Bestelnummer:</span>
                                        <a href="{{route('dashboard.orders.show', $order->getUuid())}}" class="orange-link-bold">
                                            {{str_replace('Bestelnummer: ', '', $order->getParsedOrderUuid())}}
                                        </a>
                                    </div>
                                    <div class="mobile-field">
                                        <span class="mobile-content">{{$order->client->getName()}}</span>
                                    </div>
                                    <div class="mobile-field">
                                        <span class="mobile-content">{{$order->getPortionAmount()}} porties</span>
                                    </div>
                                    <div class="mobile-field">
                                        <span class="mobile-content">{{$order->getExpectedPickupTime()->format('H:i')}}</span>
                                    </div>
                                    <div class="status-container justify-content-between">
                                        <span class="status-badge">{{ $order->getStatus() }}</span>
                                        <a href="#" type="button" data-bs-toggle="collapse" 
                                           data-bs-target="#collapseOrder{{$order->getUuid()}}" 
                                           aria-expanded="false" 
                                           aria-controls="collapseOrder{{$order->getUuid()}}"
                                           class="expand-button">
                                            <i class="fa fa-chevron-down"></i>
                                        </a>
                                    </div>
                                    <!-- Collapsible Content -->
                                    <div class="collapse" id="collapseOrder{{$order->getUuid()}}">
                                        <div class="card-body">
                                            <div class="mobile-order-details">
                                                <div class="mobile-field">
                                                    <span class="mobile-label-collapsed">Opmerking:</span>
                                                    <span class="collapse-content">{{$order->getRemarks() ?: 'Geen opmerking aanwezig'}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Desktop View -->
                                <div class="row align-center d-none d-md-flex">
                                    <div class="col-2 no-right-padding-extreme">
                                        <a href="{{route('dashboard.orders.show', $order->getUuid())}}" class="clickable-cell">
                                            {{str_replace('Bestelnummer: ', '', $order->getParsedOrderUuid())}}
                                        </a>
                                    </div>
                                    <div class="col-2 no-left-padding-extreme">{{$order->client->getName()}}</div>
                                    <div class="col-1">{{$order->getPortionAmount()}} porties</div>
                                    <div class="col-2 compact-column">{{$order->getExpectedPickupTime()->format('H:i')}} uur</div>
                                    <div class="col-4 no-left-padding compact-column">{{$order->getRemarks() ?: 'Geen opmerking aanwezig'}}</div>
                                    <div class="col-1 text-right">{{ $order->getStatus() }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>
    <style>
    .orange-heading {
        color: #f3723b;
        margin-bottom: 10px;
    }

    .pagination {
        margin-top: 20px;
        text-align: center;
    }
    
    /* Changes for stacking "Verwacht afhaalmoment" */
    .verwacht-column {
        line-height: 1.2;
        padding-right: 0;
    }
    
    /* Changes for minimizing space between columns */
    .no-right-padding {
        padding-right: 0;
    }
    
    .no-left-padding {
        padding-left: 5px;
    }
    
    /* Even more extreme padding reduction for Bestelnummer and Klantnaam */
    .no-right-padding-extreme {
        padding-right: 0;
        margin-right: -20px; 
    }
    
    .no-left-padding-extreme {
        padding-left: 0;
        margin-left: -20px; 
    }
    
    /* Reduce space between header and first row */
    .table-header {
        background-color: #f8f9fa;
        padding: 0.3rem;
        margin-bottom: 0;
        border-radius: 4px;
    }
    
    .table-row {
        margin-top: 0;
        margin-bottom: 0.5rem;
        padding-top: 0.4rem !important;
        padding-bottom: 0.4rem !important;
    }
    
    /* NEW: Compact columns for Verwacht afhaalmoment and Opmerking */
    .compact-column {
        padding-left: 0;
        padding-right: 0;
        margin-left: -4px;
        margin-right: -80px;
    }
    
    /* Right align status text */
    .text-right {
        text-align: right !important;
        margin-left: 13% !important;
    }
    
    @media (max-width: 768px) {
        .hide-on-mobile {
            display: none !important;
        }
        
        .mobile-field {
            display: block;
            margin-bottom: 0.5rem;
        }

        .mobile-field-horizontal {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .mobile-label {
            color: #f3723b;
            font-weight: bold;
            margin-right: 0.5rem;
        }

        .mobile-field a.orange-link-bold {
            color: #f3723b;
            text-decoration: underline;
        }

        .table-row {
            background-color: #fff9f6;
            padding: 1rem !important;
            margin-bottom: 1rem;
        }

        .date-value {
            margin-top: 0.25rem;
            margin-bottom: 0.5rem;
            color: black;
        }

        .status-container {
            display: flex;
            align-items: center;
            margin-top: 0.5rem;
            width: 100%;
        }

        .status-badge {
            display: inline-block;
            background-color: #FFE4B5;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .expanded-info {
            margin-top: 1rem;
            padding-top: 1rem;
        }

        .card-body {
            background-color: white;
            padding: 1rem;
            margin-top: 1rem;
            border: 1px solid #dee2e6;
        }

        .expand-button {
            display: flex;
            align-items: center;
        }

        .mobile-content {
            color: black;
        }

        .collapse-content {
            color: black !important;
        }
        
        .mobile-order-details {
            color: black;
        }

        .mobile-label-collapsed {
            color: black;
            font-weight: bold;
            margin-right: 0.5rem;
        }
        
        /* Make status container use space-between to push elements to edges */
        .justify-content-between {
            justify-content: space-between !important;
        }
    }

    @media (min-width: 769px) {
        .mobile-label {
            display: none;
        }

        .clickable-cell {
            color: #f3723b;
            text-decoration: none;
            cursor: pointer;
        }

        .clickable-cell:hover {
            text-decoration: underline;
        }
    }

    .line {
        border-left: 2px solid #ddd;
        height: 100%;
        margin-left: -10px;
    }

    .order-number-box {
        border-radius: 10px;
        position: absolute;
        background: #fff;
        color: #e14d0e;
        text-align: center;
        padding: 5px 10px;
        right: 0;
        font-size: 11px;
        line-height: 1.2;
        top: -10px;
        font-weight: bold;
        border: 1px solid #f3723b;
    }

    .order-number-box a {
        color: #333;
        text-decoration: none;
    }

    .order-number-box:hover {
        background: #f8f8f8;
    }

    .order-number-label {
        font-size: 14px;
        font-weight: 500;
        color: #f3723b;
    }

    .aanbod {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
        color: #333;
        color: #f3723b;
        margin-left: -1%;
        border-bottom: 1px solid #f3723b !important;
        max-width: 99.7%;
    }
    .mt-30 {
        margin-top: 30px !important;
    }
    </style>
@endsection