<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Aankoopbewijs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo {
            max-width: 200px;
            margin-bottom: 10px;
        }
        .header-text {
            text-align: center;
            margin-top: 5px;
            font-size: 14px;
        }
        .main-content {
            width: 100%;
            margin-bottom: 30px;
        }
        .two-columns {
            display: table;
            width: 100%;
        }
        .left-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .right-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }
        .cook-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .cook-info {
            margin-bottom: 10px;
        }
        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            color: #666;
            margin-bottom: 10px;
        }
        .receipt-details {
            text-align: right;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        .receipt-table th, .receipt-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .receipt-table th {
            background-color: #f8f8f8;
        }
        .contact-info {
            margin: 30px 0;
        }
        .thank-you {
            text-align: center;
            margin-top: 40px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
        }
        .recipient {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path('img/logo.png') }}" class="logo" alt="DeBurenKoken.nl" style="max-width: 200px;">
        </div>
        
        <div class="main-content">
            <div class="two-columns">
                <div class="left-column">
                    <div class="cook-name">{{ $user->getUsername() }}</div>
                    <div class="cook-info">
                        @if(isset($user->cook) && $user->cook)
                            {{ $user->cook->getStreet() }} {{ $user->cook->getHouseNumber() }}{{ $user->cook->getAddition() ? ' '.$user->cook->getAddition() : '' }}<br>
                            {{ $user->cook->getPostalCode() }} {{ $user->cook->getCity() }}<br>
                            {{ $user->userProfile->getPhoneNumber() }}
                        @endif
                    </div>
                </div>
                <div class="right-column">
                    <div class="receipt-title">AANKOOPBEWIJS</div>
                    <div class="receipt-details">
                    <div>AANKOOPBEWIJSNUMMER: {{ str_replace('Bestelnummer: ', '', $order->getParsedOrderUuid()) }}</div>
                        <div>AANKOOPDATUM: {{ $order->getCreatedAt()->format('d-m-Y') }}</div>
                        <div>LEVERDATUM: {{ $order->getExpectedPickupTime()->format('d-m-Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="recipient">
            <strong>AAN:</strong><br>
            {{ $order->client->getName() }}
        </div>

        <table class="receipt-table">
            <thead>
                <tr>
                    <th>HOEVEELHEID</th>
                    <th>BESCHRIJVING</th>
                    <th>PRIJS PER EENHEID</th>
                    <th>TOTAAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $order->getPortionAmount() }}</td>
                    <td>{{ $order->dish->getTitle() }}</td>
                    <td>€ {{ number_format($order->advert->getPortionPrice(), 2, ',', '.') }}</td>
                    <td>€ {{ number_format($order->advert->getPortionPrice() * $order->getPortionAmount(), 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="contact-info">
            Indien je vragen hebt over het aankoopbewijs, kun je contact opnemen met {{ $user->getUsername() }}, 
            {{ $user->userProfile->getPhoneNumber() }}, {{ $user->getEmail() }}.
        </div>

        <div class="thank-you">BEDANKT VOOR JE AANKOOP!</div>

        <div class="footer">
            <img src="{{ public_path('img/logo.png') }}" class="logo" alt="DeBurenKoken.nl" style="max-width: 200px;">
        </div>
    </div>
</body>
</html>
