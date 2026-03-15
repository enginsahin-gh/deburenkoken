@extends('layout.main')
@section('content')
    <section class="clearfix mt-3">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="center-box">
                        <div class="row">
                            <div class="col-12 text-center">
                                <h1>Vraag is succesvol verstuurd</h1>
                                <p class="mt-20">Je vraag is succesvol ontvangen. Je ontvangt een e-mail ter bevestiging. We streven ernaar om binnen 3 werkdagen te reageren.</p>
                                <a href="{{route('home')}}" class="btn btn-light col-6 mx-auto">Ga naar startpagina</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
