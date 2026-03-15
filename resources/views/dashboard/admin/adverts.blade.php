@extends('layout.dashboard')

@section('dashboard')
    <div class="page-header neg-header mb-30">
        <div class="container"><h1>Advertenties</h1></div>
    </div>
    <div class="dashboard container">
        <form action="{{route('dashboard.admin.adverts')}}" class="row align-center mb-30">
            @csrf
            <div class="col-3">Zoeken</div>
            <div class="col-3">
                <input type="text" name="search" value="{{$search}}">
            </div>
            <div class="col-3">
                <button type="submit" class="btn btn-light m-0">Zoeken</button>
            </div>
        </form>

        <table class="table table-striped">
            <thead>
                <tr>
                    <td><b>ID Advertentie</b></td>
                    <td><b>Gerechtnaam</b></td>
                </tr>
            </thead>
            <tbody>
                @foreach($adverts as $advert)
                <tr>
                    <td><a href="{{route('dashboard.admin.adverts.single', $advert->getUuid())}}">{{$advert->getParsedAdvertUuid()}}</a></td>
                    <td><a href="{{route('dashboard.admin.adverts.single', $advert->getUuid())}}">{{$advert->dish?->getTitle()}}</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection


