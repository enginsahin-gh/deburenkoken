@extends('layout.dashboard')

@section('dashboard')
    <section class="clearfix mt-3">
        <div class="container">
            <div class="row">
                <div class="col-8 text-center mx-auto">
                    <h1>Gerecht verwijderen succesvol</h1>
                </div>
            </div>
            <div class="row mt-70">
                <div class="col-8 mx-auto">
                    <p class="text-center">{{$dish->getTitle()}} is succesvol verwijderd. Jij en je klanten die een actieve bestelling hadden op dit gerecht ontvangen een mail ter bevestiging.</p>
                </div>
            </div>
            <div class="row mt-50">
                <div class="col-6 mx-auto text-center">
                    <a href="{{route('dashboard.dishes.new')}}" class="btn btn-light">Naar mijn omgeving</a>
                </div>
            </div>
        </div>
    </section>
@endsection
