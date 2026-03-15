@extends('layout.dashboard')

@section('dashboard')
<style>
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
        gap: 1rem;
        margin-top: 0.5rem;
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
</style>

    <div class="page-header neg-header">
        <div class="container"><h1>Bestellingen</h1></div>
    </div>

    <div class="container mt-30">
        <div class="row">
            <div class="col f">
                <button id='filter' class="btn-outline"><i class="fa-solid fa-filter"></i></button> 
            </div>
        </div>
        <form id='filter-options' action="{{route('dashboard.orders.home')}}" method="GET" class="row w-100">
            <div class="col-2">Van datum</div>
            <div class="col-3"><input type="date" name="from" id="from" min="{{ $min }}" value="{{$from?->format('Y-m-d')}}"></div>
            <div class="col-1">Tot datum</div>
            <div class="col-3"><label for="to"></label><input type="date" name="to" id="to" min="<?= date('Y-m-d') ?>" value="{{$to?->format('Y-m-d')}}"></div>
            <div class="col-3"><button class="btn-outline">Filter toepassen</button></div>
        </form>
        <div class="row mt-20">
            <div class="col-12 table">
                <div class="row table-header hide-on-mobile">
                    <div class="col"><h5>Bestelnummer</h5></div>
                    <div class="col"><h5>Bestelmoment</h5></div>
                    <div class="col">Klantnaam</div>
                    <div class="col">Gerechtnaam</div>
                    <div class="col">Aantal porties</div>
                    <div class="col">Afhaalmoment</div>
                    <div class="col">Opmerking</div>
                    <div class="col">Status</div>
                </div>
            </div>
        </div>
        @foreach($orders as $order)
            <div class="row border mb-3 p-2 w-100 table-row">
                <div class="col-12">
                    <!-- Mobile view -->
                    <div class="d-block d-md-none">
                        <!-- Main visible content -->
                        <div class="mobile-field-horizontal">
                            <span class="mobile-label">Bestelnummer:</span>
                            <a href="{{route('dashboard.orders.show', $order->getUuid())}}" class="orange-link-bold">
                                {{str_replace('Bestelnummer: ', '', $order->getParsedOrderUuid())}}
                            </a>
                        </div>
                        <div class="mobile-field">
                            <span class="mobile-content">{{$order->getCreatedAt()->translatedFormat('d-m-Y H:i')}}</span>
                        </div>
                        <div class="mobile-field">
                            <span class="mobile-content">{{$order->dish->getTitle()}}</span>
                        </div>
                        <div class="mobile-field">
                            <span class="mobile-content">{{$order->client->getName()}}</span>
                        </div>
                        <div class="mobile-field">
                            <span class="mobile-content">{{$order->getPortionAmount()}} porties</span>
                        </div>
                        <div class="mobile-field">
                        <span class="mobile-content">{{$order->getExpectedPickupTime()->translatedFormat('d-m-Y / H:i')}} uur</span>
                        </div>
                        <div class="status-container">
                            <span class="status-badge">{{ $order->getStatus() }}</span>
                            <a href="#" type="button" data-bs-toggle="collapse" 
                               data-bs-target="#collapseOrder{{$order->getUuid()}}" 
                               aria-expanded="false" 
                               aria-controls="collapseOrder{{$order->getUuid()}}"
                               class="expand-button">
                                <i class="fa-solid fa-angles-down"></i>
                            </a>
                        </div>

                        <!-- Collapsible content -->
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
                    
                    <!-- Desktop view -->
                    <div class="row align-center d-none d-md-flex">
                        <div class="col">
                            <a href="{{route('dashboard.orders.show', $order->getUuid())}}" class="clickable-cell">
                                {{str_replace('Bestelnummer: ', '', $order->getParsedOrderUuid())}}
                            </a>
                        </div>
                        <div class="col">{{$order->getCreatedAt()->translatedFormat('d-m-Y H:i')}}</div>
                        <div class="col">{{$order->client->getName()}}</div>
                        <div class="col">{{$order->dish->getTitle()}}</div>
                        <div class="col">{{$order->getPortionAmount()}} porties</div>
                        <div class="col">{{$order->getExpectedPickupTime()->translatedFormat('d-m-Y / H:i')}} uur</div>
                        <div class="col">{{$order->getRemarks() ?: 'Geen opmerking aanwezig'}}</div>
                        <div class="col">{{ $order->getStatus() }}</div>
                    </div>
                </div>
            </div>
        @endforeach
        {{$orders->links()}}
    </div>
@endsection
@section('page.scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterButton = document.getElementById('filter');
        const filterOptions = document.getElementById('filter-options');

        filterButton.addEventListener('click', function(event) {
            event.stopPropagation();
            if (filterOptions.style.display === 'none' || filterOptions.style.display === '') {
                filterOptions.style.display = 'inline-flex';
                filterButton.innerHTML = '<i class="fa-solid fa-times"></i>';
            } else {
                filterOptions.style.display = 'none';
                filterButton.innerHTML = '<i class="fa-solid fa-filter" aria-hidden="true"></i>';
            }
        });

        document.addEventListener('click', function(event) {
            const isClickInsideFilterOptions = filterOptions.contains(event.target);
            const isClickInsideFilterButton = filterButton.contains(event.target);
            if (!isClickInsideFilterOptions && !isClickInsideFilterButton) {
                filterOptions.style.display = 'none';
                filterButton.innerHTML = '<i class="fa-solid fa-filter" aria-hidden="true"></i>';
            }
        });
    });
</script>
@endsection