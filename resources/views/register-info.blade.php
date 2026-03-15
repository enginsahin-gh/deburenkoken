@extends('layout.main')

@section('content')
    <style>
        /* FontAwesome icon fix - verwijdert zwarte streepjes */
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

        /* Verbeterde tooltip styling */
        .tooltip {
            position: relative;
            display: inline-block;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            min-width: 200px;
            max-width: 300px;
            width: max-content;
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
            word-wrap: break-word;
            box-sizing: border-box;
        }

        /* Zorg ervoor dat tooltip binnen scherm blijft */
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

        /* Responsieve tooltip positionering */
        @media (max-width: 768px) {
            .tooltip .tooltiptext {
                max-width: 250px;
                width: 90vw;
                left: 50%;
                transform: translateX(-50%);
            }
        }

        /* Tooltip voor elementen aan de rechterkant van het scherm */
        .tooltip.tooltip-left .tooltiptext {
            left: auto;
            right: 0;
            transform: none;
        }

        .tooltip.tooltip-left .tooltiptext::after {
            left: auto;
            right: 20px;
            margin-left: 0;
        }

        /* Tooltip voor elementen aan de linkerkant van het scherm */
        .tooltip.tooltip-right .tooltiptext {
            left: 0;
            transform: none;
        }

        .tooltip.tooltip-right .tooltiptext::after {
            left: 20px;
            margin-left: 0;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }

        .tooltip:hover .tooltiptext::after {
            visibility: visible;
            opacity: 1;
        }
    </style>
    <div class="page-header">
        <div class="container">
            <h1>Registreer</h1>
        </div>
    </div>
    <!-- register title -->
    <div class="ltn__about-us-area pt-20">
        <div class="container">
            <div class="row justify-content-center">
                <div class="align-self-center">
                    <div class="about-us-info-wrap">
                        <div class="page-title">
                            <h2>Account Aanmaken</h2>
                        </div>
                        <!-- <div class="btn-wrapper">
                                <a href="{{ route('register.now') }}" class="btn btn-light col-12">Start nu!</a>
                            </div> -->
                        <x-csrf-error />
                        @if(session('noMoreAccountsError'))
                            <p class="text-center p-0 m-0 invalid-feedback" style="font-size: 15p;">
                                {{session('noMoreAccountsError')}}</p>
                        @endif
                        <form method="POST" action="{{route('register.submit')}}" class="row form-box" id="registerForm">
                            @csrf
                            <div class="form-group col-6">
                                <label for="username">Thuiskok naam
                                    <div class="tooltip ml-2"><i class="fa fa-info-circle"></i>
                                        <span class="tooltiptext">Dit is de naam die wordt weergeven bij je advertenties. De
                                            Thuiskok naam dient uniek te zijn en mag alleen letters, cijfers en maximaal één
                                            punt of underscore bevatten (niet aan begin of einde).</span>
                                    </div>
                                </label>
                                <input type="text" class="form-control" value="{{old('username')}}" id="username"
                                    name="username" required>
                                <div id="usernameError" class="invalid-feedback" style="display: none;">Ongeldige Thuiskok
                                    naam. Gebruik alleen letters, cijfers en maximaal één punt of underscore (niet aan begin
                                    of einde).</div>
                                @if(session('usernameExists'))
                                    <div class="invalid-feedback">Deze gebruikersnaam is al in gebruik.</div>
                                @endif
                                @if(session('usernameRequired'))
                                    <div class="invalid-feedback">De gebruikersnaam is verplicht.</div>
                                @endif
                                @error('username')
                                    <div class="invalid-feedback">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="form-group col-6">
                                <label for="email">E-mailadres
                                    <div class="tooltip ml-2"><i class="fa fa-info-circle"></i>
                                        <span class="tooltiptext">naam@voorbeeld.com</span>
                                    </div>
                                </label>
                                <input type="email" class="form-control" id="email" value="{{old('email')}}" name="email"
                                    required>
                                @error('email')
                                    <div class="invalid-feedback">{{$message}}</div>
                                @enderror
                                @if(session('emailRequired'))
                                    <div class="invalid-feedback">Het e-mailadres is verplicht.</div>
                                @endif
                                @if(session('emailExists'))
                                    <div class="invalid-feedback">Dit e-mailadres is al in gebruik.</div>
                                @endif
                            </div>

                            <div class="form-group col-6">
                                <label for="password">Wachtwoord
                                    <div class="tooltip ml-2"><i class="fa fa-info-circle"></i>
                                        <span class="tooltiptext">Het wachtwoord moet minimaal 8 tekens, 1 hoofdletter, 1
                                            cijfer en 1 symbool bevatten</span>
                                    </div>
                                </label>
                                <div class="form-row">

                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <i class="far fa-eye" id="togglePassword"></i>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{$message}}</div>
                                @enderror
                            </div>

                            <div class="form-group col-6">
                                <label for="password_confirmation">Wachtwoord controle

                                    <div class="tooltip ml-2"><i class="fa fa-info-circle"></i>
                                        <span class="tooltiptext">Dit wachtwoord moet gelijk zijn.</span>
                                    </div>
                                </label>
                                <div class="form-row">
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" required>
                                    <i class="far fa-eye" id="togglePasswordConfirmed"></i>
                                </div>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{$message}}</div>
                                @enderror
                            </div>

                            <div class="col-12 form-group terms-agreement-section">
                                <p class="mb-2">
                                    <strong>Door een account aan te maken op DeBurenKoken.nl ga je akkoord met het volgende:</strong>
                                </p>
                                <ul class="terms-list mb-3">
                                    <li>Je bereidt je gerechten hygiënisch en volgens de geldende
                                        voedselveiligheidsrichtlijnen (<a href="{{ route('cook.facts') }}" target="_blank"
                                            class="underline">HACCP</a>).</li>
                                    <li>Je vermeldt bij je gerechten duidelijke en juiste allergeneninformatie.</li>
                                    <li>Je begrijpt dat bij structurele of commerciële verkoop aanvullende verplichtingen
                                        kunnen gelden, zoals een KvK-inschrijving en/of registratie bij de Nederlandse
                                        Voedsel- en Warenautoriteit (NVWA).</li>
                                </ul>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">Ik ga akkoord met bovenstaande en de <a
                                            href="{{ route('terms.conditions') }}" target="_blank"
                                            class="underline">algemene voorwaarden</a>.</label>
                                </div>
                            </div>
                            @error('terms')
                                <div class="error">{{$message}}</div>
                            @enderror
                            <div class="form-group col-12 text-center">
                                <button type="submit" class="btn btn-small btn-light col-12">Account aanmaken</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="voordelen">
        <div class="container mt-40">
            <div class="row justify-content-center">
                <div class="align-self-center">
                    <div class="section-title-area ltn__section-title-3 text-center page-title">
                        <h1>De voordelen</h1>
                    </div>
                </div>
            </div>
            <div class="about-register-advantages row">
                <div class="col-6">
                    <div class="row">
                        <h5>Geen verplichtingen, kook op je eigen voorwaarden</h5>
                        <p>Met DeBurenKoken.nl bied je moeiteloos gerechten aan je buurt aan, precies wanneer het jou
                            uitkomt. Jij hebt de volledige vrijheid om te bepalen wat, hoeveel en wanneer je kookt, zonder
                            enige verplichtingen.</p>
                    </div>
                    <div class="row">
                        <h5>Jij kookt, wij zorgen voor de rest</h5>
                        <p>Met DeBurenKoken.nl heb je alle tools die je nodig hebt om direct aan de slag te gaan als
                            Thuiskok. Plaats eenvoudig je gerecht online en wij regelen de rest voor je.</p>
                    </div>
                    <div class="row">
                        <h5>Voordeel voor jou én je buren</h5>
                        <p>Met jouw kookkunsten help je de buren genieten van gezonde, lekkere en gevarieerde maaltijden,
                            terwijl jij er zelf ook iets mee verdient.</p>
                    </div>
                </div>
                <div class="col-6">
                    <img src="{{asset('img/five.jpg')}}" alt="Geniet">
                </div>
            </div>
        </div>
    </div>

    <!-- Info area start-->
    <div class="ltn__service-area pt-115 pb-50">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="section-title-area ltn__section-title-2 text-center">
                        <h1 class="section-title">Hoe werkt het?</h1>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <img src="{{asset('img/hoe4.svg')}}" alt="gratis aanmelden">
                        </div>
                        <div class="service-item-brief">
                            <h4>Gratis aanmelden</h4>
                            <p>Stel je eigen Thuiskok-profiel in en begin direct.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <img src="{{asset('img/hoe5.svg')}}" alt="Contact met Thuiskok">
                        </div>
                        <div class="service-item-brief">
                            <h4>Gerecht plaatsen</h4>
                            <p>Bepaal zelf welk gerecht je kookt, hoeveel porties en wanneer. Met een paar klikken staat je
                                gerecht online.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__service-item-1">
                        <div class="service-item-img">
                            <img src="{{asset('img/hoe6.svg')}}" alt="Geniet">
                        </div>
                        <div class="service-item-brief">
                            <h4>Koken</h4>
                            <p>Geniet van het bereiden van een heerlijk gerecht. Wij zorgen voor de rest!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Start now button -->
    <!-- <div class="ltn__about-us-area pt-10 pb-50">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="align-self-center w-80">
                        <div class="about-us-info-wrap">
                            <div class="section-title-area ltn__section-title-3 text-center">
                                <h1 class="section-title"></h1>
                            </div>
                            <div class="btn-wrapper">
                                <a href="{{ route('register.now') }}" class="btn btn-light col-12">Start nu!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

    <!-- Homecook reviews
        <div class="ltn__about-us-area testimonials">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="align-self-center w-80">
                        <div class="about-us-info-wrap">
                            <div class="page-title">
                                <h1>Woorden van Thuiskoks</h1>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center testimonial-content">
                    <div class="col-lg-4 col-sm-6">
                        <div class="testimonial-item alt">
                            <div class="circle">
                                <img src="{{asset('img/nine.jpg')}}" class="round-image mx-auto" alt="gratis aanmelden">
                            </div>
                            <div class="service-item-brief">
                                <h4>Kookliefhebber, Thuiskok uit Sliedrecht</h4>
                                <hr/>
                                <p>Ik vind het fantastisch om te zien hoe iets eenvoudigs als een maaltijd mensen samenbrengt. Of het nu een snelle doordeweekse maaltijd is of een uitgebreid diner, het is altijd bijzonder om te zien hoe een goed bereide maaltijd een glimlach op iemands gezicht tovert.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="testimonial-item">
                            <div class="circle">
                                <img src="{{asset('img/ten.jpg')}}" class="round-image mx-auto" alt="Contact met Thuiskok">
                            </div>
                            <div class="service-item-brief">
                                <h4>Dethuischef, Thuiskok uit Papendrecht</h4>
                                <hr/>
                                <p>Als kok is koken voor mij veel meer dan alleen een dagelijkse taak; het is een passie die me vreugde en voldoening brengt. Wat ik het meest waardeer aan koken, is de creativiteit die het met zich meebrengt.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="testimonial-item alt">
                            <div class="circle">
                                <img src="{{asset('img/eleven.jpg')}}" class="round-image mx-auto" alt="Geniet">
                            </div>
                            <div class="service-item-brief">
                                <h4>Desmaakmaker, Thuiskok uit Sliedrecht</h4>
                                <hr/>
                                <p>Ik vind het geweldig om te experimenteren met seizoensgebonden producten. Er is iets bijzonders aan het koken met ingrediënten die op dat moment op hun best zijn.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    -->

    <!-- Homecook frequent questions -->
    <div class="ltn__about-us-area pt-10 pb-20 accordion-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="align-self-center w-80">
                    <div class="about-us-info-wrap">
                        <div class="page-title">
                            <!-- <h3>Veelgestelde vragen</h3> -->
                            <h1>Veelgestelde vragen van Thuiskoks</h1>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-0">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse"
                                data-parent="#accordion" href="#collapse-0" aria-expanded="true" aria-controls="collapse-0">
                                Hoe bepaal ik een prijs per portie?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-0" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-0">
                        <div class="panel-body px-3 mb-4">
                            <p>Bij het bepalen van de prijs voor een maaltijd als Thuiskok, moet je rekening houden met de
                                kosten van ingrediënten, de tijd en moeite die je erin steekt. Vergelijk je prijzen met die
                                van andere Thuiskoks in de regio en houd rekening met de portiegrootte en de gebruikte
                                ingrediënten.</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-1">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse"
                                data-parent="#accordion" href="#collapse-1" aria-expanded="true" aria-controls="collapse-1">
                                Hoe ontvang ik mijn betalingen?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-1">
                        <div class="panel-body px-3 mb-4">
                            <p>DeBurenKoken.nl faciliteert online betalingen tussen Thuiskoks en klanten. Het bedrag wordt
                                automatisch toegevoegd aan de portemonnee van de Thuiskok op hun account. Vanuit deze
                                portemonnee kunnen Thuiskoks het bedrag vervolgens naar hun eigen betaalrekening uitbetalen.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading-2">
                        <h3 class="panel-title">
                            <span class="collapsed p-3" role="button" title="" data-toggle="collapse"
                                data-parent="#accordion" href="#collapse-2" aria-expanded="true" aria-controls="collapse-2">
                                Kan ik mijn bestelling annuleren?
                            </span>
                        </h3>
                    </div>
                    <div id="collapse-2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-2">
                        <div class="panel-body px-3 mb-4">
                            <p>Bestellingen kunnen geannuleerd worden tot aan het uiterst bestelmoment.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- <!-- Start now button -->--}}
    {{-- <div class="ltn__about-us-area register pt-10 pb-50">--}}
        {{-- <div class="container">--}}
            {{-- <div class="row justify-content-center">--}}
                {{-- <div class="align-self-center w-80">--}}
                    {{-- <div class="about-us-info-wrap">--}}
                        {{-- <div class="section-title-area ltn__section-title-3 text-center">--}}
                            {{-- <h1 class="section-title"></h1>--}}
                            {{-- </div>--}}
                        {{-- <div class="btn-wrapper">--}}
                            {{-- <a href="{{ route('register.now') }}" class="fixed-bottom btn btn-light col-12">Start
                                nu!</a>--}}
                            {{-- </div>--}}
                        {{-- </div>--}}
                    {{-- </div>--}}
                {{-- </div>--}}
            {{-- </div>--}}
        {{-- </div>--}}

