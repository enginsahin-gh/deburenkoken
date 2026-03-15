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
                                <h1 class="section-title">Website status</h1>
                                <p>Weet je zeker dat je de             
                                    @if($websiteStatus && !$websiteStatus->is_online) 
                                    website online wil zetten?
                                    @else 
                                    website offline wil zetten? 
                                    @endif
                                </p>
                                <div class="col-12 p-0">
                                    <form action="{{ $websiteStatus && !$websiteStatus->is_online ? route('dashboard.admin.update.website.status.online') : route('dashboard.admin.update.website.status.offline') }}" method="POST">
                                        @csrf
                                        @method('POST')
                                        <div class="row" id='row-inline'>
                                            <div class='col'>
                                                <a href="{{ route('dashboard.admin.accounts') }}" class="btn btn-small btn-light ">Annuleren</a>
                                            </div>
                                            <div class='col'>
                                                <button class="btn btn-small btn-outline">
                                                    @if($websiteStatus && !$websiteStatus->is_online) 
                                                    Website online zetten
                                                    @else 
                                                    Website offline zetten
                                                    @endif
                                                </button>
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
