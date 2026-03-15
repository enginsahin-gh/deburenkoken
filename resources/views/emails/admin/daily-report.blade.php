<!DOCTYPE html>
<html>
<head>
    <style>
        body { 
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .section {
            margin: 20px 0;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .no-data {
            font-style: italic;
            color: #666;
        }
    </style>
</head>
<body>
    <h2>Dagelijks Beheerrapport - {{ $date }}</h2>

    <div class="section">
        <div class="section-title">Uitbetalingsaanvragen met de status 'Uit te betalen'</div>
        @if($pendingPayouts->isEmpty())
            <p class="no-data">Er zijn geen uitbetalingsaanvragen met de status 'uit te betalen'.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Uitbetalingsnummer</th>
                        <th>Datum aanvraag</th>
                        <th>Thuiskoknaam</th>
                        <th>Uit te betalen bedrag</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($pendingPayouts as $payout)
                    <tr>
                        <td>{{ substr($payout->uuid, -6) }}</td>
                        <td>{{ $payout->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $payout->user->username }}</td>
                        <td>€{{ number_format($payout->amount, 2, ',', '.') }}</td>
                        <td>
                            @if($payout->state == 1)
                                In behandeling
                            @elseif($payout->state == 2)
                                Uitbetaald
                            @else
                                Onbekend
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Nieuwe Accounts ({{ $date }})</div>
        @if($newAccounts->isEmpty())
            <p class="no-data">Er zijn vandaag geen nieuwe accounts aangemaakt.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Thuiskoknaam</th>
                        <th>E-mailverificatie voltooid?</th>
                        <th>IBAN verificatie voltooid?</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($newAccounts as $account)
                    <tr>
                        <td>{{ $account->username }}</td>
                        <td>{{ $account->email_verified_at ? 'Ja' : 'Nee' }}</td>
                        <td>
                            @if($account->banking && !empty($account->banking->iban))
                                Ja
                            @else
                                Nee
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>