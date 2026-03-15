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
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header-logo {
            text-align: center !important;
            margin-bottom: 15px !important;
        }
        .header-logo img {
            max-width: 200px;
            height: auto;
            display: inline-block;
        }
        /* New approach for header row */
        .header-row {
            width: 100%;
            display: table !important;
            margin-bottom: 20px !important; 
        }
        .header-cell {
            display: table-cell !important;
            vertical-align: middle !important;
            height: 30px !important;
        }
        .company-name {
            font-weight: bold;
            font-size: 16px;
            text-align: left;
        }
        .factuur-title {
            font-size: 24px;
            font-weight: bold;
            text-align: right;
        }
        .header-info {
            margin-bottom: 20px !important;
            margin-top: 0 !important;
        }
        .company-info {
            float: left;
            padding-top: 12px !important;
        }
        .invoice-info {
            float: right;
            text-align: right;
        }
        .client-info {
            clear: both;
            margin-top: 80px !important; /* Increased spacing before client info */
            margin-bottom: 15px !important;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
        }
        th {
            text-align: center;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
        }
        .thank-you {
            display: block;
            width: 200px;
            margin: 0 auto;
            text-align: center;
            box-sizing: border-box;
            padding: 0;
            position: relative;
            left: 0;
        }
        .footer-logo {
            text-align: center;
            margin-top: 20px;
        }
        .footer-logo img {
            max-width: 200px; 
            height: auto;
            margin: 0 auto;
            display: block;
        }
        .logo {
            max-width: 200px; 
            height: auto;
        }
        .footer img {
            max-width: 200px; 
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Logo bovenaan -->
        <div class="header-logo">
            <img src="{{ public_path('img/logo.png') }}" alt="DeBurenKoken.nl">
        </div>

        <!-- New table-based header approach -->
        <div class="header-row">
            <div class="header-cell company-name">DeBurenKoken.nl</div>
            <div class="header-cell factuur-title">FACTUUR</div>
        </div>

        <div class="header-info">
            <div class="company-info">
                DeBurenKoken<br>
                NL864719334B01
            </div>
            <div class="invoice-info">
                <!-- Toon alleen laatste 6 karakters van UUID -->
                FACTUURNUMMER: {{ substr($payment->getUuid(), -6) }}<br>
                FACTUURDATUM: {{ $payment->getCreatedAt()->format('d-m-Y') }}<br>
                LEVERDATUM: {{ $payment->getPaymentDate()->format('d-m-Y') }}
            </div>
        </div>

        <div class="client-info">
            <strong>AAN:</strong><br>
            {{ $user->getUsername() }}<br>
            @if($user->type_thuiskok === 'Zakelijke Thuiskok' && $user->kvk_naam)
                {{ $user->kvk_naam }}<br>
            @endif
        </div>

        <table style="border-collapse: collapse; width: 100%;">
            <tr>
                <th style="width: 70%; border: 1px solid #000; text-align: center;">BESCHRIJVING</th>
                <th style="width: 30%; border: 1px solid #000; text-align: center;">TOTAAL</th>
            </tr>
            <tr>
                <td style="border: 1px solid #000; text-align: center;">Uitbetaling totaalbedrag portemonnee DeBurenKoken.nl</td>
                <td style="border: 1px solid #000; text-align: right;">€ {{ number_format($payment->getAmount(), 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="border: none; text-align: left; padding: 8px 0;">Bijdrage DeBurenKoken</td>
                <td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; border-top: none; text-align: right; padding: 8px;">€ {{ number_format($payment->getFeeAmount(), 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="border: none; text-align: left; padding: 8px 0;">DeBurenKoken bijdrage is inclusief 21% BTW</td>
                <td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; border-top: none; text-align: right; padding: 8px;">€ {{ number_format($payment->getFeeAmount() * 0.21, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="border: none; text-align: left; padding: 8px 0;"><strong>Totaal uitbetaald op rekening</strong></td>
                <td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; border-top: none; text-align: right; padding: 8px;"><strong>€ {{ number_format($payment->getPayoutAmount(), 2, ',', '.') }}</strong></td>
            </tr>
        </table>

        <div class="footer">
            <div class="thank-you" style="text-align: center;"><strong style="margin: 0; padding: 0; display: inline-block;">Bedankt voor het vertrouwen!</strong></div>
        </div>
    </div>
    <div class="footer-logo">
        <img src="{{ public_path('img/logo.png') }}" class="logo" alt="DeBurenKoken.nl">
    </div>
</body>
</html>