@endsection

@section('page.scripts')
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const toggleConfirmed = document.querySelector('#togglePasswordConfirmed');
        const password = document.querySelector('#password');
        const passwordConfirmed = document.querySelector('#password_confirmation');
        const usernameInput = document.querySelector('#username');
        const usernameError = document.querySelector('#usernameError');
        const emailInput = document.querySelector('#email');
        const registerForm = document.querySelector('#registerForm');

        // Dinamische tooltip positionering
        document.addEventListener('DOMContentLoaded', function () {
            const tooltips = document.querySelectorAll('.tooltip');

            tooltips.forEach(tooltip => {
                const tooltipText = tooltip.querySelector('.tooltiptext');

                tooltip.addEventListener('mouseenter', function () {
                    // Reset classes
                    this.classList.remove('tooltip-left', 'tooltip-right');

                    // Wacht even zodat de tooltip zich kan positioneren
                    setTimeout(() => {
                        const tooltipRect = tooltipText.getBoundingClientRect();
                        const windowWidth = window.innerWidth;

                        // Check of tooltip buiten het scherm valt
                        if (tooltipRect.left < 0) {
                            this.classList.add('tooltip-right');
                        } else if (tooltipRect.right > windowWidth) {
                            this.classList.add('tooltip-left');
                        }
                    }, 10);
                });
            });
        });

        togglePassword.addEventListener('click', () => {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';

            password.setAttribute('type', type);
            togglePassword.classList.toggle('fa-eye-slash');
        });

        toggleConfirmed.addEventListener('click', () => {
            const type = passwordConfirmed.getAttribute('type') === 'password' ? 'text' : 'password';

            passwordConfirmed.setAttribute('type', type);
            toggleConfirmed.classList.toggle('fa-eye-slash');
        });

        // Username validation
        usernameInput.addEventListener('input', validateUsername);

        function validateUsername() {
            const username = usernameInput.value;

            // Start and end with alphanumeric
            const validStartEnd = /^[a-zA-Z0-9].*[a-zA-Z0-9]$|^[a-zA-Z0-9]$/;

            // Count total dots and underscores (should be 0 or 1 total)
            const dotsCount = (username.match(/\./g) || []).length;
            const underscoresCount = (username.match(/_/g) || []).length;
            const totalSpecialChars = dotsCount + underscoresCount;

            // Check if there are any invalid characters
            const onlyValidChars = /^[a-zA-Z0-9._]*$/.test(username);

            // Check for adjacent special characters
            const noAdjacentSpecials = !/[._][._]/.test(username);

            if (username === '') {
                usernameError.style.display = 'none';
                usernameInput.setCustomValidity('');
                return;
            }

            if (!validStartEnd.test(username) || !onlyValidChars || !noAdjacentSpecials || totalSpecialChars > 1) {
                usernameError.style.display = 'block';
                usernameInput.setCustomValidity('Ongeldige Thuiskok naam');
            } else {
                usernameError.style.display = 'none';
                usernameInput.setCustomValidity('');
            }
        }

        // Email lowercase enforcement
        function enforceEmailLowercase() {
            const currentValue = emailInput.value;
            const lowercaseValue = currentValue.toLowerCase();

            if (currentValue !== lowercaseValue) {
                // Store cursor position
                const cursorPosition = emailInput.selectionStart;

                // Update value to lowercase
                emailInput.value = lowercaseValue;

                // Restore cursor position
                emailInput.setSelectionRange(cursorPosition, cursorPosition);
            }
        }

        // Add event listeners for email input
        emailInput.addEventListener('input', enforceEmailLowercase);
        emailInput.addEventListener('paste', function (event) {
            // Small delay to allow paste to complete, then enforce lowercase
            setTimeout(enforceEmailLowercase, 1);
        });

        // Additional security: check on focus out
        emailInput.addEventListener('blur', enforceEmailLowercase);

        registerForm.addEventListener('submit', function (event) {
            validateUsername();
            // Final check to ensure email is lowercase before submitting
            enforceEmailLowercase();

            if (usernameInput.validity.valid === false) {
                event.preventDefault();
            }
        });
    </script>
@endsection
