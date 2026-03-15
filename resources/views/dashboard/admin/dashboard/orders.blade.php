@extends('layout.dashboard')
@section('dashboard')
    <div class="page-header neg-header mb-30">
        <div class="container"><h1>Bestellingen</h1></div>
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
                    <td width="50%">antal bestellingen:</td>
                    <td width="50%">{{$orderCount}}</td>
                </tr>
                <tr>
                    <td>Aantal euro's aan bestellinge:</td>
                    <td>{{$orderTotal}}</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection


