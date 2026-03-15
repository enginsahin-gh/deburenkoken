@extends('layout.main')

@section('content')
<section class="clearfix mt-3">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="center-box">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h1>Verzenden succesvol</h1>

                            <p class="mt-20">Dank voor de medewerking, de DAC7 informatie is succesvol verzonden.</p>
                            
                            @auth
                                <a href="{{ route('dashboard.adverts.active.home') }}" class="btn btn-light" style="background: linear-gradient(to right, #f3723b 0%, #e54750 100%); color: white; border-radius: 6px; border: none; padding: 8px 20px; display: inline-block; margin-top: 15px; text-decoration: none;">Ga naar Dashboard</a>
                            @else
                                <a href="{{ route('login.home') }}" class="btn btn-light" style="background: linear-gradient(to right, #f3723b 0%, #e54750 100%); color: white; border-radius: 6px; border: none; padding: 8px 20px; display: inline-block; margin-top: 15px; text-decoration: none;">Inloggen</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection