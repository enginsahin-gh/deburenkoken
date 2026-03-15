@extends('layout.dashboard')
@section('dashboard')
    <div class="page-header neg-header mb-30">
        <div class="container">
            <h1>Controle Bankrekening</h1>
        </div>
    </div>
    <div class="dashboard container">
        <form class="row" method="GET" action="{{ route('dashboard.admin.accounts.banking') }}">
            @csrf
            <div class="col-3">Zoeken op gebruikersnaam</div>
            <div class="col-3">
                <input type="text" name="query" value="{{ $query }}">
            </div>
            <div class="col-3">
                <button type="submit" class="btn btn-light m-0">Zoeken</button>
            </div>
        </form>
        <table class="table table-striped w-100">
            <thead>
                <tr>
                    <td><b>Gebruikersnaam</b></td>
                    <td><b>E-mail</b></td>
                    <td><b>Voornaam</b></td>
                    <td><b>Achternaam</b></td>
                    <td><b>Geboortedatum</b></td>
                    <td><b>IBAN</b></td>
                    <td><b>Naam IBAN</b></td>
                </tr>
            </thead>
            <tbody>
                @foreach ($usersWithBanking as $user)
                    <tr>
                        <td>{{ $user['username'] ?? '' }}</td>
                        <td>{{ $user['email'] ?? '' }}</td>
                        <td>{{ $user['first_name'] ?? '' }}</td>
                        <td>{{ $user['last_name'] ?? '' }}</td>
                        <td>{{ $user['birthday'] ?? '' }}</td>
                        <td>
                            <span class="sensitive-value" id="iban-banking-{{ $loop->index }}">{{ $user['iban'] ?? '' }}</span>
                            @if(!empty($user['iban']))
                                <button type="button" class="reveal-btn"
                                    data-user-uuid="{{ $user['user_uuid'] }}"
                                    data-field-type="iban"
                                    data-target="iban-banking-{{ $loop->index }}"
                                    data-masked="{{ $user['iban'] }}"
                                    title="Toon volledig IBAN">
                                    <i class="fas fa-eye"></i>
                                </button>
                            @endif
                        </td>
                        <td>{{ $user['account_holder'] ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @include('partials.sensitive-data-reveal')
@endsection
