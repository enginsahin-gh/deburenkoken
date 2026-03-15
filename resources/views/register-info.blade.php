@extends('layout.main')
@section('content')
    <div class="dbk-auth-wrapper">
        <div class="container">
            <div class="dbk-auth-card">
                <div class="dbk-auth-left">
                    <div class="dbk-auth-left-content">
                        <h2>Word Thuiskok! 🍳</h2>
                        <p>Deel jouw heerlijke gerechten met de buurt en verdien er extra mee bij.</p>
                    </div>
                </div>
                <div class="dbk-auth-right">
                    <h1>Account aanmaken</h1>
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
                                </div>
                            </label>
                            <div class="dbk-password-wrap">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <i class="far fa-eye dbk-toggle-pw" id="togglePassword"></i>
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
                            <div class="dbk-password-wrap">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                <i class="far fa-eye dbk-toggle-pw" id="togglePasswordConfirmed"></i>
                            </div>
                            <small id="passwordConfirmationError" class="form-text text-danger"></small>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">Ik ga akkoord met de <a href="{{route('terms.conditions')}}"><u>algemene voorwaarden</u></a>.</label>
                        </div>
                        <small id="termsError" class="form-text text-danger d-block"></small>
                        @error('terms')
                        <div class="error">{{$message}}</div>
                        @enderror
                        <div class="form-group dbk-auth-submit">
                            <button type="submit" class="dbk-btn-primary">Account aanmaken</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@if(session('noMoreAccountsError'))
    <p class="text-center p-0 m-0 invalid-feedback" style="font-size: 15p;">{{session('noMoreAccountsError')}}</p>
