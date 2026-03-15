@extends('layout.dashboard')
@section('dashboard')
@if($firsttime)
    <div class="page-header">
        <div class="container"><h1>Account</h1></div>
    </div>
        <div class="row">
    <div class="col-8 offset-2 page-title">
            <h2>Account informatie</h2>
        </div>
    </div>
<div class="ltn__about-us-area pt-20 pb-20">
    <div class="container">
        <div class="col-7 mx-auto">
            <form method="POST" action="{{ route('verification.information.submit', ['advert_uuid' => $advert]) }}" class="row form-box" autocomplete="off">
        @csrf
    <div class="col-6">
<div class="form-group">
    <label for="firstname">Voornaam</label>
    <input type="text" class="form-control @error('firstname') is-invalid @enderror" value="{{ old('firstname') }}" id="firstname" name="firstname" pattern="^[A-Za-zÀ-ÖØ-öø-ÿ\s\-]+$" maxlength="100" required>
        @error('firstname')
            <div class="text-danger">{{ $message }}</div>
            @enderror
                </div>
            </div>
                <div class="col-6">
                <div class="form-group">
                    <label for="insertion">Tussenvoegsel (optioneel)</label>
                    <input type="text" class="form-control" value="{{ old('insertion') }}" id="insertion" name="insertion" pattern="^[A-Za-zÀ-ÖØ-öø-ÿ\s\-]*$" maxlength="100">
                     @error('insertion')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    </div>
                </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="lastname">Achternaam</label>
                            <input type="text" class="form-control @error('lastname') is-invalid @enderror" id="lastname" value="{{ old('lastname') }}" name="lastname" pattern="^[A-Za-zÀ-ÖØ-öø-ÿ\s\-]+$" maxlength="100" required>
                            @error('lastname')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            <label for="phone">Telefoonnummer</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" value="{{ old('phone') }}" name="phone" maxlength="15" required>
                            @error('phone')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            <label for="birthday">Geboortedatum</label>
                            <input type="date" class="form-control @error('birthday') is-invalid @enderror" id="birthday" value="{{ old('birthday') }}" name="birthday" placeholder="Geboortedatum" required>
                            @error('birthday')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            <label for="kvk_naam">KVK Naam (optioneel)</label>
                            <input type="text" class="form-control @error('kvk_naam') is-invalid @enderror" id="kvk_naam" name="kvk_naam" value="{{ old('kvk_naam', $user->kvk_naam ?? '') }}" pattern="^[A-Za-zÀ-ÖØ-öø-ÿ\s\-\&\.]*$" maxlength="100">
                            @error('kvk_naam')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                <div class="col-6">
    <div class="form-group" style="position: relative;">
        <label for="btw_nummer">BTW Nummer (optioneel)</label>
        <input type="text" class="form-control @error('btw_nummer') is-invalid @enderror" id="btw_nummer" name="btw_nummer" value="{{ old('btw_nummer', $user->btw_nummer ?? '') }}">
        <small class="form-text text-muted" style="position: absolute; top: 100%; left: 0; margin-top: 2px; font-size: 11px;">Bijvoorbeeld: NL123456789B01</small>
        @error('btw_nummer')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>
                    <div class="col-6">
    <div class="form-group">
        <label for="nvwa_nummer">NVWA Nummer (optioneel)</label>
        <input type="text" class="form-control @error('nvwa_nummer') is-invalid @enderror" id="nvwa_nummer" name="nvwa_nummer" value="{{ old('nvwa_nummer', $user->nvwa_nummer ?? '') }}" pattern="^[A-Za-z0-9]*$" maxlength="20">
        @error('nvwa_nummer')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>
