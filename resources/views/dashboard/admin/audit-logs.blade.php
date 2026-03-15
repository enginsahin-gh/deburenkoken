@extends('layout.dashboard')
@section('dashboard')
    <div class="page-header neg-header mb-30">
        <div class="container"><h1>Inzageregistratie</h1></div>
    </div>
    <div class="dashboard container">
        <form action="{{ route('dashboard.admin.audit-logs') }}" method="GET" class="row align-center mb-30">
            <div class="col-3">Zoeken op gebruikersnaam<br><small>(beheerder of betroffen gebruiker)</small></div>
            <div class="col-3">
                <input type="text" name="search" value="{{ $search }}">
            </div>
            <div class="col-3">
                <button type="submit" class="btn btn-light m-0">Zoeken</button>
            </div>
        </form>
        <table class="table table-striped">
            <thead>
                <tr>
                    <td class='col-2'><b>Datum & tijd</b><br><small style="font-weight:normal;">↓ nieuwste eerst</small></td>
                    <td class='col-2'><b>Beheerder</b></td>
                    <td class='col-2'><b>Betroffen gebruiker</b></td>
                    <td class='col-2'><b>Type gegeven</b></td>
                    <td class='col-2'><b>IP-adres</b></td>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td class='col-2'>{{ $log->created_at->format('d-m-Y H:i') }}</td>
                    <td class='col-2'>{{ $log->admin?->username }}</td>
                    <td class='col-2'>{{ $log->targetUser?->username }}</td>
                    <td class='col-2'>{{ $log->field_type }}</td>
                    <td class='col-2'>{{ $log->ip_address }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $logs->appends(request()->query())->links() }}
    </div>
@endsection
