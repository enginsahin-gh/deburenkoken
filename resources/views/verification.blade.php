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
                                @if($verificationFailed)
                                    <h1>Je e-mail kon niet geverifieerd worden.</h1>
                                    <p class="mt-50">Je e-mail kon niet geverifieerd worden. Vul uw email adres hieronder in om een nieuwe verificatie mail aan te vragen:</p>
                                    <form method="POST" action="{{route('register.verification')}}">
                                        @csrf
                                        <div class="form-group">
                                            <label for="email">
                                            </label>
                                            <input type="text" class="form-control" value="{{old('email')}}" id="email" name="email" placeholder="E-mail" required>
                                            @error('email')
                                                <div class="error">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group mt-3">
                                            <button type="submit"class="btn btn-small btn-light col-12">Verificatiemail versturen</button>
                                        </div>
                                    </form>
                                    <div class='c-signout'>
                                        <a href="{{route('logout')}}" class="ico-logout"><img src="{{asset('img/sidebar/icon-logout-w.svg')}}" /><img src="{{asset('img/sidebar/icon-logout-active.svg')}}" />Uitloggen</a>
                                    </div>

                                @elseif($verificationSend)
                                    @if($resend)
                                        <h1>Verificatiemail verzonden</h1>
                                        <p>Je aanvraag voor een activatiemail is succesvol ontvangen. Binnen enkele minuten zal er (als het een bekend mailadres is) een mail verstuurd worden.</p>

                                    @else
                                        <h1>Account aanvragen succesvol</h1>
                                        <p>Je aanvraag voor een account is succesvol ontvangen. Binnen enkele minuten ontvang je een e-mail ter verificatie.</p>
                                        <p>Als je de e-mail niet in je inbox ziet, controleer dan ook je spam.</p>
                                    @endif
                                @elseif(isset($changed) && $changed)
                                    <h1>E-mailadres succesvol gewijzigd</h1>
                                    <p>Je verzoek om je e-mailadres te wijzigen is succesvol verwerkt. Binnen enkele minuten ontvang je een bevestigings mail.</p>
                                    <a href="{{route('dashboard.settings.home')}}" class="btn btn-light col-6 mx-auto">Ga naar Mijn Omgeving</a>
                            
                                @elseif($verified)
                                    <h1>Account succesvol aangemaakt</h1>
                                    <p>Welkom {{ $thuiskoknaam ?? 'Thuiskok' }}, je account is succesvol aangemaakt. Druk op de onderstaande knop om naar je Thuiskok omgeving te gaan. Veel kookplezier!</p>
                                    <a href="{{route('dashboard.settings.home')}}" class="btn btn-light col-6 mx-auto">Ga naar Mijn Omgeving</a>
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