</div>

                    <div class="col-12">
                        <div class="form-group mt-3 d-flex justify-content-center">
                            <button type="submit" class="btn btn-small btn-light" style="width: 200px;">Opslaan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@elseif($secondtime)
    <div class="page-header">
        <div class="container"><h1>Locatie informatie</h1></div>
    </div>
    
    {{-- Show errors if any --}}
    @if ($errors->any())
        <div class="container">
            <div class="alert alert-danger">
                <h4>Er zijn problemen opgetreden:</h4>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    
    <div class="ltn__about-us-area pt-20 pb-20">
        <div class="container">
            <div class="row">
                <div class="col-8 offset-2">
                    <div class="form-box">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group address-search-group">
                                    <i class="fa-solid fa-location-dot location-pin"></i>
                                    <input placeholder="Vul hier je volledige locatie in (straat, nummer, plaats)" type="text" class="form-input-box search-box address-search-input" name="plaats" id="autocomplete" aria-describedby="selection">
                                    <i class="fa-solid fa-magnifying-glass magnifying-glass pointer" id='searchButton'></i>
                                </div>
                                <div id="msg" class="address-instruction-box">
                                    <i class="fa-solid fa-circle-info"></i>
                                    <span>Vul hierboven je volledige adres in en selecteer het uit de lijst</span>
                                    <span id="example" class="hide">{Straat} {nummer} {plaats} en selecteer uw adres</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <form method="POST" action="{{ session('editing_address_from_settings') ? route('dashboard.settings.update.location.submit') : route('verification.location.submit', ['advert_uuid' => $advert ?? null]) }}" class="row">
    @csrf
    <div class="col-6">
        <div class="form-group">
            <label for="postal">Postcode</label>
            <div class="readonly-field-wrapper">
                <input type="text" class="form-control readonly-address-field" id="postal" value="{{ !empty(old('postal')) ? old('postal') : ($cook ? $cook->getPostalCode() : '') }}" name="postal" placeholder="Postcode" required readonly>
                <div class="readonly-field-tooltip">Gebruik het zoekveld hierboven om je adres in te vullen</div>
            </div>
                                        @error('postal')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="number">Huisnummer</label>
                                        <div class="readonly-field-wrapper">
                                            <input type="text" class="form-control readonly-address-field" value="{{ !empty(old('housenumber')) ? old('housenumber') : ($cook ? $cook->getHouseNumber() : '') }}" id="number" name="housenumber" placeholder="Nummer" required readonly>
                                            <div class="readonly-field-tooltip">Gebruik het zoekveld hierboven om je adres in te vullen</div>
                                        </div>
                                        @error('housenumber')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="addition">Toevoeging</label>
                                        <div class="readonly-field-wrapper">
                                            <input type="text" class="form-control readonly-address-field" id="addition" value="{{ !empty(old('addition')) ? old('addition') : ($cook ? $cook->getAddition() : '') }}" name="addition" placeholder="Toevoeging" readonly>
                                            <div class="readonly-field-tooltip">Gebruik het zoekveld hierboven om je adres in te vullen</div>
                                        </div>
                                        @error('addition')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="street">Straatnaam</label>
                                        <div class="readonly-field-wrapper">
                                            <input type="text" class="form-control readonly-address-field" value="{{ !empty(old('street')) ? old('street') : ($cook ? $cook->getStreet() : '') }}" id="street" name="street" placeholder="Straatnaam" readonly>
                                            <div class="readonly-field-tooltip">Gebruik het zoekveld hierboven om je adres in te vullen</div>
                                        </div>
                                        @error('street')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="place">Plaats</label>
                                        <div class="readonly-field-wrapper">
                                            <input type="text" class="form-control readonly-address-field" id="city" value="{{ !empty(old('place')) ? old('place') : ($cook ? $cook->getCity() : '') }}" name="place" placeholder="Plaats" readonly>
                                            <div class="readonly-field-tooltip">Gebruik het zoekveld hierboven om je adres in te vullen</div>
                                        </div>
                                        @error('place')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="country">Land</label>
                                        <div class="readonly-field-wrapper">
                                            <input type="text" class="form-control readonly-address-field" id="country" value="{{ !empty(old('country')) ? old('country') : ($cook ? $cook->getCountry() : '') }}" name="country" placeholder="Country" readonly>
                                            <div class="readonly-field-tooltip">Gebruik het zoekveld hierboven om je adres in te vullen</div>
                                        </div>
                                        @error('country')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <input type="hidden" id="latitude" name="latitude" value="{{ $cook ? $cook->getLatitude() : '' }}" readonly>
                                <input type="hidden" id="longitude" name="longitude" value="{{ $cook ? $cook->getLongitude() : '' }}" readonly>
                                <div class="col-12 text-center">
                                    <div class="form-group mt-3 d-flex justify-content-center">
                                        @php
                                            $isEdit = !empty($cook) && $cook->getCity() != '';
                                        @endphp
                                        <div class="button-container" style="width: 50%; display: flex; justify-content: {{ $isEdit ? 'space-between' : 'center' }}; gap: 1rem;">
                                            <button type="submit" id="opslaan" class="btn btn-small btn-light" style="width: 50%;" disabled>Opslaan</button>
                                            <a href="{{ route('dashboard.settings.details.home') }}" class="btn btn-outlines" style="width: 50%; {{ !$isEdit ? 'visibility: hidden; position: absolute;' : '' }}">Annuleren</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <input type="hidden" id="client" value="true">
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
@endif
@endsection
@section('page.scripts')
@if($firsttime)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone');
    const kvkNaamInput = document.getElementById('kvk_naam');
    const btwNummerInput = document.getElementById('btw_nummer');
    const nvwaNummerInput = document.getElementById('nvwa_nummer');
    
    const protectMaxLength = function(element, maxLength) {
        if (!element) return;
        
        element.setAttribute('maxlength', maxLength);
        
        setInterval(function() {
            if (element.getAttribute('maxlength') != maxLength) {
                element.setAttribute('maxlength', maxLength);
            }
        }, 100);
        
        element.addEventListener('input', function() {
            if (this.value.length > maxLength) {
                this.value = this.value.substring(0, maxLength);
            }
            if (this.getAttribute('maxlength') != maxLength) {
                this.setAttribute('maxlength', maxLength);
            }
        });
        
        element.addEventListener('paste', function(e) {
            setTimeout(() => {
                if (this.value.length > maxLength) {
                    this.value = this.value.substring(0, maxLength);
                }
                if (this.getAttribute('maxlength') != maxLength) {
                    this.setAttribute('maxlength', maxLength);
                }
            }, 0);
        });
        
        element.addEventListener('keydown', function(e) {
            if (this.getAttribute('maxlength') != maxLength) {
                this.setAttribute('maxlength', maxLength);
            }
            if (this.value.length >= maxLength && 
                e.key !== 'Backspace' && 
                e.key !== 'Delete' && 
                e.key !== 'ArrowLeft' && 
                e.key !== 'ArrowRight' && 
                e.key !== 'Tab' && 
                !e.ctrlKey && !e.altKey && !e.metaKey) {
                e.preventDefault();
            }
        });
    };
    
    protectMaxLength(phoneInput, 15);
    protectMaxLength(kvkNaamInput, 100);
    protectMaxLength(nvwaNummerInput, 20);
    
    const firstnameInput = document.getElementById('firstname');
    const lastnameInput = document.getElementById('lastname');
    const insertionInput = document.getElementById('insertion');
    
    const validateNameInput = function(e) {
        if (e.key === 'Backspace' || e.key === 'Delete' || 
            e.key === 'ArrowLeft' || e.key === 'ArrowRight' || 
            e.key === 'Tab' || e.key === 'Enter' ||
            e.ctrlKey || e.altKey || e.metaKey) {
            return true;
        }
        
        if (e.key === ' ' && this.value.includes(' ')) {
            e.preventDefault();
            return false;
        }
        
        if (this.value.length >= 100 && e.key !== 'Backspace' && e.key !== 'Delete') {
            e.preventDefault();
            return false;
        }
        
        if (!/^[A-Za-zÀ-ÖØ-öø-ÿ\s\-]$/.test(e.key)) {
            e.preventDefault();
            return false;
        }
        return true;
    };
    
    const validateKvkNameInput = function(e) {
        if (e.key === 'Backspace' || e.key === 'Delete' || 
            e.key === 'ArrowLeft' || e.key === 'ArrowRight' || 
            e.key === 'Tab' || e.key === 'Enter' ||
            e.ctrlKey || e.altKey || e.metaKey) {
            return true;
        }
        
        if (e.key === ' ' && this.value.includes(' ')) {
            e.preventDefault();
            return false;
        }
        
        if (this.value.length >= 100 && e.key !== 'Backspace' && e.key !== 'Delete') {
            e.preventDefault();
            return false;
        }
        
        if (!/^[A-Za-zÀ-ÖØ-öø-ÿ\s\-\&\.]$/.test(e.key)) {
            e.preventDefault();
            return false;
        }
        return true;
    };
    
    const validatePhoneInput = function(e) {
        if (e.key === 'Backspace' || e.key === 'Delete' || 
            e.key === 'ArrowLeft' || e.key === 'ArrowRight' || 
            e.key === 'Tab' || e.key === 'Enter' ||
            e.ctrlKey || e.altKey || e.metaKey) {
            return true;
        }
        
        if (this.value.length >= 15 && e.key !== 'Backspace' && e.key !== 'Delete') {
            e.preventDefault();
            return false;
        }
        
        if (!/^[0-9+\s\-]$/.test(e.key)) {
            e.preventDefault();
            return false;
        }
        return true;
    };
    
    const validateBtwInput = function(e) {
        if (e.key === 'Backspace' || e.key === 'Delete' || 
            e.key === 'ArrowLeft' || e.key === 'ArrowRight' || 
            e.key === 'Tab' || e.key === 'Enter' ||
            e.ctrlKey || e.altKey || e.metaKey) {
            return true;
        }
        
        if (!/^[NLnl0-9AB]$/.test(e.key)) {
            e.preventDefault();
            return false;
        }
        return true;
    };
    
    const validateNvwaInput = function(e) {
        if (e.key === 'Backspace' || e.key === 'Delete' || 
            e.key === 'ArrowLeft' || e.key === 'ArrowRight' || 
            e.key === 'Tab' || e.key === 'Enter' ||
            e.ctrlKey || e.altKey || e.metaKey) {
            return true;
        }
        
        if (this.value.length >= 20 && e.key !== 'Backspace' && e.key !== 'Delete') {
            e.preventDefault();
            return false;
        }
        
        if (!/^[A-Za-z0-9]$/.test(e.key)) {
            e.preventDefault();
            return false;
        }
        return true;
    };
    
    const cleanupInvalidInput = function(input, pattern, maxLength) {
        const value = input.value;
        let cleanValue = value.replace(new RegExp('[^' + pattern + ']', 'g'), '');
        
        if (input.id === 'phone') {
            cleanValue = cleanValue.replace(/[^0-9+\s\-]/g, '');
        } else if (input.id === 'nvwa_nummer') {
            cleanValue = cleanValue.replace(/[^A-Za-z0-9]/g, '');
        } else {
            cleanValue = cleanValue.replace(/\s+/g, ' ');
            
            const firstSpaceIndex = cleanValue.indexOf(' ');
            if (firstSpaceIndex !== -1) {
                const firstPart = cleanValue.substring(0, firstSpaceIndex + 1);
                const secondPart = cleanValue.substring(firstSpaceIndex + 1).replace(/ /g, '');
                cleanValue = firstPart + secondPart;
            }
        }
        
        if (cleanValue.length > maxLength) {
            cleanValue = cleanValue.substring(0, maxLength);
        }
        
        if (value !== cleanValue) {
            input.value = cleanValue;
        }
    };
    
    if (firstnameInput) {
        firstnameInput.addEventListener('keydown', validateNameInput);
        firstnameInput.addEventListener('input', function() {
            cleanupInvalidInput(this, 'A-Za-zÀ-ÖØ-öø-ÿ\\s\\-', 100);
        });
        firstnameInput.addEventListener('paste', function(e) {
            setTimeout(() => {
                cleanupInvalidInput(this, 'A-Za-zÀ-ÖØ-öø-ÿ\\s\\-', 100);
            }, 0);
        });
    }
    
    if (lastnameInput) {
        lastnameInput.addEventListener('keydown', validateNameInput);
        lastnameInput.addEventListener('input', function() {
            cleanupInvalidInput(this, 'A-Za-zÀ-ÖØ-öø-ÿ\\s\\-', 100);
        });
        lastnameInput.addEventListener('paste', function(e) {
            setTimeout(() => {
                cleanupInvalidInput(this, 'A-Za-zÀ-ÖØ-öø-ÿ\\s\\-', 100);
            }, 0);
        });
    }
    
    if (insertionInput) {
        insertionInput.addEventListener('keydown', validateNameInput);
        insertionInput.addEventListener('input', function() {
            cleanupInvalidInput(this, 'A-Za-zÀ-ÖØ-öø-ÿ\\s\\-', 100);
        });
        insertionInput.addEventListener('paste', function(e) {
            setTimeout(() => {
                cleanupInvalidInput(this, 'A-Za-zÀ-ÖØ-öø-ÿ\\s\\-', 100);
            }, 0);
        });
    }
    
    if (kvkNaamInput) {
        kvkNaamInput.addEventListener('keydown', validateKvkNameInput);
        kvkNaamInput.addEventListener('input', function() {
            cleanupInvalidInput(this, 'A-Za-zÀ-ÖØ-öø-ÿ\\s\\-\\&\\.', 100);
        });
        kvkNaamInput.addEventListener('paste', function(e) {
            setTimeout(() => {
                cleanupInvalidInput(this, 'A-Za-zÀ-ÖØ-öø-ÿ\\s\\-\\&\\.', 100);
            }, 0);
        });
    }
    
    if (phoneInput) {
        phoneInput.addEventListener('keydown', validatePhoneInput);
        phoneInput.addEventListener('input', function() {
            cleanupInvalidInput(this, '0-9+\\s\\-', 15);
            
            const phoneNumber = this.value;
            
            function validateDutchPhoneNumber(phone) {
                const cleanedPhone = phone.replace(/[\s\-]/g, '');
                
                if (cleanedPhone.match(/^06\d{8}$/)) return true;
                if (cleanedPhone.match(/^\+316\d{8}$/)) return true;
                if (cleanedPhone.match(/^0[1-9]\d{8}$/)) return true;
                if (cleanedPhone.match(/^\+31[1-9]\d{8}$/)) return true;
                
                return false;
            }
            
            if (phoneNumber.length >= 10) {
                if (validateDutchPhoneNumber(phoneNumber)) {
                    phoneInput.setCustomValidity('');
                } else {
                    phoneInput.setCustomValidity('Voer een geldig Nederlands telefoonnummer in');
                }
            } else if (phoneNumber.length > 0) {
                phoneInput.setCustomValidity('');
            } else {
                phoneInput.setCustomValidity('');
            }
        });
        
        phoneInput.addEventListener('paste', function(e) {
            setTimeout(() => {
                cleanupInvalidInput(this, '0-9+\\s\\-', 15);
            }, 0);
        });
        
        phoneInput.addEventListener('blur', function() {
            const value = this.value;
            if (value) {
                function validateDutchPhoneNumber(phone) {
                    const cleanedPhone = phone.replace(/[\s\-]/g, '');
                    
                    if (cleanedPhone.match(/^06\d{8}$/)) return true;
                    if (cleanedPhone.match(/^\+316\d{8}$/)) return true;
                    if (cleanedPhone.match(/^0[1-9]\d{8}$/)) return true;
                    if (cleanedPhone.match(/^\+31[1-9]\d{8}$/)) return true;
                    
                    return false;
                }
                
                if (!validateDutchPhoneNumber(value)) {
                    this.setCustomValidity('Voer een geldig Nederlands telefoonnummer in (mobiel: 06xxxxxxxx of +316xxxxxxxx, vast: 0xxxxxxxxx of +31xxxxxxxxx)');
                } else {
                    this.setCustomValidity('');
                }
            } else {
                this.setCustomValidity('');
            }
        });
    }
    
    if (btwNummerInput) {
        btwNummerInput.addEventListener('keydown', validateBtwInput);
        btwNummerInput.addEventListener('input', function() {
            cleanupInvalidInput(this, 'NLnl0-9AB', 50);
            this.value = this.value.toUpperCase();
        });
        btwNummerInput.addEventListener('paste', function(e) {
            setTimeout(() => {
                cleanupInvalidInput(this, 'NLnl0-9AB', 50);
                this.value = this.value.toUpperCase();
            }, 0);
        });
        btwNummerInput.addEventListener('blur', function() {
            const value = this.value.toUpperCase();
            if (value) {
                const btwPattern = /^NL[0-9]+[AB][0-9]{2}$/;
                if (!btwPattern.test(value)) {
                    this.setCustomValidity('Voer een geldig Nederlands BTW nummer in (bijv. NL123456789B01 of NL123456789A01)');
                } else {
                    this.setCustomValidity('');
                }
            } else {
                this.setCustomValidity('');
            }
        });
    }
    
    if (nvwaNummerInput) {
        nvwaNummerInput.addEventListener('keydown', validateNvwaInput);
        nvwaNummerInput.addEventListener('input', function() {
            cleanupInvalidInput(this, 'A-Za-z0-9', 20);
        });
        nvwaNummerInput.addEventListener('paste', function(e) {
            setTimeout(() => {
                cleanupInvalidInput(this, 'A-Za-z0-9', 20);
            }, 0);
        });
    }
});
</script>
@endif
@if($secondtime)
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition = function(success, error, options) {
                    if (typeof error === "function") {
                        error({ code: 1, message: "Geolocation has been disabled." });
                    }
                };

                navigator.geolocation.watchPosition = function(success, error, options) {
                    if (typeof error === "function") {
                        error({ code: 1, message: "Geolocation has been disabled." });
                    }
                };
            }

            function initializeLocationHandling() {
                const autocompleteInput = document.getElementById('autocomplete');
                const opslaanButton = document.getElementById('opslaan');
                
                if (!autocompleteInput) {
                    return;
                }

                if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                    const autocomplete = new google.maps.places.Autocomplete(autocompleteInput, {
                        types: ['address'],
                        componentRestrictions: { country: 'nl' }
                    });

                    autocomplete.addListener('place_changed', function() {
                        const place = autocomplete.getPlace();

                        if (!place.geometry) {
                            alert('Geen locatie data gevonden. Probeer een ander adres.');
                            return;
                        }

                        const lat = place.geometry.location.lat();
                        const lng = place.geometry.location.lng();

                        const addressComponents = extractAddressComponents(place.address_components);

                        populateFormFields(addressComponents, lat, lng);
                    });
                }
            }

            function extractAddressComponents(components) {
                const result = {
                    street: '',
                    housenumber: '',
                    addition: '',
                    postal: '',
                    place: '',
                    country: 'NL'
                };

                components.forEach(component => {
                    const types = component.types;
                    const longName = component.long_name;
                    const shortName = component.short_name;

                    types.forEach(type => {
                        switch (type) {
                            case 'route':
                                result.street = longName;
                                break;
                            case 'street_number':
                                const numberMatch = longName.match(/^(\d+)(.*)$/);
                                if (numberMatch) {
                                    result.housenumber = numberMatch[1];
                                    result.addition = numberMatch[2].trim();
                                } else {
                                    result.housenumber = longName;
                                }
                                break;
                            case 'postal_code':
                                result.postal = longName;
                                break;
                            case 'locality':
                                result.place = longName;
                                break;
                            case 'administrative_area_level_2':
                                if (!result.place) {
                                    result.place = longName;
                                }
                                break;
                            case 'country':
                                result.country = 'NL';
                                break;
                        }
                    });
                });

                return result;
            }

            function populateFormFields(addressData, lat, lng) {
                const fields = {
                    'street': addressData.street,
                    'number': addressData.housenumber,
                    'addition': addressData.addition,
                    'postal': addressData.postal,
                    'city': addressData.place,
                    'country': 'NL',
                    'latitude': lat,
                    'longitude': lng
                };

                Object.entries(fields).forEach(([fieldId, value]) => {
                    const field = document.getElementById(fieldId);
                    if (field) {
                        field.value = value || '';
                        if (fieldId === 'country' && !field.value) {
                            field.value = 'NL';
                        }
                    }
                });

                setTimeout(() => {
                    const countryField = document.getElementById('country');
                    if (countryField && (!countryField.value || countryField.value === 'Netherlands')) {
                        countryField.value = 'NL';
                    }
                }, 100);

                const opslaanButton = document.getElementById('opslaan');
                if (opslaanButton) {
                    opslaanButton.disabled = false;
                    opslaanButton.style.opacity = '1';
                }
                
                validateFormData(addressData, lat, lng);
            }

            function validateFormData(addressData, lat, lng) {
                return true;
            }

            function enhanceFormSubmission() {
                const form = document.querySelector('form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        const countryField = document.getElementById('country');
                        if (countryField && (!countryField.value || countryField.value === 'Netherlands')) {
                            countryField.value = 'NL';
                        }
                        
                        const latField = document.getElementById('latitude');
                        const lngField = document.getElementById('longitude');
                        
                        if (!latField.value || !lngField.value) {
                            e.preventDefault();
                            alert('Selecteer eerst een adres uit de suggesties');
                            return false;
                        }
                        
                        return true;
                    });
                }
            }

            function initializeReadonlyFieldFeedback() {
                const readonlyFields = document.querySelectorAll('.readonly-address-field');
                const tooltipTimers = new WeakMap();
                const highlightTimers = new WeakMap();

                readonlyFields.forEach(function(field) {
                    field.addEventListener('click', function() {
                        const wrapper = this.closest('.readonly-field-wrapper');
                        if (!wrapper) {
                            return;
                        }

                        const tooltip = wrapper.querySelector('.readonly-field-tooltip');
                        if (!tooltip) {
                            return;
                        }

                        tooltip.classList.add('visible');

                        clearTimeout(tooltipTimers.get(tooltip));
                        tooltipTimers.set(tooltip, setTimeout(function() {
                            tooltip.classList.remove('visible');
                        }, 3000));

                        const autocompleteInput = document.getElementById('autocomplete');
                        if (autocompleteInput) {
                            autocompleteInput.classList.add('address-search-highlight');
                            clearTimeout(highlightTimers.get(autocompleteInput));
                            highlightTimers.set(autocompleteInput, setTimeout(function() {
                                autocompleteInput.classList.remove('address-search-highlight');
                            }, 1500));
                        }
                    });
                });
            }

            setTimeout(() => {
                initializeLocationHandling();
                enhanceFormSubmission();
                initializeReadonlyFieldFeedback();
            }, 500);
        });

    </script>
    @include('layout.scripts.google')
