<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overzicht Bestellingen</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Overzicht Bestellingen - Verlopen Advertentie</h2>
    <p>Beste {{ $user->name }},</p>
    
    <p>Hieronder vindt u een overzicht van de bestellingen die behoren tot uw advertentie:</p>
    
    <table>
        <thead>
            <tr>
                <th>Bestelnummer</th>
                <th>Geplande Ophaaltijd</th>
                <th>Portie Aantal</th>
                <th>Opmerkingen</th>
                <th>Betaling Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
            <tr>
                <td>{{ $order->getParsedOrderUuid() }}</td>
                <td>{{ $order->getExpectedPickupTime()->format('d-m-Y H:i') }}</td>
                <td>{{ $order->getPortionAmount() }}</td>
                <td>{{ $order->getRemarks() ?: '-' }}</td>
                <td>{{ $order->getPaymentState() === \App\Models\Order::IN_PROCESS ? 'In behandeling' : ($order->getPaymentState() === \App\Models\Order::SUCCEED ? 'Betaald' : 'Mislukt') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <p>Bedankt,</p>
    <p>Uw Team</p>
</body>
</html>
