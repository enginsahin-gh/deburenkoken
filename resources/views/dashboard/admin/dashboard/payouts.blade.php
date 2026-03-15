@extends('layout.dashboard')

@section('dashboard')
    <div class="page-header neg-header">
        <div class="container"><h1>Uitbetalingsverzoeken</h1></div>
    </div>
<style>
.text-success{color:#28a745;font-size:14px;}.row{width:100%!important;}@media(min-width:769px){.table-container{width:130%;margin-left:-200px;max-width:none;overflow-x:auto;}.table-row .col[data-label="Aanvraagdatum"]{margin-left:30px;}.table-row:nth-child(even) .btn-outline{margin:0 60px!important;}}@media(max-width:768px){.table-container{width:100%;margin-left:0;overflow-x:auto;}.table{width:100%;display:block;}.table-header{display:none;}.table-row{margin-bottom:15px;display:block;border:1px solid #ddd;border-radius:5px;padding:10px!important;}.table-row .col{display:flex;padding:8px 5px;text-align:right;justify-content:space-between;border-bottom:1px solid #eee;}.table-row .col:before{content:attr(data-label);font-weight:bold;text-align:left;padding-right:10px;}.table-row .col[data-label="Aanvraagdatum"]{margin-left:0;}.table-row .col-1{display:flex;justify-content:center;padding:10px 5px;}button[type="submit"]{margin:15px auto;display:block;width:80%;}.button-container{padding:0 15px;}}

/* Tooltip stijlen */
.tooltip-icon {
    cursor: pointer;
    position: relative;
    display: inline-block;
}

.tooltip-icon .tooltip-text {
    visibility: hidden;
    width: 200px;
    background-color: #333;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 5px;
    position: absolute;
    z-index: 1000;
    bottom: 125%;
    left: 50%;
    margin-left: -100px;
    opacity: 0;
    transition: opacity 0.3s;
}

.tooltip-icon .tooltip-text::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #333 transparent transparent transparent;
}

.tooltip-icon:hover .tooltip-text {
    visibility: visible;
    opacity: 1;
}

/* Zorg ervoor dat de container geen overflow heeft die de tooltip afsnijdt */
.container-fluid, .table-responsive, .table-container {
    overflow: visible !important;
}
</style>