@endif
@endsection
<style>
.btn-outlines {
    background: #fff !important;
    color: #f3723b !important;
    border: 2px solid #f3723b !important;
    padding: 8px 15px !important;
    border-radius: 6px !important;
}

.tooltip {
    position: relative;
    display: inline-block;
    cursor: help;
    border-bottom: none !important;
}

.tooltip .tooltiptext {
    visibility: hidden;
    width: 220px;
    background-color: #333;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 8px 12px;
    position: absolute;
    z-index: 1000;
    bottom: 125%;
    left: 50%;
    margin-left: -110px;
    opacity: 0;
    transition: opacity 0.3s;
    font-size: 12px;
    line-height: 1.4;
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
}

.tooltip:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
}

/* Address search box styling */
.address-search-group {
    margin-bottom: 4px;
}

input[type=text].address-search-input {
    border: 2px solid #f3723b !important;
    border-radius: 8px !important;
    box-shadow: 0 2px 8px rgba(243, 114, 59, 0.2) !important;
    font-size: 15px !important;
    transition: box-shadow 0.2s, border-color 0.2s;
}

input[type=text].address-search-input:focus {
    border-color: #e54750 !important;
    box-shadow: 0 0 0 3px rgba(243, 114, 59, 0.25) !important;
    outline: none;
}

