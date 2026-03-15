@extends('layout.dashboard')
@section('dashboard')
    <div class="page-header neg-header mb-30">
        <div class="container"><h1>Gerechten</h1></div>
    </div>

    <div class="secondnav mb-5">
        <a href="{{route('dashboard.admin.dashboard.accounts')}}" class="{{Route::is('dashboard.admin.dashboard.accounts*') ? 'active' : ''}} btn">Accounts</a>
        <a href="{{route('dashboard.admin.dashboard.dishes')}}" class="{{Route::is('dashboard.admin.dashboard.dishes*') ? 'active' : ''}} btn btn-outline-fat">Gerechten</a>
        <a href="{{route('dashboard.admin.dashboard.orders')}}" class="{{Route::is('dashboard.admin.dashboard.orders*') ? 'active' : ''}} btn">Bestellingen</a>
        <a href="{{route('dashboard.admin.dashboard.revenue')}}" class="{{Route::is('dashboard.admin.dashboard.revenue*') ? 'active' : ''}} btn btn-outline-fat">Inkomsten</a>
    </div>
    <div class="dashboard container">
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td width="50%">Aantal gerechten:</td>
                    <td width="50%">{{$dishes}}</td>
                </tr>
                <tr>
                    <td>Aantal advertenties:</td>
                    <td>{{$adverts}}</td>
                </tr>
                <tr>
                    <td>Aantal advertenties online:</td>
                    <td>{{$advertsOnline}}</td>
                </tr>
                <tr>
                    <td>Aantal porties verkocht:</td>
                    <td>{{$orderCount}}</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection


