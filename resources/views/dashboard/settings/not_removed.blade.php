@extends('layout.dashboard')

@section('dashboard')
    <style>
        .main {
            margin-left: 0 !important;
        }
    </style>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-8">
                <div class="row">
                    <div class="col-12 text-center">
                        <h1>Account verwijderen</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-center">
                        <p>Uw account kan niet verwijderd worden omdat u nog actieve advertenties heeft.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 justify-content-center text-center">
                        <a href="{{route('dashboard.adverts.active.home')}}" class="btn btn-light btn-small">Mijn omgeving</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
