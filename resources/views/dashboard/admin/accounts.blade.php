@extends('layout.dashboard')
@section('dashboard')
    <div class="page-header neg-header mb-30">
        <div class="container"><h1>Accounts</h1></div>
    </div>
    <div class="dashboard container">
        <form action="{{ route('dashboard.admin.accounts') }}" class="row align-center mb-30">
            @csrf
            <div class="col-3">Zoeken</div>
            <div class="col-3">
                <input type="text" name="name" value="{{ $search }}">
            </div>
            <div class="col-3">
                <button type="submit" class="btn btn-light m-0">Zoeken</button>
            </div>
        </form>
        <table class="table table-striped">
            <thead>
                <tr>
                    <td class='col-2'><b>ID Profiel</b></td>
                    <td class='col-2'><b>Profielnaam</b></td>
                    <td class='col-2'><b>Aangemaakt op</b></td>
                    <td class='col-2'><b>E-mail verificatie voltooid?</b></td>
                    <td class='col-2'><b>IBAN verificatie voltooid?</b></td>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td class='col-2'><a href="{{ route('dashboard.admin.accounts.single', $user->getUuid()) }}">{{ $user->getUuid() }}</a></td>
                    <td class='col-2'><a href="{{ route('dashboard.admin.accounts.single', $user->getUuid()) }}">{{ $user->getUsername() }}</a></td>
                    <td class='col-2'><a href="{{ route('dashboard.admin.accounts.single', $user->getUuid()) }}">{{ $user->getCreatedAt() }}</a></td>
                    <td class='col-2'>
                        <a href="{{ route('dashboard.admin.accounts.single', $user->getUuid()) }}">
                            {{ $user->getEmailVerifiedAt() ? 'Ja' : 'Nee' }}
                        </a>
                    </td>
                    <td class='col-2'>
                        <a href="{{ route('dashboard.admin.accounts.single', $user->getUuid()) }}">
                            {{ $user->iban_verified ? 'Ja' : 'Nee' }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection