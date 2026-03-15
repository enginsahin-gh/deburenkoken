@extends('layout.dashboard')

@section('dashboard')
<style>
.btn-outline-fatt {
    background: #fff !important;
    color: #f3723b !important;
    border: 2px solid #f3723b !important;
    width: 210px !important;
    max-height: 40% !important;
    border-radius: 6px !important;
}
.btn-outline-fattt {
    background: #fff !important;
    color: #f3723b !important;
    border: 2px solid #f3723b !important;
    border-radius: 6px !important;
}

/* Responsive button styling for mobile */
@media (max-width: 767px) {
    .btn-outline-fatt, 
    .btn-outline-fattt,
    .btn-outline {
        width: 100% !important;
        margin: 0 0 10px 0 !important;
        display: block !important;
        box-sizing: border-box !important;
    }
    
    .alert-bestelling {
        display: block;
        width: 100%;
        margin-bottom: 15px;
        box-sizing: border-box;
    }
    
    .col-12 {
        padding: 0 15px;
    }
    
    .row {
        margin: 0;
    }
}
</style>
<div class="page-header neg-header">
    <div class="container"><h1>{{$order->getParsedOrderUuid()}}</h1></div>
</div>

<div class="container">
    <table class="table table-striped table-bordered mt-20">
        <tr>
            <td width="30%"><b>Datum + tijdstip bestelling: </b></td>
            <td>{{$order->getCreatedAt()->translatedFormat('d-m-Y H:i')}}</td>
        </tr>
        <tr>
            <td><b>Status: </b></td>
            <td>
                @if($order->status === \App\Models\Order::STATUS_GEANNULEERD)
                    <span class="badge bg-danger">Geannuleerd</span>
                @elseif($order->status === \App\Models\Order::STATUS_VERLOPEN)
                    <span class="badge bg-secondary">Verlopen</span>
                @else
                    <span class="badge bg-success">Actief</span>
                @endif
            </td>
        </tr>
        <tr>
            <td><b>Totaalprijs: </b></td>
            <td>€ {{$order->advert->getPortionPrice() * $order->getPortionAmount()}}</td>
        </tr>
        <tr>
            <td><b>Klantnaam: </b></td>
            <td>{{$order->client->getName()}}</td>
        </tr>
        <tr>
            <td><b>Klant e-mail: </b></td>
            <td style="word-break: break-all;">{{$order->client->getEmail()}}</td>
        </tr>
        <tr>
            <td><b>Klant telefoonnummer: </b></td>
            <td>{{$order->client->getPhoneNumber()}}</td>
        </tr>
        <tr>
            <td><b>Gerechtnaam: </b></td>
            <td>{{$order->dish->getTitle()}}</td>
        </tr>
        <tr>
            <td><b>Advertentienummer: </b></td>
            <td>{{$order->getParsedAdvertUuid()}}</td>
        </tr>
        <tr>
            <td><b>Datum + verwacht afhaalmoment: </b></td>
            <td>{{$order->getExpectedPickupTime()->translatedFormat('d-m-Y H:i')}}</td>
        </tr>
        <tr>
            <td><b>Aantal porties: </b></td>
            <td>{{$order->getPortionAmount()}} porties</td>
        </tr>
        <tr>
            <td><b>Opmerkingen: </b></td>
            <td>{{$order->getRemarks()}}</td>
        </tr>
    </table>

    <div class="row">
        <div class="col-12">
            @php
                $isOrderMomentPassed = $order->advert->getParsedOrderTo()->isPast();
                $isBusinessCook = auth()->user()->type_thuiskok === 'Zakelijke Thuiskok';
                $documentType = $isBusinessCook ? 'Factuur' : 'Aankoopbewijs';
            @endphp

            @if ($cancel && $status === true)
                <a href="{{route('dashboard.orders.cancel', $order->getUuid())}}" class="btn btn-outline">Annuleren</a>
            @else
                <span class="alert alert-warning alert-bestelling">Deze bestelling kan niet meer geannuleerd worden omdat het uiterste bestelmoment is verstreken of de bestelling al eerder is geannuleerd.</span>
            @endif

            {{-- Document Download Button --}}
            @if ($isOrderMomentPassed && $order->status !== \App\Models\Order::STATUS_GEANNULEERD)
                <a href="{{ route('dashboard.orders.document.download', $order->getUuid()) }}" class="btn btn-small btn-outline-fatt">Download {{ $documentType }}</a>
            @elseif ($order->status !== \App\Models\Order::STATUS_GEANNULEERD)
                <button class="btn btn-small btn-outline-fattt disabled" data-tooltip="Button is klikbaar na uiterste bestelmoment">Download {{ $documentType }}</button>
            @endif

            {{-- Document Send Button --}}
            @if ($isOrderMomentPassed && $order->status !== \App\Models\Order::STATUS_GEANNULEERD)
                <a href="{{ route('dashboard.orders.document.send', $order->getUuid()) }}" class="btn btn-small btn-outline-fatt">Stuur {{ $documentType }} naar klant</a>
            @elseif ($order->status !== \App\Models\Order::STATUS_GEANNULEERD)
                <button class="btn btn-small btn-outline-fattt disabled" data-tooltip="Button is klikbaar na uiterste bestelmoment">Stuur {{ $documentType }} naar klant</button>
            @endif
        </div>
    </div>  
