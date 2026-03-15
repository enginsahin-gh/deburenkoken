@extends('layout.dashboard')

@section('dashboard')
    <section class="clearfix mt-3">
        <div class="container">
            <div class="row">
                <div class="col-2">
                    <a href="{{route('dashboard.dishes.new')}}" class="btn btn-light">Terug</a>
                </div>
                <div class="col-8 offset-2 text-center">
                    <h1>Gerecht wijzigen?</h1>
                </div>
            </div>
            <div class="row mt-70">
                <div class="col-8 offset-2">
                    <p class="text-center">Weet je zeker dat je {{$dish->getTitle()}} wilt wijzigen, er zijn {{$orderCount}} bestellingen actief? De klanten die een bestelling hebben geplaatst zullen een mail ontvangen. Indien je wilt doorgaan met wijzigen kun je een boodschap plaatsen die via de mail naar je klanten verstuurd zal worden.</p>
                </div>
            </div>
            <form action="{{route('dashboard.dishes.update.confirm', $dish->getUuid())}}" method="POST">
                @csrf
                <div class="d-none">
                    <input type="text" name="requestItems" value="{{json_encode($data)}}">
                </div>
                <div class="row">
                    <div class="col-8 offset-2">
                        <label for="text"></label>
                        <textarea name="editText" id="text"></textarea>
                    </div>
                </div>
                <div class="row mt-50">
                    <div class="col-6 mx-auto">
                        <button class="btn btn-light">Wijzig gerecht</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
