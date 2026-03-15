@extends('layout.main')

@section('content')

    <!-- login title -->
    <div class="ltn__about-us-area pt-20 pb-20" style="min-height: 80vh; display: flex; align-items: center;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-md-12">
                    <div class="login-box fade-in-up">
                        <div class="row">
                            <div class="col-md-5 col-12" style="background: linear-gradient(135deg, var(--dbk-primary-dark), var(--dbk-primary)); display: flex; align-items: center; justify-content: center; padding: 40px;">
                                <img src="{{asset('img/login-sideImg.svg')}}" class="login-sideImg" style="filter: brightness(10); max-width: 80%;" />
                            </div>

                            <div class="col-md-7 col-12" style="padding: 32px;">
                                <h1 class="section-title" style="text-align: left;">Welkom terug</h1>
                                <div class="col-12 p-0">
                                    <x-csrf-error />
                                    @if(!$errors->has('csrf') && $errors->any())
                                        <h4 class="tx-red">{{$errors->first()}}</h4>
                                    @elseif(!$errors->any())
                                        <span style="height: 26px; margin-bottom: 15px; display: block;"></span>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        @if(Session::has('status'))
                                            <p class="alert alert-info">{{ Session::get('status') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12 p-0">
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
                                            <label for="password">Wachtwoord
                                            </label>
                                            <span class="tx-red float-right"><a href="{{route('login.forgot')}}">Wachtwoord vergeten?</a></span>
                                            <div class="form-row">
                                                <input type="password" class="form-control" id="password" name="password" required>
                                                <i class="far fa-eye" id="togglePassword"></i>
                                            </div>
                                            @error('password')
                                            <div class="invalid-feedback">{{$message}}</div>
                                            @enderror
                                        </div>
                                            <div class="form-check form-check-inline"  @if(isset($essentialCookies) && $essentialCookies) style="display: none;" @endif>
                                                <input type="hidden"  name="remember" value="off">
                                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                                <label class="form-check-label" for="remember">Ingelogd blijven voor 30 dagen</label>
                                            </div>
                                            <div class="form-group mt-3">
                                                <button type="submit" class="btn btn-small btn-light col-12">Inloggen</button>
                                            </div>
                                            <div class="form-group text-center">
                                                Nog geen account als Thuiskok? <a href="{{route('register.info')}}" class="tx-red">Account aanmaken</a>
                                            </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
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