</div>

<style>
    .tooltip {
        position: absolute;
        background-color: #333;
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 1000;
        pointer-events: none;
    }
    
    .disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn {
        margin-right: 5px;
    }
    
    @media (max-width: 767px) {
        .btn {
            margin-right: 0;
            margin-bottom: 10px;
        }
        
        .tooltip {
            position: fixed;
            width: 80%;
            left: 10% !important;
            text-align: center;
            z-index: 9999;
            padding: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const disabledButtons = document.querySelectorAll('.disabled');
        let activeTooltip = null;
        
        // Function to create tooltip
        function createTooltip(button, tooltipText) {
            // Remove any existing tooltip first
            if (activeTooltip) {
                document.body.removeChild(activeTooltip);
                activeTooltip = null;
            }
            
            // Create new tooltip
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.innerText = tooltipText;
            document.body.appendChild(tooltip);
            
            const buttonRect = button.getBoundingClientRect();
            
            // Desktop positioning
            if (window.innerWidth > 767) {
                tooltip.style.left = buttonRect.left + 'px';
                tooltip.style.top = (buttonRect.bottom + 5) + 'px';
            } 
            // Mobile positioning
            else {
                tooltip.style.left = '10%';
                tooltip.style.width = '80%';
                tooltip.style.top = (buttonRect.bottom + 5) + 'px';
            }
            
            activeTooltip = tooltip;
            tooltip.associatedButton = button;
            return tooltip;
        }
        
        // Function to remove tooltip
        function removeTooltip() {
            if (activeTooltip) {
                document.body.removeChild(activeTooltip);
                activeTooltip = null;
            }
        }
        
        // Detect if device is mobile
        const isMobile = window.innerWidth <= 767;
        
        // Add event listeners for each disabled button
        disabledButtons.forEach(button => {
            const tooltipText = button.getAttribute('data-tooltip');
            
            if (tooltipText) {
                if (isMobile) {
                    // For mobile - click functionality
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        // If there's already a tooltip for this button, remove it
                        if (activeTooltip && activeTooltip.associatedButton === this) {
                            removeTooltip();
                        } else {
                            // Otherwise create a new tooltip
                            const tooltip = createTooltip(this, tooltipText); // Changed to use tooltipText
                            console.log("Created tooltip on mobile:", tooltip);
                        }
                    });
                } else {
                    // For desktop - hover functionality
                    button.addEventListener('mouseover', function() {
                        createTooltip(this, tooltipText);
                    });
                    
                    button.addEventListener('mouseout', function() {
                        removeTooltip();
                    });
                    
                    button.addEventListener('mousemove', function() {
                        if (activeTooltip) {
                            const newButtonRect = this.getBoundingClientRect();
                            activeTooltip.style.left = newButtonRect.left + 'px';
                            activeTooltip.style.top = (newButtonRect.bottom + 5) + 'px';
                        }
                    });
                }
            }
        });
        
        // Close tooltip when scrolling
        window.addEventListener('scroll', function() {
            removeTooltip();
        });
        
        // Close tooltip when clicking elsewhere on the page
        document.addEventListener('click', function(e) {
            if (activeTooltip && e.target !== activeTooltip.associatedButton) {
                removeTooltip();
            }
        });
    });
</script>
@endsection