@endif
@section('page.scripts')
    <script>
        // KEEP ALL THE ORIGINAL register-info.blade.php JavaScript validation code EXACTLY
        const togglePassword = document.querySelector('#togglePassword');
        const toggleConfirmed = document.querySelector('#togglePasswordConfirmed');
        const password = document.querySelector('#password');
        const passwordConfirmed = document.querySelector('#password_confirmation');
        togglePassword.addEventListener('click', () => { const type = password.getAttribute('type') === 'password' ? 'text' : 'password'; password.setAttribute('type', type); togglePassword.classList.toggle('fa-eye-slash'); });
        toggleConfirmed.addEventListener('click', () => { const type = passwordConfirmed.getAttribute('type') === 'password' ? 'text' : 'password'; passwordConfirmed.setAttribute('type', type); toggleConfirmed.classList.toggle('fa-eye-slash'); });
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            const usernameInput = document.getElementById('username');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const passwordConfirmationInput = document.getElementById('password_confirmation');
            const termsCheckbox = document.getElementById('terms');
            function validateEmail(email) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email); }
            function checkPasswordStrength(pw) {
                var strength = 0;
                var bar = document.getElementById('passwordStrengthBar');
                var txt = document.getElementById('passwordStrengthText');
                document.getElementById('passwordStrength').style.display = 'block';
                if (pw.length >= 8) strength += 25;
                if (pw.match(/[A-Z]/)) strength += 25;
                if (pw.match(/[0-9]/)) strength += 25;
                if (pw.match(/[^a-zA-Z0-9]/)) strength += 25;
                bar.style.width = strength + '%';
                if (strength <= 25) { bar.style.backgroundColor = '#dc3545'; txt.textContent = 'Zwak'; txt.style.color = '#dc3545'; return false; }
                else if (strength <= 50) { bar.style.backgroundColor = '#ffc107'; txt.textContent = 'Matig'; txt.style.color = '#ffc107'; return false; }
                else if (strength <= 75) { bar.style.backgroundColor = '#28a745'; txt.textContent = 'Goed'; txt.style.color = '#28a745'; return true; }
                else { bar.style.backgroundColor = '#28a745'; txt.textContent = 'Sterk'; txt.style.color = '#28a745'; return true; }
            }
            usernameInput.addEventListener('blur', function() { if (this.value.trim() === '') document.getElementById('usernameError').textContent = 'Gebruikersnaam is verplicht'; else if (this.value.length < 3) document.getElementById('usernameError').textContent = 'Gebruikersnaam moet minimaal 3 tekens bevatten'; else document.getElementById('usernameError').textContent = ''; });
            emailInput.addEventListener('blur', function() { if (this.value.trim() === '') document.getElementById('emailError').textContent = 'E-mailadres is verplicht'; else if (!validateEmail(this.value)) document.getElementById('emailError').textContent = 'Voer een geldig e-mailadres in (voorbeeld@example.com)'; else document.getElementById('emailError').textContent = ''; });
            emailInput.addEventListener('input', function() { if (this.value.trim() !== '' && validateEmail(this.value)) document.getElementById('emailError').textContent = ''; });
            passwordInput.addEventListener('input', function() {
                var isStrong = checkPasswordStrength(this.value);
                if (this.value.trim() === '') document.getElementById('passwordError').textContent = 'Wachtwoord is verplicht';
                else if (this.value.length < 8) document.getElementById('passwordError').textContent = 'Wachtwoord moet minimaal 8 tekens bevatten';
                else if (!isStrong) document.getElementById('passwordError').textContent = 'Wachtwoord moet minimaal één hoofdletter, één cijfer en één speciaal teken bevatten';
                else document.getElementById('passwordError').textContent = '';
                if (passwordConfirmationInput.value.trim() !== '') { if (this.value !== passwordConfirmationInput.value) document.getElementById('passwordConfirmationError').textContent = 'Wachtwoorden komen niet overeen'; else document.getElementById('passwordConfirmationError').textContent = ''; }
            });
            passwordConfirmationInput.addEventListener('input', function() { if (this.value !== passwordInput.value) document.getElementById('passwordConfirmationError').textContent = 'Wachtwoorden komen niet overeen'; else document.getElementById('passwordConfirmationError').textContent = ''; });
            termsCheckbox.addEventListener('change', function() { if (!this.checked) document.getElementById('termsError').textContent = 'Je moet akkoord gaan met de algemene voorwaarden'; else document.getElementById('termsError').textContent = ''; });
            form.addEventListener('submit', function(e) {
                var isValid = true;
                if (usernameInput.value.trim() === '') { document.getElementById('usernameError').textContent = 'Gebruikersnaam is verplicht'; isValid = false; } else if (usernameInput.value.length < 3) { document.getElementById('usernameError').textContent = 'Gebruikersnaam moet minimaal 3 tekens bevatten'; isValid = false; }
                if (emailInput.value.trim() === '') { document.getElementById('emailError').textContent = 'E-mailadres is verplicht'; isValid = false; } else if (!validateEmail(emailInput.value)) { document.getElementById('emailError').textContent = 'Voer een geldig e-mailadres in (voorbeeld@example.com)'; isValid = false; }
                if (passwordInput.value.trim() === '') { document.getElementById('passwordError').textContent = 'Wachtwoord is verplicht'; isValid = false; } else if (passwordInput.value.length < 8) { document.getElementById('passwordError').textContent = 'Wachtwoord moet minimaal 8 tekens bevatten'; isValid = false; } else if (!passwordInput.value.match(/[A-Z]/) || !passwordInput.value.match(/[0-9]/) || !passwordInput.value.match(/[^a-zA-Z0-9]/)) { document.getElementById('passwordError').textContent = 'Wachtwoord moet minimaal één hoofdletter, één cijfer en één speciaal teken bevatten'; isValid = false; }
                if (passwordConfirmationInput.value !== passwordInput.value) { document.getElementById('passwordConfirmationError').textContent = 'Wachtwoorden komen niet overeen'; isValid = false; }
                if (!termsCheckbox.checked) { document.getElementById('termsError').textContent = 'Je moet akkoord gaan met de algemene voorwaarden'; isValid = false; }
                if (!isValid) e.preventDefault();
            });
        });
    </script>
@endsection
