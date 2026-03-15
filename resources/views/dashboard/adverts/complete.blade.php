@extends('layout.dashboard')

@section('dashboard')
    <section class="clearfix mt-3">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="center-box">
                        <div class="row">
                            <div class="col-12 text-center">
                                <h1>Advertentie annuleren succesvol</h1>
                                <p class="mt-20">{{$advert->dish->getTitle()}} - {{$advert->getParsedAdvertUuid()}} is succesvol geannuleerd.</p>
                                <a href="{{route('dashboard.adverts.active.home')}}" class="btn btn-light col-6 mx-auto">Naar mijn omgeving</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
