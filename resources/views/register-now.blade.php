@extends('layout.main')

@section('content')
    <!-- register title -->
    <div class="ltn__about-us-area pt-20 pb-20">
        <div class="container">
            <div class="row justify-content-center">
                <div class="about-us-info-wrap mb-0">
                    <div class="section-title-area mb-0">
                        <h1 class="section-title">Account aanmaken</h1>
                    </div>
                </div>
            </div>
            <div class="col-7 mx-auto">
                @if($errors->has('csrf'))
                    <div class="alert alert-warning mb-3" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> {{ $errors->first('csrf') }}
                    </div>
                @endif
                <form method="POST" action="{{route('register.submit')}}" id="registerForm">
                    @csrf
                    <div class="form-group">
                        <label for="username">Thuiskok naam
                            <div class="tooltip ml-2"><i class="fa-regular fa-circle-info"></i>
                                <span class="tooltiptext">Dit is de naam die wordt weergeven bij je advertenties, de naam dient uniek te zijn</span>
                            </div>
                        </label>
                        <input type="text" class="form-control" value="{{old('username')}}" id="username" name="username" required>
                        <small id="usernameError" class="form-text text-danger"></small>
                        @error('username')
                            <div class="error">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">E-mailadres
                            <div class="tooltip ml-2"><i class="fa-regular fa-circle-info"></i>
                                <span class="tooltiptext">Het email adres moet er zo uit zien: voorbeeld@example.com</span>
                            </div>
                        </label>
                        <input type="email" class="form-control" id="email" value="{{old('email')}}" name="email" required>
                        <small id="emailError" class="form-text text-danger"></small>
                        @error('email')
                            <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Wachtwoord
                            <div class="tooltip ml-2"><i class="fa-regular fa-circle-info"></i>
                                <span class="tooltiptext">Het wachtwoord moet minimaal 8 tekens, 1 hoofdletter 1 cijfer en 1 symbool bevatten</span>
                            </div></label>
                        <div class="form-row">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <i class="far fa-eye" id="togglePassword"></i>
                        </div>
                        <small id="passwordError" class="form-text text-danger"></small>
                        <div id="passwordStrength" class="mt-2" style="display: none;">
                            <small class="d-block mb-1">Wachtwoord sterkte:</small>
                            <div class="progress" style="height: 5px;">
                                <div id="passwordStrengthBar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small id="passwordStrengthText" class="form-text mt-1"></small>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Wachtwoord controle
                            <div class="tooltip ml-2"><i class="fa-regular fa-circle-info"></i>
                                <span class="tooltiptext">Dit wachtwoord moet gelijk zijn.</span>
                            </div>
                        </label>
                        <div class="form-row">
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            <i class="far fa-eye" id="togglePasswordConfirmed"></i>
                        </div>
                        <small id="passwordConfirmationError" class="form-text text-danger"></small>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">Ik ga akkoord met de <a href="{{route('terms.conditions')}}"><u>algemene voorwaarden</u></a>.</label>
                    </div>
                    <small id="termsError" class="form-text text-danger d-block"></small>
                    @error('terms')
                    <div class="error">{{$message}}</div>
                    @enderror
                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-small btn-light col-12">Account aanmaken</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@if(session('noMoreAccountsError'))
    <p class="text-center p-0 m-0 invalid-feedback" style="font-size: 15p;">{{session('noMoreAccountsError')}}</p>
