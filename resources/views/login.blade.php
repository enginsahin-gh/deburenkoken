@extends('layout.main')
@section('content')
    <div class="dbk-auth-wrapper">
        <div class="container">
            <div class="dbk-auth-card">
                <div class="dbk-auth-left">
                    <div class="dbk-auth-left-content">
                        <h2>Welkom terug! 👋</h2>
                        <p>Log in en ontdek wat er vandaag gekookt wordt in jouw buurt.</p>
                        <img src="{{asset('img/login-sideImg.svg')}}" class="dbk-auth-illustration" alt="Login" />
                    </div>
                </div>
                <div class="dbk-auth-right">
                    <h1>Inloggen</h1>
                    <x-csrf-error />
                    @if(!$errors->has('csrf') && $errors->any())
                        <div class="dbk-auth-error">{{$errors->first()}}</div>
                    @endif
                    @if(Session::has('status'))
                        <div class="alert alert-info">{{ Session::get('status') }}</div>
                    @endif
                    <form method="POST" action="{{route('login.submit')}}">
                        @csrf
                        <div class="form-group">
                            <label for="email">E-mailadres</label>
                            <input type="text" class="form-control" value="{{old('email')}}" id="email" name="email" required>
                            @error('email')
                            <div class="error">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <div class="dbk-label-row">
                                <label for="password">Wachtwoord</label>
                                <a href="{{route('login.forgot')}}" class="dbk-forgot-link">Wachtwoord vergeten?</a>
                            </div>
                            <div class="dbk-password-wrap">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <i class="far fa-eye dbk-toggle-pw" id="togglePassword"></i>
                            </div>
                            @error('password')
                            <div class="error">{{$message}}</div>
                            @enderror
                        </div>
                        <div class="form-check" @if(isset($essentialCookies) && $essentialCookies) style="display: none;" @endif>
                            <input type="hidden" name="remember" value="off">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Ingelogd blijven voor 30 dagen</label>
                        </div>
                        <div class="form-group dbk-auth-submit">
                            <button type="submit" class="dbk-btn-primary">Inloggen</button>
                        </div>
                        <div class="dbk-auth-footer">
                            Nog geen account als Thuiskok? <a href="{{route('register.info')}}">Account aanmaken</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page.scripts')
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        togglePassword.addEventListener('click', () => {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            togglePassword.classList.toggle('fa-eye-slash');
        });
    </script>
@endsection