<div class="container mt-30">
        @if(session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif
        <div class="row">
            <div class="col f">
                <button id='filter' class="btn-outline"><i class="fa-solid fa-filter"></i></button> 
            </div>
        </div>
   
         <form id="approveForm" action="{{ route('dashboard.admin.payouts.approve') }}" method="POST">
            @csrf
            <div class="table-container mt-20">
                <div class="col-12 table">
                    <div class="row table-header">
                        <div class="col">Uitbetalingsnmr.</div>
                        <div class="col">Aanvr.dat.</div>
                        <div class="col">Thuiskok naam</div>
                        <div class="col">Volledige naam</div>
                        <div class="col">
                            DAC7 grens overschreden?
                            <span class="tooltip-icon">
                                <i class="fas fa-info-circle"></i>
                                <span class="tooltip-text">Grens DAC7 is 30 bestellingen of 2000 euro.</span>
                            </span>
                        </div>
                        <div class="col">DAC7 gegevens aangeleverd?</div>
                        <div class="col">Totaalbedrag</div>
                        <div class="col">5% Bijdrage</div>
                        <div class="col">Uit te betalen</div>
                        <div class="col">Rekeningnummer</div>
                        <div class="col">Status</div>
                        <div class="col">Datum uitbetaald</div>
                        <div class="col">Aanvraagtype</div>
                        <div class="col-1 p-1">Goedkeuren</div>
                    </div>

                    @foreach($users as $user)
                    <div class="row table-row border mb-3 p-2">
                        <div class="col" data-label="Uitbetalingsnummer">
                            {{ substr($user['uuid'], -6) }}
                        </div>
                        <div class="col" data-label="Aanvraagdatum">
                            {{$user['created_at']}}
                        </div>
                        <div class="col" data-label="Thuiskok naam">
                            {{$user['username']}}
                        </div>
                        <div class="col" data-label="Volledige naam">
                            {{$user['firstname'] . ' ' . $user['lastname']}}
                        </div>
                        <div class="col" data-label="DAC7 grens overschreden?">
                            {{ isset($user['dac7_exceeded']) && $user['dac7_exceeded'] ? 'Ja' : 'Nee' }}
                        </div>
                        <div class="col" data-label="DAC7 gegevens aangeleverd?">
                            {{ isset($user['dac7_information_provided']) && $user['dac7_information_provided'] ? 'Ja' : 'Nee' }}
                        </div>
                        <div class="col" data-label="Totaalbedrag">
                            € {{number_format($user['amount'], 2)}}
                        </div>
                        <div class="col" data-label="5% Bijdrage">
                            € {{number_format($user['amount'] * 0.05, 2)}}
                        </div>
                        <div class="col" data-label="Uit te betalen">
                            € {{number_format($user['amount'] * 0.95, 2)}}
                        </div>
                        <div class="col" data-label="Rekeningnummer">
                            <span class="sensitive-value" id="iban-payout-{{ $loop->index }}">{{ $user['iban'] }}</span>
                            @if($user['iban'] !== '' && $user['iban'] !== 'N/A')
                                <button type="button" class="reveal-btn"
                                    data-user-uuid="{{ $user['user_uuid'] }}"
                                    data-field-type="iban"
                                    data-target="iban-payout-{{ $loop->index }}"
                                    data-masked="{{ $user['iban'] }}"
                                    title="Toon volledig IBAN">
                                    <i class="fas fa-eye"></i>
                                </button>
                            @endif
                        </div>
                        <div class="col" data-label="Status">
                            {{ isset($user['payment_date']) ? 'Uitbetaald' : 'In behandeling' }}
                        </div>
                        <div class="col" data-label="Datum uitbetaald">
                            {{ isset($user['payment_date']) ? Carbon\Carbon::parse($user['payment_date'])->format('d-m-Y') : '-' }}
                        </div>
                        <div class="col" data-label="Aanvraagtype">
                            {{ $user['payment_type'] === 'automatic' ? 'Automatisch' : 'Handmatig' }}
                        </div>
                        <div class="col-1 text-center">
                        @if(!isset($user['payment_date']))
                            <input type="checkbox" name="selected_users[]" value="{{$user['uuid']}}">
                        @else
                            <span class="text-success">Goedgekeurd</span>
                        @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Button buiten de scrollbare container -->
            <div class="button-container">
                <button type="submit" class="btn btn-primary">Goedkeuren</button>
            </div>
        </form>
    </div>

    @include('partials.sensitive-data-reveal')

@endsection

@section('page.scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterButton = document.getElementById('filter');
        const filterOptions = document.getElementById('filter-options');

        filterButton.addEventListener('click', function(event) {
            event.stopPropagation();
            if (filterOptions && (filterOptions.style.display === 'none' || filterOptions.style.display === '')) {
                filterOptions.style.display = 'inline-flex';
                filterButton.innerHTML = '<i class="fa-solid fa-times"></i>';
            } else if (filterOptions) {
                filterOptions.style.display = 'none';
                filterButton.innerHTML = '<i class="fa-solid fa-filter" aria-hidden="true"></i>';
            }
        });

        document.addEventListener('click', function(event) {
            if (!filterOptions) return;
            
            const isClickInsideFilterOptions = filterOptions.contains(event.target);
            const isClickInsideFilterButton = filterButton.contains(event.target);
            if (!isClickInsideFilterOptions && !isClickInsideFilterButton) {
                filterOptions.style.display = 'none';
                filterButton.innerHTML = '<i class="fa-solid fa-filter" aria-hidden="true"></i>';
            }
        });

        const approveForm = document.getElementById('approveForm');
        const approveButton = approveForm.querySelector('button[type="submit"]');
        const checkboxes = approveForm.querySelectorAll('input[type="checkbox"]');

        approveButton.addEventListener('click', function(event) {
            const checkedCheckboxes = Array.from(checkboxes).filter(checkbox => checkbox.checked);
            if (checkedCheckboxes.length === 0) {
                event.preventDefault();
                alert('Selecteer ten minste één gebruikersorder om goed te keuren.');
            }
        });
    });
</script>
@endsection