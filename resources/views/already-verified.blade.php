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
                                <h1>E-mailadres reeds geverifieerd</h1>
                                <p class="mt-50">Je e-mailadres is op een eerder moment succesvol geverifieerd. Je kunt 
                                    <a href="{{ route('login.home') }}" class="link-primary">hier</a> inloggen. 
                                    </p>
                                <p>
                                    Ondervind je problemen met je account? Neem dan contact met ons op via het 
                                    <a href="{{route('contact')}}" class="link-primary">contactformulier</a>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-1"></div>
            </div>
        </div>
    </section>

    <style>
    .login-box {
        background: #fff;
        border-radius: 10px;
        padding: 2rem;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    .login-sideImg {
        width: 100%;
        height: auto;
        object-fit: cover;
    }

    .social-icon {
        transition: transform 0.2s;
    }

    .social-icon:hover {
        transform: scale(1.1);
    }

    .link-primary {
        color: #FFA500;
        text-decoration: none;
    }

    .link-primary:hover {
        text-decoration: underline;
    }

    h1 {
        color: #333;
        font-size: 1.8rem;
        margin-bottom: 1.5rem;
    }

    p {
        color: #666;
        line-height: 1.6;
    }

    .mt-50 {
        margin-top: 50px;
    }

    .social-links {
        border-top: 1px solid #eee;
        padding-top: 1.5rem;
    }
    </style>
@endsection