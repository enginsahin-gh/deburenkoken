@extends('layout.dashboard')

@section('dashboard')
    <div class="page-header neg-header mb-30">
        <div class="container"><h1>Gerechten</h1></div>
    </div>

    <div class="dashboard container">
        <form action="{{route('dashboard.admin.dishes')}}" class="row align-center mb-30">
            @csrf
            <div class="col-3">Zoeken</div>
            <div class="col-3">
                <input type="text" name="search" value="{{$search}}">
            </div>
            <div class="col-3">
                <button type="submit" class="btn btn-light m-0">zoeken</button>
            </div>
        </form>

        <table class="table table-striped">
            <thead>
                <tr>
                    <td><b>ID Gerecht</b></td>
                    <td><b>Gerechtnaam</b></td>
                </tr>
            </thead>
            <tbody>
                @foreach($dishes as $dish)
                <tr>
                    <td><a href="{{route('dashboard.admin.dishes.single', $dish->getUuid())}}">{{$dish->getUuid()}}</a></td>
                    <td><a href="{{route('dashboard.admin.dishes.single', $dish->getUuid())}}">{{$dish->getTitle()}}</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection


