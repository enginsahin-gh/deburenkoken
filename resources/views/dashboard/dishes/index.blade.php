@extends('layout.dashboard')

@section('title', 'Gerechten')
@section('page.style')
    <style>
        .pagination {
            color: #000000;
        }
        .pagination .page-link {
            color:#000000;
            border: none;
        }

        .pagination .page-item.active .page-link {
            background-color: #fff;
            border:none;
            color:#000000;
        }

        @media (max-width: 768px) {
            .tbl-title {
                display: none;
            }
        }
    </style>
@endsection
@section('dashboard')
    <div class="container">
        <div class="row mt-10">
            @if (!$old && $canCreateDish)
                <a href="{{route('dashboard.dishes.create')}}" class="dashboard-button">
                    <h4>Nieuw gerecht</h4>
                    <span class="add-sign">+</span>
                </a>
            @elseif (!$old && !$canCreateDish)
                {{-- FIX PUNT 2: Toon duidelijke warning bij limiet bereikt --}}
                <div class="alert alert-warning">
                    <strong>Je hebt het maximale aantal gerechten (25) voor vandaag bereikt. Probeer het morgen opnieuw.</strong>
                </div>
            @endif
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="tbl-title">Naam gerecht</div>
        </div>
        @foreach($dishes as $dish)
            <div class="row table-single-row">
                <div class="col-12">
                    <a href="{{route('dashboard.dishes.show', $dish->getUuid())}}" class="underline" style='line-height: 57px;'>{{$dish->getTitle()}} </a>                
                    
                    <a href="{{ route('dashboard.adverts.createWithDish', ['uuid' => $dish->getUuid()]) }}" class="dashboard-button nie-advtBtn">
                        <h4>Nieuwe advertentie</h4>
                        <span class="add-sign">+</span>
                    </a>
                </div>
            </div>
        @endforeach
        {{$dishes->links()}}
    </div>
@endsection