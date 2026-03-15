@extends('layout.dashboard')

@section('dashboard')
    <div class="page-header neg-header mb-30">
        <div class="container"><h1>Settings</h1></div>
    </div>
    <div class="dashboard container">
        <p>@if($websiteStatus && !$websiteStatus->is_online)  De website is nu offline.  @else De website is nu online.  @endif</p>
        <a href="{{ route('dashboard.admin.website.status') }}" class="btn btn-light"> 
            @if($websiteStatus && !$websiteStatus->is_online) 
                Website online zetten
            @else 
                Website offline zetten
            @endif
        </a>
    </div>
@endsection