input[type=text].address-search-highlight {
    animation: search-pulse 0.4s ease-in-out 3;
}

@keyframes search-pulse {
    0% { box-shadow: 0 2px 8px rgba(243, 114, 59, 0.2) !important; }
    50% { box-shadow: 0 0 0 5px rgba(243, 114, 59, 0.4) !important; }
    100% { box-shadow: 0 2px 8px rgba(243, 114, 59, 0.2) !important; }
}

/* Address instruction box */
.address-instruction-box {
    display: flex;
    align-items: center;
    gap: 8px;
    background-color: #fff8f5;
    border: 1px solid #f3723b;
    border-radius: 6px;
    padding: 10px 14px;
    margin-bottom: 16px;
    color: #a84200;
    font-weight: 500;
    font-size: 14px;
}

.address-instruction-box i {
    color: #f3723b;
    font-size: 16px;
    flex-shrink: 0;
}

/* Readonly address fields */
.readonly-field-wrapper {
    position: relative;
}

.readonly-address-field {
    background-color: #f5f5f5 !important;
    cursor: not-allowed !important;
    color: #888 !important;
    border-color: #ddd !important;
}

.readonly-field-tooltip {
    display: none;
    position: absolute;
    bottom: calc(100% + 6px);
    left: 50%;
    transform: translateX(-50%);
    background-color: #333;
    color: #fff;
    font-size: 12px;
    padding: 6px 10px;
    border-radius: 5px;
    white-space: nowrap;
    z-index: 100;
    pointer-events: none;
}

.readonly-field-tooltip::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    border: 5px solid transparent;
    border-top-color: #333;
}

.readonly-field-tooltip.visible {
    display: block;
}

@media (max-width: 767px) {
    .button-container {
        flex-direction: column !important;
        width: 80% !important;
        gap: 0 !important;
    }

    .button-container a {
        margin-top: 1px !important;
    }

    .button-container button,
    .button-container a {
        width: 100% !important;
    }

    .tooltip .tooltiptext {
        width: 180px;
        margin-left: -90px;
    }

    .readonly-field-tooltip {
        white-space: normal;
        width: 160px;
        text-align: center;
        left: 0;
        transform: none;
    }

    .readonly-field-tooltip::after {
        left: 30%;
        transform: none;
    }
}

</style>