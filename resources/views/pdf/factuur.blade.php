<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Factuur</title>
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
            margin-bottom: 40px;
        }
        .logo {
            max-width: 200px; 
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            vertical-align: top;
        }
        .cook-info {
            width: 50%;
            text-align: left;
        }
        /* Change 1: Increase font size for cook name */
        .cook-name {
            font-size: 18px;
            font-weight: bold;
        }
        .factuur-info {
            width: 50%;
            text-align: right;
        }
        .factuur-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        /* Change 2: Add more space above the client section */
        .client-section {
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .invoice-table th, .invoice-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .invoice-table th {
            background-color: #f8f8f8;
            font-weight: bold;
            text-align: center;
        }
        /* Specifieke stijl voor BTW en TOTAAL rijen */
        .invoice-table .subtotal-row td {
            border-left: none;
            border-right: none;
            border-bottom: none;
            text-align: right;
        }
        .invoice-table .subtotal-row td:last-child {
            border: 1px solid #ddd;
            width: 15%;
        }
        .contact-info {
            margin: 30px 0;
            text-align: center;
        }
        .thank-you {
            text-align: center;
            margin: 40px 0;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
        }
        .footer img {
            max-width: 200px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Logo -->
        <div class="header">
            <img src="{{ public_path('img/logo.png') }}" alt="DeBurenKoken.nl" class="logo">
        </div>

        <!-- Header Section -->
        <table class="info-table">
            <tr>
                <td class="cook-info">
                    <!-- Change 1: Added cook-name class for bigger font -->
                    <div class="cook-name">{{ $order->advert->cook->user->getUsername() ?? '<THUISKOKNAAM>' }}</div>
                    {{ $order->advert->cook->getStreet() ?? '<STRAATNAAM THUISKOK>' }} {{ $order->advert->cook->getHouseNumber() ?? '' }} {{ $order->advert->cook->getAddition() ?? '' }}<br>
                    {{ $order->advert->cook->getPostalCode() ?? '<POSTCODE>' }} {{ $order->advert->cook->getCity() ?? '<STAD THUISKOK>' }}<br>
                    {{ $order->advert->cook->user->userProfile ? $order->advert->cook->user->userProfile->getPhoneNumber() : '<TELEFOON THUISKOK>' }}<br>
                    @if($order->advert->cook->user->type_thuiskok === 'Zakelijke Thuiskok')
                        @if($order->advert->cook->user->kvk_naam)
                            {{ $order->advert->cook->user->kvk_naam }}<br>
                        @endif
                        @if($order->advert->cook->user->btw_nummer)
                            {{ $order->advert->cook->user->btw_nummer }}<br>
                        @endif
                    @endif
                </td>
                <td class="factuur-info">
                    <div class="factuur-title">FACTUUR</div>
                    <!-- Change 3: Use order UUID without prefix for invoice number -->
                    FACTUURNUMMER: {{ substr($order->getUuid(), -6) ?? '000000' }}<br>
                    <!-- Change 4: Standardize date format -->
                    FACTUURDATUM {{ $order->created_at ? $order->created_at->format('d-m-Y') : 'DD-MM-YYYY' }}<br>
                    LEVERDATUM: {{ $order->advert->pickup_date ? \Carbon\Carbon::parse($order->advert->pickup_date)->format('d-m-Y') : 'DD-MM-YYYY' }}
                </td>
            </tr>
        </table>

        <!-- Client Information with extra space above -->
        <div class="client-section">
            <strong>AAN:</strong><br>
            {{ $order->client->getName() ?? '<NAAM KLANT>' }}
        </div>

        <!-- Complete Invoice Table including BTW and TOTAAL with proper price calculations -->
        <table class="invoice-table">
            <tr>
                <th style="width: 15%;">HOEVEELHEID</th>
                <th style="width: 55%;">BESCHRIJVING</th>
                <th style="width: 15%;">PRIJS PER EENHEID</th>
                <th style="width: 15%;">TOTAAL</th>
            </tr>
            <tr>
                <td style="text-align: center;">{{ $order->getPortionAmount() ?? '<AANTALPORTIES>' }}</td>
                <td>{{ $order->advert->dish->getTitle() ?? '<GERECHTNAAM>' }}</td>
                <!-- Change 5: Correct price calculations -->
                @php
                    $portionPrice = $order->advert->getPortionPrice() ?? 0;
                    $portionAmount = $order->getPortionAmount() ?? 0;
                    
                    // The price excluding VAT (base price)
                    $priceExVat = round($portionPrice / 1.09, 2);
                    
                    // Total without VAT
                    $totalExVat = $priceExVat * $portionAmount;
                    
                    // VAT amount
                    $vatAmount = round($totalExVat * 0.09, 2);
                    
                    // Total with VAT
                    $totalWithVat = $totalExVat + $vatAmount;
                @endphp
                <td style="text-align: right;">€{{ number_format($priceExVat, 2, ',', '.') }}</td>
                <td style="text-align: right;">€{{ number_format($totalExVat, 2, ',', '.') }}</td>
            </tr>
            <!-- BTW Row als onderdeel van de tabel -->
            <tr class="subtotal-row">
                <td colspan="3" style="text-align: right; border-top: none;">BTW 9%:</td>
                <td style="text-align: right;">€{{ number_format($vatAmount, 2, ',', '.') }}</td>
            </tr>
            <!-- TOTAAL Row als onderdeel van de tabel -->
            <tr class="subtotal-row">
                <td colspan="3" style="text-align: right; border-top: none;"><strong>TOTAAL:</strong></td>
                <td style="text-align: right;"><strong>€{{ number_format($totalWithVat, 2, ',', '.') }}</strong></td>
            </tr>
        </table>

        <!-- Contact Information -->
        <div class="contact-info">
            Indien je vragen hebt over deze factuur, kun je contact opnemen met {{ $order->advert->cook->user->getUsername() ?? '<THUISKOKNAAM>' }}, 
            {{ $order->advert->cook->user->userProfile ? $order->advert->cook->user->userProfile->getPhoneNumber() : '<TELEFOONNUMMER THUISKOK>' }}, 
            {{ $order->advert->cook->user->getEmail() ?? '<MAIL THUISKOK>' }}.
        </div>

        <!-- Thank You Message -->
        <div class="thank-you">
            BEDANKT VOOR JE AANKOOP!
        </div>

        <!-- Footer -->
        <div class="footer">
            <img src="{{ public_path('img/logo.png') }}" alt="DeBurenKoken.nl" class="logo">
        </div>
    </div>
</body>
</html>