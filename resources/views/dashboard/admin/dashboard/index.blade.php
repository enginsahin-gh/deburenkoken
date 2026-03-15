@extends('layout.dashboard')
@section('dashboard')
    <div class="page-header neg-header mb-30">
        <div class="container"><h1>Dashboard</h1></div>
    </div>
    
    <div class="secondnav">
        <a href="{{route('dashboard.admin.dashboard.accounts')}}" class="{{Route::is('dashboard.admin.dashboard.accounts*') ? 'active' : ''}} btn">Accounts</a>
        <a href="{{route('dashboard.admin.dashboard.dishes')}}" class="{{Route::is('dashboard.admin.dashboard.dishes*') ? 'active' : ''}} btn btn-outline-fat">Gerechten</a>
        <a href="{{route('dashboard.admin.dashboard.orders')}}" class="{{Route::is('dashboard.admin.dashboard.orders*') ? 'active' : ''}} btn">Bestellingen</a>
        <a href="{{route('dashboard.admin.dashboard.revenue')}}" class="{{Route::is('dashboard.admin.dashboard.revenue*') ? 'active' : ''}} btn btn-outline-fat">Inkomsten</a>
    </div>
    

    <div class="dashboard container">
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td width="50%">Account aangemaakt op:</td>
                    <td width="50%">{{$created}}</td>
                </tr>
                <tr>
                    <td>Aantal accounts verwijderd:</td>
                    <td>{{$deleted}}</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection


