@extends('layout.main')

@section('content')

    <!-- login title -->
    <div class="ltn__about-us-area pt-20 pb-20">
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
                                <h1 class="section-title">Inloggen</h1>
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
                <div class="col-1"></div>
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
