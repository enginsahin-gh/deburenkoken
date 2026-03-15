@extends('layout.main')
@section('content')
    <section class="clearfix mt-3">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="center-box">
                        <div class="row">
                            <div class="col-12 text-center">
                                <h1>Betaling succesvol</h1>
                                <p class="mt-20">Je betaling is succesvol ontvangen. Je krijgt een bevestigingsmail met daarin informatie over het afhalen.</p>
                                <p>Heb je geen e-mail ontvangen? Controleer dan ook je spamfolder. Eet smakelijk alvast!</p>
                                <a href="{{route('search.cooks.detail', $cookUuid)}}?{{$searchString}}" class="btn btn-light col-6 mx-auto">Ga naar je Thuiskok</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


