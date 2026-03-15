@extends('layout.dashboard')

@section('dashboard')
<style>
    button.btn, 
    a.btn {
        display: inline-block;
        text-align: center;
        width: 200px; 
        height: 45px; 
        line-height: 45px; 
        padding: 0; 
        font-size: 16px;
        border-radius: 5px;
        margin-top: 0px;
    }

    .btn-outline {
        color: #6c757d;
        background-color: transparent;
        border: 1px solid #6c757d;
        text-decoration: none;
    }

    .btn-light {
        color: #f8f9fa;
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        text-decoration: none;
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
        border: 1px solid #dfdede;
        padding: 30px 15px;
        margin: 30px 0;
    }

    .button-container {
        display: flex;
        justify-content: center;
        gap: 10px; /* Reduced gap for horizontal buttons */
    }

    button[type="submit"] {
        margin: auto;
    }

    .btn-outline {
        line-height: 1.5 !important;
    }

    /* Media query for phones */
    @media (max-width: 768px) {
        .responsive-break {
            display: inline-block;
        }

        .button-container {
            flex-direction: column;
            gap: 8px; /* Slightly smaller gap for vertical buttons */
        }

        .btn, .btn-light {
            width: 100%; /* Make buttons same size on phones */
        }
    }

    @media (min-width: 769px) {
        .responsive-break {
            display: none;
        }
    }
    @media only screen and (max-width: 767px) {
    [type="submit"].btn {
        bottom: 30px;
    }
    a.btn {
    width: 220px;
    margin: 100px 10px 30px;
    }    
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
                                <h1>
                                    Advertentie<span class="responsive-break"><br></span>wijzigen?
                                </h1>
                            </div>
                        </div>
                        <div class="row mt-20">
                            <div class="col-12 text-center">
                                <p>
                                    Weet je zeker dat je<span class="responsive-break"><br></span> 
                                    {{$advert->dish->getTitle()}}<span class="responsive-break"><br></span>
                                    -<span class="responsive-break"><br></span>
                                    {{$advert->getParsedAdvertUuid()}}<span class="responsive-break"><br></span>
                                    wilt wijzigen?
                                </p>
                            </div>
                        </div>
                        <form action="{{route('dashboard.adverts.update.confirm', $advert->getUuid())}}" method="POST">
                            @csrf
                            <div class="d-none">
                                <input type="text" name="requestItems" value="{{json_encode($data)}}">
                            </div>
                            <div class="row mt-50">
                                <div class="col-12">
                                    <div class="button-container">
                                        <a href="{{url()->previous()}}" class="btn btn-light">Terug</a>
                                        <button type="submit" class="btn btn-outline">Wijzig Advertentie</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
