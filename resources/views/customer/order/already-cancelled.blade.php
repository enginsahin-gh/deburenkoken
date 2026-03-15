@extends('layout.main')

@section('content')
<section class="clearfix mt-3">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="center-box">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h1 style="font-size: 36px;">Annulering succesvol</h1>
                            <p class="mt-20">Je bestelling is op een eerder moment succesvol geannuleerd.</p>
                            <a href="{{ route('home') }}" class="btn btn-light col-6 mx-auto">Ga naar startpagina</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.mt-3 {
    margin-top: 1rem;
}

.mt-20 {
    margin-top: 20px;
}

.container {
    width: 100%;
    padding-right: 15px;
    padding-left: 15px;
    margin-right: auto;
    margin-left: auto;
}

.row {
    display: flex;
    flex-wrap: wrap;
    margin-right: -15px;
    margin-left: -15px;
}

.col-12 {
    position: relative;
    width: 100%;
    padding-right: 15px;
    padding-left: 15px;
}

.center-box {
    background: #ffffff;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.text-center {
    text-align: center;
}

h1 {
    color: #333;
    margin-bottom: 20px;
}

.btn {
    display: inline-block;
    font-weight: 400;
    text-align: center;
    vertical-align: middle;
    padding: 10px 20px;
    font-size: 1rem;
    line-height: 1.5;
    border-radius: 4px;
    transition: all 0.15s ease-in-out;
    text-decoration: none;
}

.btn-light {
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    color: #333;
}

.btn-light:hover {
    background-color: #e2e6ea;
    border-color: #dae0e5;
    color: #333;
}

.col-6 {
    flex: 0 0 50%;
    max-width: 50%;
}

.mx-auto {
    margin-left: auto;
    margin-right: auto;
}
</style>
@endsection