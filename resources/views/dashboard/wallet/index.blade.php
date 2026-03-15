@extends('layout.dashboard')

@section('dashboard')
<style>
    
.btn-download {
    display: inline-block;
    padding: 4px 10px;
    background: linear-gradient(to right, #f3723b 0%, #e54750 100%);
    color: #fff;
    border-radius: 4px;
    text-decoration: none;
    cursor: pointer;
    font-size: 14px;
}
.btn-download.disabled {
    background: #ccc;
    cursor: not-allowed;
    opacity: 0.7;
}
.btn-download:hover:not(.disabled) {
    opacity: 0.9;
}
.payout button[type="submit"], 
.payout a.transaction-btn {
    background: linear-gradient(to right, #f3723b 0%, #e54750 100%);
    color: #fff;
    border: 2px solid #f3723b;
    padding: 8px 15px;
    width: 200px;
    margin: 10px auto 10px;
    border-radius: 6px;
    cursor: pointer;
    text-align: center;
    display: inline-block;
    text-decoration: none;
}
.payout button[type="submit"].disabled, 
.payout button[type="button"].disabled {
    background: linear-gradient(to right, #f3723b 0%, #e54750 100%);
    border: 2px solid #f3723b;
    color: #fff;
    cursor: not-allowed;
    opacity: 0.5;
    width: 200px;
    padding: 8px 15px;
    margin: 10px auto 10px;
    border-radius: 6px;
}
.page-header {
    margin-bottom: 0;
}
.page-header .container {
    padding: 0 15px;
}
.container {
    padding-left: 15px;
    padding-right: 15px;
}
.row{
    margin-right: -10px !important; 
    margin-left: -0px !important;
}
.page-header h1 {
    margin: 0;
    padding: 0;
}

/* FontAwesome icon fix - verwijdert zwarte streepjes */
.fa-solid,
.fa-circle-info,
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

.col, .col-1, .col-10, .col-11, .col-12, .col-2, .col-3, .col-4, .col-5, .col-6, .col-7, .col-8, .col-9, .col-auto, .col-lg, .col-lg-1, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-auto, .col-md, .col-md-1, .col-md-10, .col-md-11, .col-md-12, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-auto, .col-sm, .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-auto, .col-xl, .col-xl-1, .col-xl-10, .col-xl-11, .col-xl-12, .col-xl-2, .col-xl-3, .col-xl-4, .col-xl-5, .col-xl-6, .col-xl-7, .col-xl-8, .col-xl-9, .col-xl-auto {
    position: relative;
    width: 100%;
    padding-right: 0px;
    padding-left: 0px;
}
.col[data-label="Rekeningnummer"] {
    display: flex;
    align-items: center;
    white-space: nowrap;
}

.col[data-label="Factuur"] {
    display: flex;
    justify-content: flex-end;
    align-items: center;
}

/* DESKTOP: Normale weergave voor datum uitbetaald en factuur */
.table-row .col[data-label="Datum uitbetaald"],
.table-row .col[data-label="Factuur"] {
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.table-row .col[data-label="Datum uitbetaald"] .mobile-value,
.table-row .col[data-label="Factuur"] .mobile-value {
    display: block; /* Toon waarde op desktop */
    margin-left: 0;
}

.table-row .col[data-label="Datum uitbetaald"] .mobile-label-with-icon,
.table-row .col[data-label="Factuur"] .mobile-label-with-icon {
    display: none; /* Verberg labels op desktop */
}

/* Mobile tooltip styling - standaard verborgen */
.mobile-label-with-icon,
.mobile-label {
    display: none;
}

@media (max-width: 768px) {
    .table-header .col:nth-child(1), 
    .table-header .col:nth-child(2), 
    .table-header .col:nth-child(3), 
    .table-header .col:nth-child(4), 
    .table-header .col:nth-child(5), 
    .table-header .col:nth-child(6), 
    .table-header .col:nth-child(7), 
    .table-header .col:nth-child(8), 
    .table-header {
        display: none !important;
    }
    
    /* MOBIEL: Speciale layout voor datum/factuur */
    .table-row .col[data-label="Datum uitbetaald"],
    .table-row .col[data-label="Factuur"] {
        justify-content: space-between;
        text-align: right;
    }
    
    .table-row .col[data-label="Datum uitbetaald"] .mobile-label-with-icon,
    .table-row .col[data-label="Factuur"] .mobile-label-with-icon {
        display: flex !important;
        align-items: center;
        gap: 5px;
    }
    
    .table-row .col[data-label="Datum uitbetaald"] .mobile-value,
    .table-row .col[data-label="Factuur"] .mobile-value {
        display: block;
        margin-left: auto;
    }
    
    /* Toon mobile tooltips alleen op mobiel */
    .mobile-tooltip {
        display: inline-flex;
        align-items: center;
        margin-left: 5px;
        min-width: 30px;
        min-height: 30px;
        justify-content: center;
    }
    
    .mobile-tooltip i {
        font-size: 16px;
        color: #666;
        cursor: pointer;
        padding: 5px;
    }
    
    .mobile-tooltip .tooltiptext {
        visibility: hidden;
        width: 280px;
        max-width: calc(100vw - 40px);
        background-color: #333;
        color: #fff;
        text-align: left;
        border-radius: 6px;
        padding: 10px;
        position: fixed;
        z-index: 10000;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0;
        transition: opacity 0.3s;
        box-sizing: border-box;
        font-size: 14px;
        line-height: 1.4;
    }
    
    .mobile-tooltip .tooltiptext strong {
        color: #f3723b;
        font-size: 16px;
        display: block;
        margin-bottom: 8px;
    }
    
    .mobile-tooltip:hover .tooltiptext,
    .mobile-tooltip:active .tooltiptext,
    .mobile-tooltip:focus .tooltiptext {
        visibility: visible;
        opacity: 1;
    }
    
    .mobile-tooltip:hover .tooltiptext::after,
    .mobile-tooltip:active .tooltiptext::after,
    .mobile-tooltip:focus .tooltiptext::after {
        visibility: visible;
        opacity: 1;
    }
    
    /* Geen pijltje op mobile tooltips */
    .mobile-tooltip .tooltiptext::after {
        display: none;
    }
}
.col {
    margin-right: 10px;
}
.rekeningnummer-status-spacing {
    margin-right: 15px;
}
@media (max-width: 768px) {
    .tooltip .tooltiptext {
    left: 100px !important;
    width: 200px !important;
}
    nav {
        display: none !important;
    }
    .row {
        flex-direction: column;
    }
    .col-6 {
        width: 100%;
        margin-bottom: 15px;
    }
    .table-header {
        display: none;
    }
    .table-row {
        display: flex;
        flex-direction: column;
        padding: 15px !important;
    }
    .table-row .col { 
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .table-row .col::before {
        content: attr(data-label);
        font-weight: bold; 
        margin-right: 10px;
    }
    
    /* Speciale styling voor rijen met custom labels en tooltips */
    .table-row .col[data-label="Datum uitbetaald"]::before,
    .table-row .col[data-label="Factuur"]::before {
        display: none; /* Verberg de ::before omdat we custom labels hebben */
    }
    
    .mobile-label-with-icon {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .mobile-label {
        display: inline;
        font-weight: bold;
    }
    
    .mobile-value {
        display: block;
        margin-left: auto;
    }
    
    /* Geen pijltje op mobile tooltips */
    .mobile-tooltip .tooltiptext::after {
        display: none;
    }
}
.table {
    width: 100%;
    table-layout: fixed;
}
.table-header, .table-row {
    display: flex;
    width: 100%;
}
.table-header .col, .table-row .col {
    flex: 1;
    min-width: 0;
    word-wrap: break-word;
}
.table-header .col:last-child, .table-row .col:last-child {
    text-align: right;
}
@media (max-width: 768px) {
    .table-row .col[data-label="Factuur"] {
        justify-content: space-between;
        text-align: right;
    }
}

.saldo-notification {
    background-color: #fff3cd;
    border: 1px solid #ffeeba;
    border-radius: 4px;
    color: #856404;
    padding: 10px;
    margin: 10px 0 30px 0;
    font-size: 14px;
}

.section-title-container {
    margin-top: 60px;
    margin-bottom: 20px;
    position: relative;
}

@media (max-width: 768px) {
    .section-title-container {
        margin-top: 30px;
        margin-bottom: 20px;
    }
    
    .saldo-notification {
        margin-bottom: 20px;
    }
    
    .col-12[style*="height: 75px"] {
        height: auto !important;
        margin-bottom: 20px;
    }
}

@media screen and (width: 1440px) and (height: 900px), 
       screen and (min-width: 1280px) and (max-width: 1440px) and (-webkit-min-device-pixel-ratio: 1) {
    
    .table-header .col:nth-child(6), 
    .table-row .col:nth-child(6) {
        white-space: nowrap;
        min-width: 120px;
        flex: 0 0 120px;
    }
    
    .table-header .col:nth-child(2), 
    .table-row .col:nth-child(2) {
        white-space: nowrap;
        min-width: 110px;
        flex: 0 0 110px;
    }
    
    .section-title-container {
        width: 100%;
        overflow: visible;
        padding-right: 20px;
        margin-top: 100px !important;
    }
    
    .section-title-container h3 {
        width: 100%;
        overflow: visible;
        white-space: nowrap;
    }
    
    .tooltip .tooltiptext {
        left: auto !important;
        right: 0 !important;
        transform: translateX(0) !important;
        text-align: left !important;
        max-width: 280px !important;
        width: max-content !important;
        min-width: 250px !important;
        box-sizing: border-box !important;
    }
    
    .tooltip .tooltiptext::after {
        left: auto !important;
        right: 20px !important;
        margin-left: 0 !important;
        margin-right: -5px !important;
    }
    
    .tooltip .tooltiptext {
        position: absolute !important;
        right: 0 !important;
        left: auto !important;
        bottom: 100% !important;
        margin-bottom: 10px !important;
    }
    
    .tooltip {
        position: relative !important;
    }
    
    .table-header .col, 
    .table-row .col {
        padding: 0 8px;
    }
    
    .container {
        max-width: 100%;
        overflow-x: visible;
    }
}

@media screen and (min-width: 1024px) and (max-width: 1440px) and (-webkit-min-device-pixel-ratio: 1.5) {
    .table-header .col:nth-child(6), 
    .table-row .col:nth-child(6) {
        white-space: nowrap;
        min-width: 120px;
        flex: 0 0 120px;
    }
    
    .table-header .col:nth-child(2), 
    .table-row .col:nth-child(2) {
        white-space: nowrap;
        min-width: 110px;
        flex: 0 0 110px;
    }
    
    .section-title-container {
        width: 100%;
        overflow: visible;
        padding-right: 20px;
        margin-top: 80px !important;
    }
    
    .section-title-container h3 {
        width: 100%;
        overflow: visible;
        white-space: nowrap;
    }
    
    .tooltip .tooltiptext {
        left: auto !important;
        right: 0 !important;
        transform: translateX(0) !important;
        text-align: left !important;
        max-width: 280px !important;
        width: max-content !important;
        min-width: 250px !important;
    }
    
    .tooltip .tooltiptext::after {
        left: auto !important;
        right: 20px !important;
        margin-left: 0 !important;
        margin-right: -5px !important;
    }
    
    .tooltip .tooltiptext {
        position: absolute;
        right: 0;
        left: auto;
        bottom: 100%;
        margin-bottom: 10px;
        box-sizing: border-box;
    }
    
    .table-header .col:last-child .tooltip,
    .table-row .col:last-child .tooltip {
        position: relative;
    }
    
    .table-header .col, 
    .table-row .col {
        padding: 0 8px;
    }
    
    .container {
        max-width: 100%;
        overflow-x: visible;
    }
}

@media screen and (min-device-width: 1024px) and (max-device-width: 1440px) and (-webkit-min-device-pixel-ratio: 2) {
    .table-header .col:nth-child(6), 
    .table-row .col:nth-child(6) {
        white-space: nowrap;
        min-width: 120px;
        flex: 0 0 120px;
    }
    
    .table-header .col:nth-child(2), 
    .table-row .col:nth-child(2) {
        white-space: nowrap;
        min-width: 110px;
        flex: 0 0 110px;
    }
    
    .section-title-container {
        width: 100%;
        overflow: visible;
        padding-right: 20px;
        margin-top: 80px !important;
    }
    
    .section-title-container h3 {
        width: 100%;
        overflow: visible;
        white-space: nowrap;
    }
    
    .tooltip .tooltiptext {
        left: auto !important;
        right: 0 !important;
        transform: translateX(0) !important;
        text-align: left !important;
    }
    
    .tooltip .tooltiptext::after {
        left: auto !important;
        right: 20px !important;
        margin-left: 0 !important;
        margin-right: -5px !important;
    }
}
</style>
<div class="page-header neg-header">
    <div class="container"><h1>Portemonnee</h1></div>
</div>
<div class="container mt-20">
    <div class="row">
        <div class="col-6">
        <div class="d-flex align-center">
            <h5 class="m-0">Saldo in behandeling: <span class="orange-bold">€ {{$reserved}}</span></h5>
            <div class="tooltip ml-2"><i class="fa-solid fa-circle-info"></i>
                <span class="tooltiptext">We reserveren dit bedrag tot het uiterste moment voor afhalen is verstreken. Op deze manier kunnen wij jou ontzorgen door het bedrag, indien nodig, terug te betalen.</span>
            </div>
        </div>
        </div>
        <div class="col-6">
            <div class="d-flex align-center">
            <h5 class="m-0">Beschikbaar saldo: <span class="orange-bold" id="available-balance">€ {{$available}}</span></h5>
                <div class="tooltip ml-2"><i class="fa-solid fa-circle-info"></i>
                    <span class="tooltiptext">Om onze diensten van de hoogste kwaliteit te kunnen blijven bieden, rekenen wij een bijdrage van 5%.</span>
                </div>
            </div>
        </div>
    </div>

    @if ($available < 0)
    <div class="saldo-notification">
        Je beschikbare saldo is negatief vanwege annuleringskosten. Betaal de openstaande transactiekosten om weer gebruik te kunnen maken van de volledige functionaliteiten.
    </div>
    @endif

<div class="col-12" style="{{ $available < 0 ? '' : 'height: 75px;' }}">
    @if (is_null($iban))
        <div class="d-flex align-center payout">
            <div>
            <a href="{{route('dashboard.wallet.iban')}}" class="btn btn-small btn-light"style="margin-left: 0px !important;  border-radius: 6px;">Iban toevoegen</a>
            </div>
        </div>
    @else
        <form action="{{route('dashboard.wallet.payout')}}" method="POST">
            @csrf
            <div class="d-flex align-center payout">
                <div>
                    @if ($available < 0)
                        <a href="{{route('dashboard.wallet.pay.transaction')}}" class="transaction-btn">Betaal transactiekosten</a>
                    @elseif ($available > 0 && (!isset($paidedOut) || !$paidedOut))
                        <button type="submit" class="btn btn-small btn-light" id="payoutButton" title="Bij het uitbetalen wordt de totale beschikbare saldo uitbetaald. Uitbetalen is enkel 1 keer per dag mogelijk.">Uitbetalen</button>
                    @else
                        <button type="button" class="btn btn-small btn-light disabled" id="payoutButton">Uitbetalen</button>
                    @endif
                </div>
            </div>
           @if ($available >= 0)
           <div class="saldo-notification">
                Bij het uitbetalen wordt de totale beschikbare saldo uitbetaald. Uitbetalen is enkel 1 keer per dag mogelijk. Op de 1e van elke maand wordt het beschikbare saldo automatisch uitbetaald.
            </div>
           @endif
        </form>
    @endif
</div>
<!-- @if(session('debug_info'))
    <div style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; margin: 10px 0; font-family: monospace; font-size: 12px;">
        <strong>DEBUG INFO:</strong><br>
        @foreach(session('debug_info') as $info)
            {{ $info }}<br>
        @endforeach
    </div>
@endif -->
    <div class="section-title-container">
        <div class="container" style="padding-left: 0px !important;">
            <div class="row">
                <div class="col-12">
                    <h3>Uitbetalingen</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container" style="padding-left: 0px !important;">
        <div class="col-12 table">
            <div class="row table-header">
                <div class="col">Uitbetalingsnr.</div>
                <div class="col">Aanvraagdatum</div>
                <div class="col">Totaalbedrag</div>
                <div class="col">5% Bijdrage</div>
                <div class="col">Uit te betalen</div>
                <div class="col rekeningnummer-status-spacing">Rekeningnummer</div>
                <div class="col">Status</div>
                <div class="col">
                    Datum<br>uitbetaald
                    <div class="tooltip ml-2">
                        <i class="fa-solid fa-circle-info"></i>
                        <span class="tooltiptext">Let op, nadat DeBurenKoken de aanvraag heeft uitbetaald, kan het nog enkele dagen duren voordat het bedrag op je bankrekening staat.</span>
                    </div>
                </div>
                <div class="col">
                    Factuur
                    <div class="tooltip ml-2">
                        <i class="fa-solid fa-circle-info"></i>
                        <span class="tooltiptext">De factuur kan gedownload worden nadat de uitbetalingsverzoek is verwerkt door DeBurenKoken.</span>
                    </div>
                </div>
            </div>
           @foreach($payments as $payment)
<div class="row border mb-3 p-2 w-100 table-row">
    <div class="col" data-label="Uitbetalingsnr.">{{ substr($payment['uuid'], -6) }}</div>
    <div class="col" data-label="Datum aanvraag">{{ $payment['created_at']->translatedFormat('d-m-Y') }}</div>
    <div class="col" data-label="Totaalbedrag">€ {{ number_format($payment['amount'], 2) }}</div>
    <div class="col" data-label="5% Bijdrage">€ {{ number_format($payment['fee_amount'], 2) }}</div>
    <div class="col" data-label="Uit te betalen">€ {{ number_format($payment['payout_amount'], 2) }}</div>
    <div class="col" data-label="Rekeningnummer">
        <span class="rekeningnummer">{{ str_repeat('*', strlen($payment['banking']->getIban()) - 3) . substr($payment['banking']->getIban(), -3) }}</span>
    </div>
    <div class="col" data-label="Status">
        {{ $payment['payment_date'] ? 'Uitbetaald' : 'In behandeling' }}
    </div>
    <div class="col" data-label="Datum uitbetaald">
        <div class="mobile-label-with-icon">
            <span class="mobile-label">Datum uitbetaald</span>
            <div class="tooltip ml-2">
                <i class="fa-solid fa-circle-info"></i>
                <span class="tooltiptext">Let op, nadat DeBurenKoken de aanvraag heeft uitbetaald, kan het nog enkele dagen duren voordat het bedrag op je bankrekening staat.</span>
            </div>
        </div>
        <span class="mobile-value">{{ $payment['payment_date'] ? \Carbon\Carbon::parse($payment['payment_date'])->format('d-m-Y') : '-' }}</span>
    </div>
    <div class="col" data-label="Factuur">
        <div class="mobile-label-with-icon">
            <span class="mobile-label">Factuur</span>
            <div class="tooltip ml-2">
                <i class="fa-solid fa-circle-info"></i>
                <span class="tooltiptext">De factuur kan gedownload worden nadat de uitbetalingsverzoek is verwerkt door DeBurenKoken.</span>
            </div>
        </div>
        <div class="mobile-value">
            @if($payment['payment_date'])
                <a href="{{ route('dashboard.wallet.download.invoice', $payment['uuid']) }}" class="btn-download">Download</a>
            @else
                <span class="btn-download disabled">Download</span>
            @endif
        </div>
    </div>
</div>
@endforeach
        </div>
    </div>
</div>
@endsection
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if a payout was just processed successfully
        @if(session('payout_success'))
            console.log('Payout success detected, updating UI');
            
            // Explicitly set available balance to 0.00
            const availableElement = document.getElementById('available-balance');
            if (availableElement) {
                availableElement.textContent = '€ 0.00';
            } else {
                // Fallback to finding by class and content
                document.querySelectorAll('.orange-bold').forEach(function(element) {
                    if (element.textContent.includes('€') && 
                        element.parentElement.textContent.toLowerCase().includes('beschikbaar saldo')) {
                        element.textContent = '€ 0.00';
                    }
                });
            }
            
            // Disable the payout button
            const payoutButton = document.getElementById('payoutButton');
            if (payoutButton) {
                payoutButton.classList.add('disabled');
                payoutButton.setAttribute('type', 'button');
                payoutButton.disabled = true;
            }
            
            // Force page reload after a brief delay to ensure balance is updated
            setTimeout(function() {
                window.location.reload();
            }, 2000);
        @endif
    });
</script>
