@extends('layout.dashboard')

@section('dashboard')
    <style>
        .main {
            margin-left: 0 !important;
        }
    </style>
    <div class="ltn__about-us-area pt-20 pb-20">
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
                                <h1 class="section-title">Account verwijderen</h1>
                                <p>Weet je zeker dat je je account wilt verwijderen? Indien je nog saldo in je portemonnee hebt, betaal dit dan eerst uit op je betaalrekening. <br>Let op: Indien je nog advertenties online hebt staan, kun je je account niet verwijderen.</p>
                                <div class="col-12 p-0">
                                    <form action="{{route('dashboard.settings.profile.delete')}}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="row" id='row-inline'>
                                            <div class='col'>
                                                <a href="{{ route('dashboard.settings.home') }}" class="btn btn-small btn-light ">Annuleren</a>
                                            </div>
                                            <div class='col'>
                                                <button class="btn btn-small btn-outline">Account verwijderen</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-1"></div>
            </div>             
        </div>
    </div>
@endsection
