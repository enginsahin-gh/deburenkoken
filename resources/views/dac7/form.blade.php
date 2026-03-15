@extends('layout.main')

@section('content')
<section class="clearfix mt-3">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="center-box">
                    <div class="row">
                        <div class="col-12">
                            <h1>Aanleveren DAC7 informatie</h1>
                            <p class="alert alert-warning" style="background-color: #fff3cd; border-color: #ffeeba; color: #856404;">
                                Kloppen er gegevens niet of heb je vragen? Neem dan contact met ons op via het 
                                <a href="{{ route('contact') }}" style="color: #856404; text-decoration: underline;">contactformulier</a>.
                            </p>
                            
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <form action="{{ route('dac7.submit', ['uuid' => $user->uuid, 'token' => $token]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <!-- Rij 1: Voornaam | Achternaam -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="firstname">Voornaam</label>
                                            <input type="text" name="firstname" id="firstname" class="form-control @error('firstname') is-invalid @enderror" 
                                                value="{{ old('firstname', $user->userProfile->firstname ?? '') }}"
                                                {{ $user->userProfile && $user->userProfile->firstname ? 'readonly' : '' }}
                                                style="{{ $user->userProfile && $user->userProfile->firstname ? 'background-color: #f2f2f2; cursor: not-allowed;' : '' }}">
                                            @error('firstname')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lastname">Achternaam</label>
                                            <input type="text" name="lastname" id="lastname" class="form-control @error('lastname') is-invalid @enderror" 
                                                value="{{ old('lastname', $user->userProfile->lastname ?? '') }}"
                                                {{ $user->userProfile && $user->userProfile->lastname ? 'readonly' : '' }}
                                                style="{{ $user->userProfile && $user->userProfile->lastname ? 'background-color: #f2f2f2; cursor: not-allowed;' : '' }}">
                                            @error('lastname')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Rij 2: Thuiskoknaam | Geboortedatum -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="thuiskoknaam">Thuiskoknaam</label>
                                            <input type="text" id="thuiskoknaam" name="thuiskoknaam" class="form-control" 
                                                value="{{ $user->username }}"
                                                readonly
                                                style="background-color: #f2f2f2; cursor: not-allowed;">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="birthday">Geboortedatum</label>
                                            <input type="date" name="birthday" id="birthday" class="form-control @error('birthday') is-invalid @enderror" 
                                                value="{{ old('birthday', $user->userProfile && $user->userProfile->birthday ? $user->userProfile->birthday->format('Y-m-d') : '') }}"
                                                {{ $user->userProfile && $user->userProfile->birthday ? 'readonly' : '' }}
                                                style="{{ $user->userProfile && $user->userProfile->birthday ? 'background-color: #f2f2f2; cursor: not-allowed;' : '' }}">
                                            @error('birthday')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Rij 3: Rekeningnummer | Adres -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="account_number">Rekeningnummer</label>
                                            @php
                                                $maskedIban = '';
                                                if ($user->banking && $user->banking->iban) {
                                                    $iban = $user->banking->iban;
                                                    if (strlen($iban) > 4) {
                                                        $maskedIban = str_repeat('*', strlen($iban) - 4) . substr($iban, -4);
                                                    } else {
                                                        $maskedIban = $iban;
                                                    }
                                                }
                                            @endphp
                                            <input type="text" name="account_number" id="account_number" class="form-control @error('account_number') is-invalid @enderror" 
                                                value="{{ old('account_number', $maskedIban) }}"
                                                {{ $user->banking && $user->banking->iban ? 'readonly' : '' }}
                                                style="{{ $user->banking && $user->banking->iban ? 'background-color: #f2f2f2; cursor: not-allowed;' : '' }}">
                                            <!-- Hidden field with actual IBAN for form submission -->
                                            @if($user->banking && $user->banking->iban)
                                                <input type="hidden" name="actual_account_number" value="{{ $user->banking->iban }}">
                                            @endif
                                            @error('account_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="address">Adres</label>
                                            <input type="text" name="address" id="address" class="form-control" 
                                                value="{{ $user->cook ? $user->cook->getStreet() . ' ' . $user->cook->getHouseNumber() . ($user->cook->getAddition() ? ' ' . $user->cook->getAddition() : '') . ', ' . $user->cook->getPostalCode() . ' ' . $user->cook->getCity() . ', ' . $user->cook->getCountry() : '' }}"
                                                readonly
                                                style="background-color: #f2f2f2; cursor: not-allowed;">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Rij 4: BSN | Is woonadres -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bsn">BSN</label>
                                            <input type="text" name="bsn" id="bsn" class="form-control @error('bsn') is-invalid @enderror" 
                                                value="{{ old('bsn', $user->bsn ?? '') }}" pattern="^[0-9]{9}$">
                                            <small class="form-text text-muted">Moet exact 9 cijfers bevatten.</small>
                                            @error('bsn')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Is bovenstaande adres je woonadres?</label>
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="is_residential_address" id="is_residential_yes" value="1" 
                                                        {{ old('is_residential_address', $user->dac7Establishment && $user->dac7Establishment->is_residential_address ? '1' : '') == '1' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_residential_yes">Ja</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="is_residential_address" id="is_residential_no" value="0" 
                                                        {{ old('is_residential_address', $user->dac7Establishment && isset($user->dac7Establishment->is_residential_address) && $user->dac7Establishment->is_residential_address === false ? '0' : '') == '0' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_residential_no">Nee</label>
                                                </div>
                                            </div>
                                            @error('is_residential_address')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Rij 5: ID voorkant | ID achterkant (naast elkaar) -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_front">ID voorkant</label>
                                            <div class="custom-file">
                                                <input type="file" name="id_front" id="id_front" class="custom-file-input @error('id_front') is-invalid @enderror">
                                                <label class="custom-file-label" for="id_front" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Kies bestand</label>
                                            </div>
                                            <small class="form-text text-muted">Enkel een paspoort, ID-kaart of Rijbewijs is toegestaan in PDF, PNG, JPEG of JPG formaat.</small>
                                            @error('id_front')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_back">ID achterkant</label>
                                            <div class="custom-file">
                                                <input type="file" name="id_back" id="id_back" class="custom-file-input @error('id_back') is-invalid @enderror">
                                                <label class="custom-file-label" for="id_back" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Kies bestand</label>
                                            </div>
                                            <small class="form-text text-muted">Enkel een paspoort, ID-kaart of Rijbewijs is toegestaan in PDF, PNG, JPEG of JPG formaat.</small>
                                            @error('id_back')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Punt 9: Vaste inrichting -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h3>
                                            Vaste inrichting 
                                            <span title="Dit is een voorbeeldtekst." style="cursor: help; position: relative;">
                                                <i class="fas fa-info-circle" style="color: #6c757d;"></i>
                                            </span>
                                        </h3>
                                        <div class="form-group">
                                            <label>Bestaat er een vaste inrichting waardoor de verkoper relevante activiteiten uitvoert binnen de EU?</label>
                                            <div class="mt-2">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="has_establishment" id="has_establishment_yes" value="1" 
                                                        {{ old('has_establishment', $user->dac7Establishment && $user->dac7Establishment->has_establishment ? '1' : '') == '1' ? 'checked' : '' }}
                                                        onchange="toggleEstablishmentFields(true)">
                                                    <label class="form-check-label" for="has_establishment_yes">Ja</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="has_establishment" id="has_establishment_no" value="0" 
                                                        {{ old('has_establishment', $user->dac7Establishment && $user->dac7Establishment->has_establishment ? '1' : '') != '1' ? 'checked' : '' }}
                                                        onchange="toggleEstablishmentFields(false)">
                                                    <label class="form-check-label" for="has_establishment_no">Nee</label>
                                                </div>
                                            </div>
                                            @error('has_establishment')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div id="establishment_fields" style="display: {{ old('has_establishment', $user->dac7Establishment && $user->dac7Establishment->has_establishment ? '1' : '') == '1' ? 'block' : 'none' }}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="establishment_country">Land</label>
                                                <input type="text" name="establishment_country" id="establishment_country" class="form-control @error('establishment_country') is-invalid @enderror" 
                                                    value="{{ old('establishment_country', $user->dac7Establishment->country ?? '') }}">
                                                @error('establishment_country')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="establishment_postal_code">Postcode</label>
                                                <input type="text" name="establishment_postal_code" id="establishment_postal_code" class="form-control @error('establishment_postal_code') is-invalid @enderror" 
                                                    value="{{ old('establishment_postal_code', $user->dac7Establishment->postal_code ?? '') }}" maxlength="6">
                                                <small class="form-text text-muted">6 tekens: cijfers en letters (bijv. 1234AD)</small>
                                                @error('establishment_postal_code')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="establishment_street">Straat</label>
                                                <input type="text" name="establishment_street" id="establishment_street" class="form-control @error('establishment_street') is-invalid @enderror" 
                                                    value="{{ old('establishment_street', $user->dac7Establishment->street ?? '') }}">
                                                @error('establishment_street')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="establishment_house_number">Huisnummer</label>
                                                <input type="text" name="establishment_house_number" id="establishment_house_number" class="form-control @error('establishment_house_number') is-invalid @enderror" 
                                                    value="{{ old('establishment_house_number', $user->dac7Establishment->house_number ?? '') }}">
                                                <small class="form-text text-muted">Cijfers en letters (bijv. 14B)</small>
                                                @error('establishment_house_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($user->type_thuiskok === 'Zakelijke Thuiskok')
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <h3>Extra voor zakelijke thuiskok</h3>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="kvk_naam">KVK naam</label>
                                                <input type="text" name="kvk_naam" id="kvk_naam" class="form-control @error('kvk_naam') is-invalid @enderror" 
                                                    value="{{ old('kvk_naam', $user->kvk_naam ?? '') }}"
                                                    {{ $user->kvk_naam ? 'readonly' : '' }}
                                                    style="{{ $user->kvk_naam ? 'background-color: #f2f2f2; cursor: not-allowed;' : '' }}">
                                                @error('kvk_naam')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="btw_nummer">BTW identificatienummer</label>
                                                <input type="text" name="btw_nummer" id="btw_nummer" class="form-control @error('btw_nummer') is-invalid @enderror" 
                                                    value="{{ old('btw_nummer', $user->btw_nummer ?? '') }}"
                                                    {{ $user->btw_nummer ? 'readonly' : '' }}
                                                    style="{{ $user->btw_nummer ? 'background-color: #f2f2f2; cursor: not-allowed;' : '' }}">
                                                @error('btw_nummer')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="kvk_nummer">KVK nummer</label>
                                                <input type="text" name="kvk_nummer" id="kvk_nummer" class="form-control @error('kvk_nummer') is-invalid @enderror" 
                                                    value="{{ old('kvk_nummer', $user->kvk_nummer ?? '') }}" pattern="^[0-9]{8}$">
                                                <small class="form-text text-muted">Moet exact 8 cijfers bevatten.</small>
                                                @error('kvk_nummer')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="rsin">RSIN (BSN voor eenmanszaak)</label>
                                                <input type="text" name="rsin" id="rsin" class="form-control @error('rsin') is-invalid @enderror" 
                                                    value="{{ old('rsin', $user->rsin ?? '') }}" pattern="^[0-9]{9}$">
                                                <small class="form-text text-muted">Moet exact 9 cijfers bevatten.</small>
                                                @error('rsin')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="vestigingsnummer">Vestigingsnummer</label>
                                                <input type="text" name="vestigingsnummer" id="vestigingsnummer" class="form-control @error('vestigingsnummer') is-invalid @enderror" 
                                                    value="{{ old('vestigingsnummer', $user->vestigingsnummer ?? '') }}" pattern="^[0-9]{12}$">
                                                <small class="form-text text-muted">Moet exact 12 cijfers bevatten.</small>
                                                @error('vestigingsnummer')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <p>Door op 'versturen' te klikken bevestig ik dat alle gegevens naar waarheid zijn ingevuld.</p>
                                        <button type="submit" class="btn btn-light" style="background: linear-gradient(to right, #f3723b 0%, #e54750 100%); color: white; border-radius: 6px; border: none; padding: 8px 20px;">Versturen</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function toggleEstablishmentFields(show) {
    document.getElementById('establishment_fields').style.display = show ? 'block' : 'none';
}

// Show filename when file is selected
document.addEventListener('DOMContentLoaded', function() {
    // File inputs
    const fileInputs = document.querySelectorAll('.custom-file-input');
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const fileName = e.target.files[0].name;
            const label = e.target.nextElementSibling;
            label.textContent = fileName;
        });
    });
    
    // Numeric field validations
    const bsnInput = document.getElementById('bsn');
    const kvkNummerInput = document.getElementById('kvk_nummer');
    const rsinInput = document.getElementById('rsin');
    const vestigingsnummerInput = document.getElementById('vestigingsnummer');
    
    // Address field validations
    const establishmentCountryInput = document.getElementById('establishment_country');
    const establishmentStreetInput = document.getElementById('establishment_street');
    const establishmentHouseNumberInput = document.getElementById('establishment_house_number');
    const establishmentPostalCodeInput = document.getElementById('establishment_postal_code');
    
    // Validation function for text fields (only letters, spaces, hyphens, apostrophes)
    const validateTextInput = function(e) {
        const key = e.key;
        if (!/^[A-Za-zÀ-ÖØ-öø-ÿ\s\-\']$/.test(key) && key !== 'Backspace' && key !== 'Delete' && key !== 'ArrowLeft' && key !== 'ArrowRight' && key !== 'Tab') {
            e.preventDefault();
        }
    };
    
    // Validation function for house number (alphanumeric)
    const validateHouseNumberInput = function(e) {
        const key = e.key;
        if (!/^[A-Za-z0-9]$/.test(key) && key !== 'Backspace' && key !== 'Delete' && key !== 'ArrowLeft' && key !== 'ArrowRight' && key !== 'Tab') {
            e.preventDefault();
        }
    };
    
    // Add event listeners for address fields
    if (establishmentCountryInput) {
        establishmentCountryInput.addEventListener('keydown', validateTextInput);
    }
    
    if (establishmentStreetInput) {
        establishmentStreetInput.addEventListener('keydown', validateTextInput);
    }
    
    if (establishmentHouseNumberInput) {
        establishmentHouseNumberInput.addEventListener('keydown', validateHouseNumberInput);
    }
    
    // Postcode validation (6 characters: letters and numbers)
    if (establishmentPostalCodeInput) {
        establishmentPostalCodeInput.addEventListener('input', function(e) {
            // Remove any characters that are not letters or numbers
            this.value = this.value.replace(/[^A-Za-z0-9]/g, '');
            
            // Limit to 6 characters
            if (this.value.length > 6) {
                this.value = this.value.slice(0, 6);
            }
            
            // Convert to uppercase
            this.value = this.value.toUpperCase();
            
            // Validate pattern (6 characters: letters and numbers)
            if (this.value.length === 6 && /^[A-Z0-9]{6}$/.test(this.value)) {
                this.setCustomValidity('');
            } else if (this.value.length > 0) {
                this.setCustomValidity('Postcode moet exact 6 tekens bevatten (letters en cijfers).');
            }
        });
    }
    
    // BSN validation (9 digits only)
    if (bsnInput) {
        bsnInput.addEventListener('input', function(e) {
            // Remove non-digits
            this.value = this.value.replace(/\D/g, '');
            
            // Check length
            if (this.value.length > 9) {
                this.value = this.value.slice(0, 9);
            }
            
            // Validate pattern
            if (this.value.length === 9 && /^[0-9]{9}$/.test(this.value)) {
                this.setCustomValidity('');
            } else if (this.value.length > 0) {
                this.setCustomValidity('BSN moet exact 9 cijfers bevatten.');
            }
        });
    }
    
    // KVK nummer validation (8 digits only)
    if (kvkNummerInput) {
        kvkNummerInput.addEventListener('input', function(e) {
            // Remove non-digits
            this.value = this.value.replace(/\D/g, '');
            
            // Check length
            if (this.value.length > 8) {
                this.value = this.value.slice(0, 8);
            }
            
            // Validate pattern
            if (this.value.length === 8 && /^[0-9]{8}$/.test(this.value)) {
                this.setCustomValidity('');
            } else if (this.value.length > 0) {
                this.setCustomValidity('KVK nummer moet exact 8 cijfers bevatten.');
            }
        });
    }
    
    // RSIN validation (9 digits only)
    if (rsinInput) {
        rsinInput.addEventListener('input', function(e) {
            // Remove non-digits
            this.value = this.value.replace(/\D/g, '');
            
            // Check length
            if (this.value.length > 9) {
                this.value = this.value.slice(0, 9);
            }
            
            // Validate pattern
            if (this.value.length === 9 && /^[0-9]{9}$/.test(this.value)) {
                this.setCustomValidity('');
            } else if (this.value.length > 0) {
                this.setCustomValidity('RSIN moet exact 9 cijfers bevatten.');
            }
        });
    }
    
    // Vestigingsnummer validation (12 digits only)
    if (vestigingsnummerInput) {
        vestigingsnummerInput.addEventListener('input', function(e) {
            // Remove non-digits
            this.value = this.value.replace(/\D/g, '');
            
            // Check length
            if (this.value.length > 12) {
                this.value = this.value.slice(0, 12);
            }
            
            // Validate pattern
            if (this.value.length === 12 && /^[0-9]{12}$/.test(this.value)) {
                this.setCustomValidity('');
            } else if (this.value.length > 0) {
                this.setCustomValidity('Vestigingsnummer moet exact 12 cijfers bevatten.');
            }
        });
    }
    
    // Simple tooltip handling without Bootstrap
    document.querySelectorAll('[title]').forEach(el => {
        el.addEventListener('mouseover', function(e) {
            let tooltip = document.createElement('div');
            tooltip.textContent = this.getAttribute('title');
            tooltip.style.position = 'absolute';
            tooltip.style.backgroundColor = '#6c757d';
            tooltip.style.color = 'white';
            tooltip.style.padding = '5px 10px';
            tooltip.style.borderRadius = '4px';
            tooltip.style.fontSize = '12px';
            tooltip.style.zIndex = '1000';
            tooltip.style.maxWidth = '200px';
            
            document.body.appendChild(tooltip);
            
            // Position the tooltip
            let rect = this.getBoundingClientRect();
            tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
            tooltip.style.left = (rect.left + rect.width/2 - tooltip.offsetWidth/2) + 'px';
            
            this.addEventListener('mouseout', function() {
                document.body.removeChild(tooltip);
            }, { once: true });
        });
    });
});
</script>

<style>
.custom-file {
    position: relative;
    display: inline-block;
    width: 100%;
    height: calc(1.5em + .75rem + 2px);
    margin-bottom: 0;
}

.custom-file-input {
    position: relative;
    z-index: 2;
    width: 100%;
    height: calc(1.5em + .75rem + 2px);
    margin: 0;
    opacity: 0;
}

.custom-file-label {
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
    z-index: 1;
    height: calc(1.5em + .75rem + 2px);
    padding: .375rem .75rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    border: 1px solid #ced4da;
    border-radius: 6px;
    display: flex;
    align-items: center;
}

.custom-file-label::after {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    z-index: 3;
    display: block;
    height: calc(1.5em + .75rem);
    padding: .375rem .75rem;
    line-height: 1.5;
    color: white;
    content: "Bladeren";
    background: linear-gradient(to right, #f3723b 0%, #e54750 100%);
    border-left: 1px solid #ced4da;
    border-radius: 0 6px 6px 0;
}

.form-group {
    margin-bottom: 1rem;
}
</style>
@endsection