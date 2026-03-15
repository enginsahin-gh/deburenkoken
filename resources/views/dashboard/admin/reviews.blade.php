@extends('layout.dashboard')

@section('dashboard')
    <div class="page-header neg-header mb-30">
        <div class="container"><h1>Reviews</h1></div>
    </div>
    <div class="dashboard container">
        <form action="{{route('dashboard.admin.reviews')}}" class="row align-center mb-30">
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
                    <td><b>Naam klant</b></td>
                    <td><b>Review op gerecht</b></td>
                    <td><b>Reviewscore</b></td>
                </tr>
            </thead>
            <tbody>
                @foreach($reviews as $review)
                <tr>
                    <td><a href="{{route('dashboard.admin.reviews.single', $review->getUuid())}}">{{$review->client->getName()}}</a></td>
                    <td><a href="{{route('dashboard.admin.reviews.single', $review->getUuid())}}">{{$review?->order?->dish->getUuid()}} {{$review?->order?->dish?->getTitle()}}</a></td>
                    <td><a href="{{route('dashboard.admin.reviews.single', $review->getUuid())}}">{{$review->getRating()}}</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
