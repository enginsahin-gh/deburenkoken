@extends('layout.dashboard')

@section('dashboard')
    <div class="dashboard" style="width: 100%; padding: 0;">
        <form action="{{ route('dashboard.admin.dac7') }}" class="row align-center mb-30">
            @csrf
            <div class="col-3">Zoeken</div>
            <div class="col-3">
                <input type="text" name="query" value="{{ $query ?? '' }}">
            </div>
            <div class="col-3">
                <button type="submit" class="btn btn-light m-0">Zoeken op Profielnaam</button>
            </div>
        </form>
        
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        
        @if(session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif
        
        <div class="table-responsive">
            <table class="table table-striped" style="width: 100%;">
                <thead>
                    <tr>
                        <td><b>Thuiskoknaam</b></td>
                        <td>
                            <b>Aantal bestellingen verkocht</b>
                            <span class="tooltip-icon">
                                <i class="fas fa-info-circle"></i>
                                <span class="tooltip-text">Dit zijn bestellingen die eventueel nog geannuleerd kunnen worden.</span>
                            </span>
                        </td>
                        <td>
                            <b>Omzet</b>
                            <span class="tooltip-icon">
                                <i class="fas fa-info-circle"></i>
                                <span class="tooltip-text">Dit is omzet wat eventueel nog geannuleerd kan worden.</span>
                            </span>
                        </td>
                        <td>
                            <b>DAC7 grens overschreden</b>
                            <span class="tooltip-icon">
                                <i class="fas fa-info-circle"></i>
                                <span class="tooltip-text">Grens DAC7 is 30 bestellingen of 2000 euro.</span>
                            </span>
                        </td>
                        <td><b>Datum grens bereikt</b></td>
                        <td><b>DAC7 informatie aangeleverd?</b></td>
                        <td><b>DAC7 Link</b></td>
                        <td><b>Type Thuiskok</b></td>
                        <td><b>Voornaam</b></td>
                        <td><b>Achternaam</b></td>
                        <td><b>Geboortedatum</b></td>
                        <td><b>Adres</b></td>
                        <td><b>Is woonadres?</b></td>
                        <td><b>Rekeningnummer</b></td>
                        <td><b>BSN</b></td>
                        <td><b>Identificatie voorkant</b></td>
                        <td><b>Identificatie achterkant</b></td>
                        <td><b>Vaste inrichting in EU?</b></td>
                        <td><b>Establishment Land</b></td>
                        <td><b>Establishment Postcode</b></td>
                        <td><b>Establishment Straat</b></td>
                        <td><b>Establishment Huisnummer</b></td>
                        <td><b>KVK naam</b></td>
                        <td><b>BTW identificatienummer</b></td>
                        <td><b>KVK nummer</b></td>
                        <td><b>RSIN (BSN voor eenmanszaak)</b></td>
                        <td><b>Vestigingsnummer</b></td>
                        <td><b>NVWA Nummer</b></td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usersWithDac7 as $user)
                    <tr>
                        <td>{{ $user['username'] }}</td>
                        <td>{{ $user['order_count'] }}</td>
                        <td>€{{ number_format($user['total_revenue'], 2, ',', '.') }}</td>
                        <td>{{ $user['dac7_exceeded'] ? 'Ja' : 'Nee' }}</td>
                        <td>{{ $user['dac7_threshold_date'] ?? '-' }}</td>
                        <td>
                            <form action="{{ route('dashboard.admin.dac7.update', $user['uuid']) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="dac7Switch{{ $loop->index }}" 
                                        name="information_provided" 
                                        {{ $user['dac7_information_provided'] ? 'checked' : '' }}
                                        onchange="this.form.submit()">
                                    <label class="custom-control-label" for="dac7Switch{{ $loop->index }}">
                                        {{ $user['dac7_information_provided'] ? 'Ja' : 'Nee' }}
                                    </label>
                                </div>
                            </form>
                        </td>
                        <td>
                            @if($user['dac7_form_link'])
                                <div id="linkContainer{{ $loop->index }}" style="display: none; background: #f8f9fa; padding: 10px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 10px;">
                                    <small><strong>DAC7 Link:</strong></small><br>
                                    <input type="text" value="{{ $user['dac7_form_link'] }}" readonly style="width: 100%; font-size: 11px; padding: 5px;" onclick="this.select()">
                                    <small class="text-muted d-block mt-1">Klik op het veld om te selecteren en kopiëren</small>
                                </div>
                                
                                <button type="button" class="btn btn-light m-0" onclick="toggleLink({{ $loop->index }})" style="white-space: nowrap;">
                                    <i class="fas fa-eye"></i>&nbsp;<span id="linkButtonText{{ $loop->index }}">Toon Link</span>
                                </button>
                                
                                @if($user['dac7_information_provided'])
                                    <br><br>
                                    <form action="{{ route('dashboard.admin.dac7.reset', $user['uuid']) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Weet je zeker dat je de DAC7 informatie wilt resetten? De gebruiker kan dan opnieuw gegevens aanleveren.')">
                                            Reset DAC7
                                        </button>
                                    </form>
                                @endif
                            @else
                                <span class="text-muted">Geen link</span>
                            @endif
                        </td>
                        <td>{{ $user['type_thuiskok'] ?? 'Particulier' }}</td>
                        <td>{{ $user['firstname'] }}</td>
                        <td>{{ $user['insertion'] ? $user['insertion'].' ' : '' }}{{ $user['lastname'] }}</td>
                        <td>{{ $user['birthday'] }}</td>
                        <td>
                            @php
                                if($user['address']['street']) {
                                    $addressString = $user['address']['street'];
                                    
                                    if($user['address']['postal']) {
                                        $addressString .= ', ' . $user['address']['postal'];
                                        
                                        if($user['address']['place']) {
                                            $addressString .= ' ' . $user['address']['place'];
                                        }
                                    } elseif($user['address']['place']) {
                                        $addressString .= ', ' . $user['address']['place'];
                                    }
                                    
                                    if($user['address']['country']) {
                                        $addressString .= ', ' . $user['address']['country'];
                                    }
                                    
                                    echo $addressString;
                                }
                            @endphp
                        </td>
                        <td>{{ isset($user['is_residential_address']) ? ($user['is_residential_address'] ? 'Ja' : 'Nee') : 'Onbekend' }}</td>
                        <td>
                            <span class="sensitive-value" id="iban-dac7-{{ $loop->index }}">{{ $user['iban'] }}</span>
                            @if(!empty($user['iban']))
                                <button type="button" class="reveal-btn"
                                    data-user-uuid="{{ $user['uuid'] }}"
                                    data-field-type="iban"
                                    data-target="iban-dac7-{{ $loop->index }}"
                                    data-masked="{{ $user['iban'] }}"
                                    title="Toon volledig IBAN">
                                    <i class="fas fa-eye"></i>
                                </button>
                            @endif
                        </td>
                        <td>
                            <span class="sensitive-value" id="bsn-dac7-{{ $loop->index }}">{{ $user['bsn'] }}</span>
                            @if(!empty($user['bsn']))
                                <button type="button" class="reveal-btn"
                                    data-user-uuid="{{ $user['uuid'] }}"
                                    data-field-type="bsn"
                                    data-target="bsn-dac7-{{ $loop->index }}"
                                    data-masked="{{ $user['bsn'] }}"
                                    title="Toon volledig BSN">
                                    <i class="fas fa-eye"></i>
                                </button>
                            @endif
                        </td>
                        <td>
                            @if($user['id_front'])
                                <a href="{{ route('dashboard.admin.dac7.download.id', ['uuid' => $user['uuid'], 'type' => 'front']) }}" class="btn btn-light m-0">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            @else
                                <span class="text-muted">Geen document</span>
                            @endif
                        </td>
                        <td>
                            @if($user['id_back'])
                                <a href="{{ route('dashboard.admin.dac7.download.id', ['uuid' => $user['uuid'], 'type' => 'back']) }}" class="btn btn-light m-0">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            @else
                                <span class="text-muted">Geen document</span>
                            @endif
                        </td>
                        <td>{{ $user['has_establishment'] ? 'Ja' : 'Nee' }}</td>
                        <td>{{ $user['establishment']['country'] }}</td>
                        <td>{{ $user['establishment']['postal_code'] }}</td>
                        <td>{{ $user['establishment']['street'] }}</td>
                        <td>{{ $user['establishment']['house_number'] }}</td>
                        <td>{{ $user['kvk_naam'] }}</td>
                        <td>{{ $user['btw_nummer'] }}</td>
                        <td>{{ $user['kvk_nummer'] }}</td>
                        <td>{{ $user['rsin'] }}</td>
                        <td>{{ $user['vestigingsnummer'] }}</td>
                        <td>{{ $user['nvwa_nummer'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function toggleLink(index) {
        var container = document.getElementById('linkContainer' + index);
        var buttonText = document.getElementById('linkButtonText' + index);
        
        if (container.style.display === 'none') {
            container.style.display = 'block';
            buttonText.textContent = 'Verberg Link';
        } else {
            container.style.display = 'none';
            buttonText.textContent = 'Toon Link';
        }
    }
    </script>

    @include('partials.sensitive-data-reveal')

    <style>
        .table-responsive {
            overflow-x: auto;
            width: 100%;
        }
        
        .table {
            width: 100%;
            min-width: 100%;
        }
        
        .custom-control {
            position: relative;
            display: inline-block;
        }
        
        .custom-control-input {
            position: absolute;
            opacity: 0;
            z-index: -1;
        }
        
        .custom-control-label {
            position: relative;
            margin-bottom: 0;
            vertical-align: top;
            padding-left: 45px;
            cursor: pointer;
        }
        
        .custom-control-label::before {
            position: absolute;
            top: 0;
            left: 0;
            display: block;
            width: 36px;
            height: 20px;
            background-color: #ccc;
            border-radius: 10px;
            content: "";
        }
        
        .custom-control-input:checked ~ .custom-control-label::before {
            background-color: #28a745;
        }
        
        .custom-control-label::after {
            position: absolute;
            top: 2px;
            left: 2px;
            display: block;
            width: 16px;
            height: 16px;
            background-color: white;
            border-radius: 50%;
            content: "";
            transition: transform 0.15s ease-in-out;
        }
        
        .custom-control-input:checked ~ .custom-control-label::after {
            transform: translateX(16px);
        }
        
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
            padding: 10px;
            position: absolute;
            z-index: 9999 !important;
            top: 100%; 
            left: 50%;
            transform: translateX(-50%);
            margin-top: 10px;
            opacity: 0;
            transition: opacity 0.3s;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            pointer-events: none;
        }

        .tooltip-icon .tooltip-text::after {
            content: "";
            position: absolute;
            bottom: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: transparent transparent #333 transparent;
        }

        .tooltip-icon:hover .tooltip-text {
            visibility: visible !important;
            opacity: 1 !important;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }
    </style>
@endsection