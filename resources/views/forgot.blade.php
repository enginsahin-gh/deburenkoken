@extends('layout.main')

@section('content')
    <section class="clearfix pt-4">
        <div class="container">
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <div class="login-box">
                        <div class="row">
                            <div class="col-5">
                                <img src="{{asset('img/login-sideImg.svg')}}" class="login-sideImg" />
                            </div>
                            <div class="col-7">
                                <h1>Wachtwoord opnieuw instellen</h1>
                                <p class="mt-50">Vul hieronder je e-mailadres in en je ontvangt dan binnen enkele minuten een e-mail waarmee je een nieuw wachtwoord kunt instellen.</p>
                                <div class="row">
                                    <div class="col-12">
                                        @if(Session::has('status'))
                                            <p class="alert alert-info">{{ Session::get('status') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <form method="POST" action="{{route('login.forgot.submit')}}" id="forgotPasswordForm">
                                    @csrf
                                    <div class="form-group">
                                        <input type="email" class="form-control" value="{{old('email')}}" id="email" name="email" placeholder="E-mail" required>
                                        <small id="emailError" class="form-text text-danger"></small>
                                        @error('email')
                                        <div class="error">{{$message}}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group mt-3">
                                        <button type="submit" class="btn btn-small btn-light col-12">Wachtwoord opnieuw instellen</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-1"></div>
            </div>
        </div>
    </section>
@endsection

@section('page.scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('forgotPasswordForm');
            const emailInput = document.getElementById('email');
            const emailError = document.getElementById('emailError');
            
            // Email validatie functie
            function validateEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }
            
            // Formulier validatie bij verzenden
            form.addEventListener('submit', function(e) {
                if (emailInput.value.trim() === '') {
                    emailError.textContent = 'E-mailadres is verplicht';
                    e.preventDefault();
                } else if (!validateEmail(emailInput.value)) {
                    emailError.textContent = 'Voer een geldig e-mailadres in';
                    e.preventDefault();
                }
            });
        });
    </script>
@endsection