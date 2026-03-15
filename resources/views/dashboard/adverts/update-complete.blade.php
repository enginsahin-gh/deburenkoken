@extends('layout.dashboard')

@section('dashboard')
<style>
    button.btn {
        display: inline-block;
        text-align: center;
    }
    a.btn-light,
    button.btn-light {
        color: #f8f9fa;
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        padding: 10px 15px;
        text-decoration: none;
        display: inline-block;
        border-radius: 5px;
    }
    .border-box {
        padding: 30px;
        border-radius: 8px;
        background-color: #fff;
    }
    .center-box {
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
    }
</style>

<section class="clearfix mt-3">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="center-box">
                    <div class="border-box">
                        <div class="row">
                            <div class="col-12 text-center">
                                <h1>Advertentie wijzigen succesvol</h1>
                            </div>
                        </div>
                        <div class="row mt-20">
                            <div class="col-12 text-center">
                                <p>{{$advert->dish->getTitle()}} - {{$advert->getParsedAdvertUuid()}} is succesvol gewijzigd.</p>
                            </div>
                        </div>
                        <div class="row mt-50">
                            <div class="col-6 mx-auto text-center">
                                <a href="{{route('dashboard.adverts.active.home')}}" class="btn btn-light col-12 mx-auto">Naar mijn omgeving</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
