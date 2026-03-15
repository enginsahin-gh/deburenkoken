@extends('layout.dashboard')

@section('dashboard')
    <div class="container">
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

            .btn-outlines,
            .btn-light.btn-small {
                padding: 8px 15px !important;
                width: 220px !important;
                border-radius: 6px !important;
                display: inline-block !important;
                margin: 0 5px !important;
                text-align: center !important;
                height: auto !important;
                line-height: normal !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                box-sizing: border-box !important;
            }

            .btn-outlines {
                background: #fff !important;
                color: #f3723b !important;
                border: 2px solid #f3723b !important;
            }

            .btn-light.btn-small {
                background: linear-gradient(to right, #f3723b 0%, #e54750 100%) !important;
                color: #fff !important;
                border: 2px solid #f3723b !important;
            }

            a.btn-light.btn-small,
            a.btn-outlines {
                display: inline-flex !important;
                justify-content: center !important;
                align-items: center !important;
            }

            .text-danger {
                color: #dc3545 !important;
            }

            .form-group {
                position: relative;
                margin-bottom: 20px;
            }

            .email-warning {
                position: absolute;
                display: none;
                padding: 12px;
                background-color: #fff3cd;
                border: 1px solid #ffeeba;
                border-radius: 4px;
                color: #856404;
                z-index: 10;
                width: 100%;
                top: 100%;
                left: 0;
                margin-top: 8px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }

            .email-form-group {
                margin-bottom: 140px;
                transition: margin-bottom 0.3s ease;
            }

            .email-form-group.warning-hidden {
                margin-bottom: 20px;
            }

            @media (max-width: 767px) {
                .email-warning {
                    position: static;
                    margin-top: 10px;
                    margin-bottom: 10px;
                }

                .email-form-group {
                    margin-bottom: 20px;
                }

                .btn-light.btn-small,
                .btn-outlines {
                    width: 100% !important;
                    margin: 5px auto !important;
                    display: block !important;
                    max-width: 300px !important;
                }

                #buttonsContainer .form-group.text-center {
                    display: flex !important;
                    flex-direction: column !important;
                    align-items: center !important;
                }

                .text-danger {
                    text-align: center;
                    margin-top: 10px;
                    width: 100%;
                }

                input[type="date"] {
                    width: 100% !important;
                    max-width: 100% !important;
                    box-sizing: border-box !important;
                    -webkit-appearance: none !important;
                    -moz-appearance: none !important;
                    appearance: none !important;
                }

                input[type="date"]::-webkit-datetime-edit {
                    padding-left: 0 !important;
                    text-align: left !important;
                }
            }

            @media (min-width: 768px) {

                .btn-light.btn-small,
                .btn-outlines {
                    width: 220px !important;
                    display: inline-block !important;
                    margin: 0 5px !important;
                }
            }
        </style>
        <div class="row">
            <div class="col-8 offset-2">
                <form action="{{ route('dashboard.settings.details.update') }}" method="post" class="row form-box">
                    @csrf
                    <div class="col-6">
                        <div class="form-group">
                            <label for="firstname">Voornaam</label>
                            <input type="text" class="form-control" name="firstname" id="firstname"
                                value="{{ $profile?->getFirstname() }}" disabled>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="insertion">Tussenvoegsel</label>
                            <input type="text" class="form-control" name="insertion" id="insertion"
                                value="{{ $profile?->getInsertion() }}" disabled>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            <label for="lastname">Achternaam</label>
                            <input type="text" class="form-control" name="lastname" id="lastname"
                                value="{{ $profile?->getLastname() }}" disabled>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="birthday">Geboortedatum</label>
                            <input type="date" class="form-control" id="birthday"
                                value="{{ $profile?->getBirthday()->translatedFormat('Y-m-d') }}" disabled>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            <label for="place">Woonplaats</label>
                            <input type="text" class="form-control" id="place" value="{{ $cook?->getCity() }}" disabled>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="postal">Postcode</label>
                            <input type="text" class="form-control" id="postal" value="{{ $cook?->getPostalCode() }}"
                                disabled>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            <label for="street">Straat</label>
                            <input type="text" class="form-control" id="street" value="{{ $cook?->getStreet() }}" disabled>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="number">Huisnummer</label>
                            <input type="text" class="form-control" id="number" value="{{ $cook?->getHouseNumber() }}"
                                disabled>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            <label for="addition">Toevoeging</label>
                            <input type="text" class="form-control" id="addition" name="addition"
                                value="{{ $cook?->getAddition() }}" disabled>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            <label for="kvk_naam">KVK Naam (optioneel)
                                <div class="tooltip ml-2"><i class="fa fa-info-circle"></i>
                                    <span class="tooltiptext">Neem contact op met DeBurenKoken via het <a
                                            href="{{ route('contact') }}"
                                            style="color: #fff; text-decoration: underline;">contactformulier</a> voor het
                                        wijzigen van je KVK naam, BTW nummer of NVWA nummer.</span>
                                </div>
                            </label>
                            <input type="text" class="form-control" id="kvk_naam" name="kvk_naam"
                                value="{{ $user->kvk_naam ?? '' }}" disabled>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            <label for="btw_nummer">BTW Nummer (optioneel)
                                <div class="tooltip ml-2"><i class="fa fa-info-circle"></i>
                                    <span class="tooltiptext">Neem contact op met DeBurenKoken via het <a
                                            href="{{ route('contact') }}"
                                            style="color: #fff; text-decoration: underline;">contactformulier</a> voor het
                                        wijzigen van je KVK naam, BTW nummer of NVWA nummer.</span>
                                </div>
                            </label>
                            <input type="text" class="form-control" id="btw_nummer" name="btw_nummer"
                                value="{{ $user->btw_nummer ?? '' }}" disabled>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            <label for="nvwa_nummer">NVWA Nummer (optioneel)
                                <div class="tooltip ml-2"><i class="fa fa-info-circle"></i>
                                    <span class="tooltiptext">Neem contact op met DeBurenKoken via het <a
                                            href="{{ route('contact') }}"
                                            style="color: #fff; text-decoration: underline;">contactformulier</a> voor het
                                        wijzigen van je KVK naam, BTW nummer of NVWA nummer.</span>
                                </div>
                            </label>
                            <input type="text" class="form-control" id="nvwa_nummer" name="nvwa_nummer"
                                value="{{ $user->nvwa_nummer ?? '' }}" disabled>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            <label for="phone">Telefoonnummer</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                name="phone" value="{{ $profile?->getPhoneNumber() }}" maxlength="15">
                            @error('phone')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            <div id="phoneValidationMessage" class="text-danger" style="display: none;"></div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group email-form-group warning-hidden" id="emailFormGroup">
                            <label for="email">E-mailadres</label>
                            <input type="text" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ $user->getEmail() }}">
                            <div id="emailWarning" class="email-warning">
                                <strong>Let op!</strong> Zorg ervoor dat je e-mailadres correct is. Als je een onjuist
                                e-mailadres invoert, kun je de toegang tot je account verliezen.
                            </div>
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            @if(session('userAlreadyExist'))
                                <div class="text-danger">{{ session('userAlreadyExist') }}</div>
                            @endif
                        </div>
                    </div>

                    @if(!$showAdress)
                        <div class="col-12">
                            <div class="alert alert-warning">
                                Het is niet mogelijk om je adres te wijzigen wanneer er actieve advertenties aanwezig zijn.
                            </div>
                        </div>
                    @endif

                    <div class="col-12" id="buttonsContainer">
                        <div class="form-group text-center">
                            @if(!is_null($cook))
                                <button type="submit" class="btn btn-light btn-small">Opslaan</button>
                                @if($showAdress)
                                    <a href="{{ route('dashboard.settings.update.location') }}" class="btn btn-outlines">Adres wijzigen</a>
                                @endif
                                <a href="{{ route('dashboard.settings.password.change') }}" class="btn btn-outlines">Wachtwoord wijzigen</a>
                            @else
                                <a href="{{ route('dashboard.settings.first.cookie') }}"
                                    class="btn btn-light btn-small">Gegevens invullen</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page.scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const emailField = document.getElementById('email');
            const emailWarning = document.getElementById('emailWarning');
            const emailFormGroup = document.getElementById('emailFormGroup');

            if (emailField && emailWarning && emailFormGroup) {
                emailField.addEventListener('focus', function () {
                    emailWarning.style.display = 'block';
                    emailFormGroup.classList.remove('warning-hidden');
                });

                emailField.addEventListener('blur', function () {
                    emailWarning.style.display = 'none';
                    emailFormGroup.classList.add('warning-hidden');
                });

                document.addEventListener('click', function (event) {
                    if (event.target !== emailField && !emailWarning.contains(event.target)) {
                        emailWarning.style.display = 'none';
                        emailFormGroup.classList.add('warning-hidden');
                    }
                });
            }

            const phoneInput = document.getElementById('phone');
            const phoneValidationMessage = document.getElementById('phoneValidationMessage');

            if (phoneInput) {
                function validateDutchPhoneNumber(phone) {
                    const cleanedPhone = phone.replace(/[\s\-]/g, '');

                    if (cleanedPhone.match(/^06\d{8}$/)) return true;
                    if (cleanedPhone.match(/^\+316\d{8}$/)) return true;
                    if (cleanedPhone.match(/^0[1-9]\d{8}$/)) return true;
                    if (cleanedPhone.match(/^\+31[1-9]\d{8}$/)) return true;

                    return false;
                }

                phoneInput.addEventListener('keydown', function (e) {
                    const key = e.key;

                    if (key === 'Backspace' || key === 'Delete' || key === 'ArrowLeft' ||
                        key === 'ArrowRight' || key === 'Tab' || key === 'Enter' ||
                        e.ctrlKey || e.altKey || e.metaKey) {
                        return;
                    }

                    if (!/^[0-9+\s\-]$/.test(key)) {
                        e.preventDefault();
                        phoneValidationMessage.textContent = 'Alleen cijfers, +, spaties en - zijn toegestaan';
                        phoneValidationMessage.style.display = 'block';
                        return;
                    }

                    if (this.value.length >= 15 && key !== 'Backspace' && key !== 'Delete') {
                        e.preventDefault();
                        phoneValidationMessage.textContent = 'Telefoonnummer is te lang';
                        phoneValidationMessage.style.display = 'block';
                    }
                });

                phoneInput.addEventListener('input', function (event) {
                    if (this.value.length > 15) {
                        this.value = this.value.substring(0, 15);
                    }

                    if (event.inputType === 'insertText' || event.inputType === 'insertFromPaste') {
                        const value = this.value;
                        const filteredValue = value.replace(/[^0-9+\s\-]/g, '');

                        if (value !== filteredValue) {
                            this.value = filteredValue;
                            phoneValidationMessage.textContent = 'Alleen cijfers, +, spaties en - zijn toegestaan';
                            phoneValidationMessage.style.display = 'block';
                        } else {
                            phoneValidationMessage.style.display = 'none';
                        }
                    }

                    const cleanedNumber = this.value.replace(/[\s\-]/g, '');

                    if (cleanedNumber.length >= 10) {
                        if (validateDutchPhoneNumber(this.value)) {
                            this.setCustomValidity('');
                            phoneValidationMessage.style.display = 'none';
                        } else {
                            phoneValidationMessage.textContent = 'Voer een geldig Nederlands telefoonnummer in (mobiel: 06xxxxxxxx of +316xxxxxxxx, vast: 0xxxxxxxxx of +31xxxxxxxxx)';
                            phoneValidationMessage.style.display = 'block';
                            this.setCustomValidity('Ongeldig telefoonnummer');
                        }
                    } else if (cleanedNumber.length > 0) {
                        this.setCustomValidity('');
                        phoneValidationMessage.style.display = 'none';
                    } else {
                        this.setCustomValidity('');
                        phoneValidationMessage.style.display = 'none';
                    }
                });

                phoneInput.addEventListener('blur', function () {
                    const value = this.value;
                    if (value && !validateDutchPhoneNumber(value)) {
                        phoneValidationMessage.textContent = 'Voer een geldig Nederlands telefoonnummer in (mobiel: 06xxxxxxxx of +316xxxxxxxx, vast: 0xxxxxxxxx or +31xxxxxxxxx)';
                        phoneValidationMessage.style.display = 'block';
                        this.setCustomValidity('Ongeldig telefoonnummer');
                    }
                });
            }
        });
    </script>
@endsection