@endif
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
        });

        // Formulier validatie
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            const usernameInput = document.getElementById('username');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const passwordConfirmationInput = document.getElementById('password_confirmation');
            const termsCheckbox = document.getElementById('terms');
            
            // Email validatie functie
            function validateEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }
            
            // Wachtwoord sterkte validatie
            function checkPasswordStrength(password) {
                let strength = 0;
                const strengthBar = document.getElementById('passwordStrengthBar');
                const strengthText = document.getElementById('passwordStrengthText');
                
                // Toon de wachtwoord sterkte indicator
                document.getElementById('passwordStrength').style.display = 'block';
                
                // Controleer lengte
                if (password.length >= 8) {
                    strength += 25;
                }
                
                // Controleer op hoofdletters
                if (password.match(/[A-Z]/)) {
                    strength += 25;
                }
                
                // Controleer op cijfers
                if (password.match(/[0-9]/)) {
                    strength += 25;
                }
                
                // Controleer op speciale tekens
                if (password.match(/[^a-zA-Z0-9]/)) {
                    strength += 25;
                }
                
                // Update de sterkte balk
                strengthBar.style.width = strength + '%';
                
                // Update de kleur van de balk
                if (strength <= 25) {
                    strengthBar.style.backgroundColor = '#dc3545'; // rood
                    strengthText.textContent = 'Zwak';
                    strengthText.style.color = '#dc3545';
                    return false;
                } else if (strength <= 50) {
                    strengthBar.style.backgroundColor = '#ffc107'; // geel
                    strengthText.textContent = 'Matig';
                    strengthText.style.color = '#ffc107';
                    return false;
                } else if (strength <= 75) {
                    strengthBar.style.backgroundColor = '#28a745'; // groen
                    strengthText.textContent = 'Goed';
                    strengthText.style.color = '#28a745';
                    return true;
                } else {
                    strengthBar.style.backgroundColor = '#28a745'; // groen
                    strengthText.textContent = 'Sterk';
                    strengthText.style.color = '#28a745';
                    return true;
                }
            }
            
            // Validatie van de gebruikersnaam
            usernameInput.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    document.getElementById('usernameError').textContent = 'Gebruikersnaam is verplicht';
                } else if (this.value.length < 3) {
                    document.getElementById('usernameError').textContent = 'Gebruikersnaam moet minimaal 3 tekens bevatten';
                } else {
                    document.getElementById('usernameError').textContent = '';
                }
            });
            
            // Email validatie - wanneer focus verlaten wordt
            emailInput.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    document.getElementById('emailError').textContent = 'E-mailadres is verplicht';
                } else if (!validateEmail(this.value)) {
                    document.getElementById('emailError').textContent = 'Voer een geldig e-mailadres in (voorbeeld@example.com)';
                } else {
                    document.getElementById('emailError').textContent = '';
                }
            });
            
            // Real-time email validatie
            emailInput.addEventListener('input', function() {
                if (this.value.trim() !== '' && validateEmail(this.value)) {
                    document.getElementById('emailError').textContent = '';
                }
            });
            
            // Wachtwoord validatie
            passwordInput.addEventListener('input', function() {
                const isStrong = checkPasswordStrength(this.value);
                
                if (this.value.trim() === '') {
                    document.getElementById('passwordError').textContent = 'Wachtwoord is verplicht';
                } else if (this.value.length < 8) {
                    document.getElementById('passwordError').textContent = 'Wachtwoord moet minimaal 8 tekens bevatten';
                } else if (!isStrong) {
                    document.getElementById('passwordError').textContent = 'Wachtwoord moet minimaal één hoofdletter, één cijfer en één speciaal teken bevatten';
                } else {
                    document.getElementById('passwordError').textContent = '';
                }
                
                // Controleer of de wachtwoorden overeenkomen als het bevestigingsveld is ingevuld
                if (passwordConfirmationInput.value.trim() !== '') {
                    if (this.value !== passwordConfirmationInput.value) {
                        document.getElementById('passwordConfirmationError').textContent = 'Wachtwoorden komen niet overeen';
                    } else {
                        document.getElementById('passwordConfirmationError').textContent = '';
                    }
                }
            });
            
            // Wachtwoord bevestiging validatie
            passwordConfirmationInput.addEventListener('input', function() {
                if (this.value !== passwordInput.value) {
                    document.getElementById('passwordConfirmationError').textContent = 'Wachtwoorden komen niet overeen';
                } else {
                    document.getElementById('passwordConfirmationError').textContent = '';
                }
            });
            
            // Voorwaarden validatie
            termsCheckbox.addEventListener('change', function() {
                if (!this.checked) {
                    document.getElementById('termsError').textContent = 'Je moet akkoord gaan met de algemene voorwaarden';
                } else {
                    document.getElementById('termsError').textContent = '';
                }
            });
            
            // Formulier validatie bij verzenden
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Valideer gebruikersnaam
                if (usernameInput.value.trim() === '') {
                    document.getElementById('usernameError').textContent = 'Gebruikersnaam is verplicht';
                    isValid = false;
                } else if (usernameInput.value.length < 3) {
                    document.getElementById('usernameError').textContent = 'Gebruikersnaam moet minimaal 3 tekens bevatten';
                    isValid = false;
                }
                
                // Valideer email
                if (emailInput.value.trim() === '') {
                    document.getElementById('emailError').textContent = 'E-mailadres is verplicht';
                    isValid = false;
                } else if (!validateEmail(emailInput.value)) {
                    document.getElementById('emailError').textContent = 'Voer een geldig e-mailadres in (voorbeeld@example.com)';
                    isValid = false;
                }
                
                // Valideer wachtwoord
                if (passwordInput.value.trim() === '') {
                    document.getElementById('passwordError').textContent = 'Wachtwoord is verplicht';
                    isValid = false;
                } else if (passwordInput.value.length < 8) {
                    document.getElementById('passwordError').textContent = 'Wachtwoord moet minimaal 8 tekens bevatten';
                    isValid = false;
                } else if (!passwordInput.value.match(/[A-Z]/) || !passwordInput.value.match(/[0-9]/) || !passwordInput.value.match(/[^a-zA-Z0-9]/)) {
                    document.getElementById('passwordError').textContent = 'Wachtwoord moet minimaal één hoofdletter, één cijfer en één speciaal teken bevatten';
                    isValid = false;
                }
                
                // Valideer wachtwoord bevestiging
                if (passwordConfirmationInput.value !== passwordInput.value) {
                    document.getElementById('passwordConfirmationError').textContent = 'Wachtwoorden komen niet overeen';
                    isValid = false;
                }
                
                // Valideer voorwaarden
                if (!termsCheckbox.checked) {
                    document.getElementById('termsError').textContent = 'Je moet akkoord gaan met de algemene voorwaarden';
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault(); // Voorkom verzenden als validatie faalt
                }
            });
        });
    </script>
@endsection