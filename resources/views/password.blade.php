@extends('layout.main')

@section('content')
    <section class="clearfix mt-3">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="center-box">
                        <div class="row">
                            <div class="col-12 text-center">
                                <h3 class="section-title">Nieuw wachtwoord instellen</h3>
                                
                                <div class="col-7 mx-auto">
                                    <form method="POST" action="{{route('login.forgot.reset.submit')}}">
                                        @csrf
                                        <div class="form-group">
                                            <label for="password">Nieuw wachtwoord
                                                <div class="tooltip ml-2"><i class="fa fa-info-circle"></i>
                                                    <span class="tooltiptext">Het wachtwoord moet minimaal 8 tekens, 1 hoofdletter, 1 cijfer en 1 symbool bevatten.</span>
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
                                        <div class="form-group">
                                            <label for="password_confirmation">Wachtwoord bevestigen
                                                <div class="tooltip ml-2"><i class="fa fa-info-circle"></i>
                                                    <span class="tooltiptext">Dit wachtwoord moet gelijk zijn.</span>
                                                </div>
                                            </label>                            
                                            <div class="form-row">
                                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                                <i class="far fa-eye" id="togglePasswordConfirmed"></i>
                                            </div>
                                            @error('password_confirmation')
                                            <div class="invalid-feedback">{{$message}}</div>
                                            @enderror
                                        </div>

                                        <input type="hidden" name="email" value="{{request('email')}}">
                                        <input type="hidden" name="token" value="{{request('token')}}">
                                        <div class="form-group mt-3 text-center">
                                            <button type="submit" class="btn btn-small btn-light col-6">Wachtwoord instellen</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        /* Fix voor FontAwesome iconen - voorkom onderstreping en borders */
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

        /* Tooltip container */
        .tooltip {
            position: relative;
            display: inline-block;
            text-decoration: none !important;
            border-bottom: none !important;
            box-shadow: none !important;
        }

        .tooltip .fa-info-circle {
            color: black;
            cursor: help;
            font-size: 14px;
            margin-left: 5px;
        }

        .tooltip .fa-info-circle:hover {
            color: #495057;
        }

        /* Tooltip text */
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
            font-size: 12px;
            font-weight: normal;
            line-height: 1.4;
        }

        /* Tooltip pijltje */
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

        /* Toon tooltip bij hover */
        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }

        /* Mobile aanpassingen voor tooltips */
        @media (max-width: 767px) {
            .tooltip .tooltiptext {
                width: 250px;
                left: 50%;
                transform: translateX(-50%);
            }
        }

        /* Form styling aanpassingen */
        .form-row {
            position: relative;
        }

        .form-row input {
            padding-right: 40px;
        }

        .form-row i {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }

        .form-row i:hover {
            color: #495057;
        }
    </style>
@endsection

@section('page.scripts')
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const toggleConfirmed = document.querySelector('#togglePasswordConfirmed');
        const password = document.querySelector('#password');
        const passwordConfirmed = document.querySelector('#password_confirmation');

        togglePassword.addEventListener('click', () => {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';

            password.setAttribute('type', type);
            togglePassword.classList.toggle('fa-eye-slash');
        });

        toggleConfirmed.addEventListener('click', () => {
            const type = passwordConfirmed.getAttribute('type') === 'password' ? 'text' : 'password';

            passwordConfirmed.setAttribute('type', type);
            toggleConfirmed.classList.toggle('fa-eye-slash');
        })
    </script>
@endsection