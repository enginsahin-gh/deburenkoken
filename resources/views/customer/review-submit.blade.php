@extends('layout.main')
@section('content')
    <section class="clearfix mt-3">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="center-box">
                        <div class="row">
                            <div class="col-12 text-center">
                                <h1>Review succesvol ingediend</h1>
                                <p class="mt-20">Je review is succesvol ingediend. Bedankt, we waarderen het enorm!</p>
                                <a href="{{route('home')}}" class="btn btn-light col-6 mx-auto">Ga naar startpagina</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection