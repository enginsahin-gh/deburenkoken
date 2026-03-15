@extends('layout.main')
@section('content')
    <section class="clearfix mt-3">
        <div class="container">
            <div class="row">
                <div class="col-1"></div>
                <div class="col-10">
                    <div class="login-box">
                        <div class="row">
                            <div class="col-5">
                                <img src="{{asset('img/login-sideImg.svg')}}" class="login-sideImg" />
                            </div>
                            <div class="col-7 pr-50">
                                @if($passwordResetMail)
                                    <h1>Aanvraag ontvangen</h1>
                                    <p class="mt-50">Je aanvraag is ontvangen. Indien je e-mailadres bij ons bekend is, ontvang je binnen enkele minuten een e-mail waarmee je een nieuw wachtwoord kunt instellen.</p>
                                    <a href="{{route('home')}}" class="btn">Ga naar startpagina</a>
                                @elseif($passwordReset)
                                    <h3>Nieuw wachtwoord succesvol ingesteld</h3>
                                    <p class="mt-50">Je wachtwoord is succesvol opgeslagen en je bent automatisch ingelogd. Druk op de onderstaande knop om naar je Thuiskok omgeving te gaan.</p>
                                    <a href="{{route('dashboard.dishes.new')}}" class="btn">Ga naar Mijn Omgeving</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-1"></div>
            </div>
        </div>
    </section>
@endsection
