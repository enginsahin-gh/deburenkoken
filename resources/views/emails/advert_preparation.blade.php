<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>Samenvatting bestelling advertentie {{ substr($advert->getUuid(), -6) }}, {{ $advert->dish->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style type="text/css">
        /* Reset en basis styling */
        /* Dit stijlt de buitenste wrapper */
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            background: #f5f7fa !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        /* Outlook fixes */
        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            outline: none;
            text-decoration: none;
        }
        
        /* Grotere specificiteit voor desktop - AANGEPAST NAAR 120% */
        html body .main-container {
            width: 80% !important;
            max-width: 640px !important;
        }
        
        @media only screen and (min-width: 641px) {
            body .main-container,
            body table.main-container,
            html body table.main-container {
                width: 80% !important;
                max-width: 640px !important;
            }
            
            body .container-td {
                width: 640px !important;
                max-width: 640px !important;
            }
            
            body .content-wrapper {
                width: 100% !important;
                max-width: 640px !important;
                padding: 30px !important;
                box-sizing: border-box !important;
            }
            
            body .order-section {
                width: 100% !important;
                max-width: 580px !important; /* 960px - 30px padding on each side */
                padding: 20px !important;
            }
            
            /* Column widths */
            body .orders-table .name-column {
                width: 25% !important;
            }
            
            body .orders-table .portions-column {
                width: 10% !important;
            }
            
            body .orders-table .time-column {
                width: 10% !important;
            }
            
            body .orders-table .remarks-column {
                width: 55% !important;
            }
        }
        
        body { 
            margin: 0; 
            padding: 0; 
            background: #f5f7fa;
            font-family: 'Open Sans', sans-serif;
            font-size: 15px !important;
            line-height: 1.6;
            color: #2d3748 !important;
        }
        
        .order-section {
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
            margin: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: relative;
            width: 100%;
            box-sizing: border-box;
        }
        
        .section-title {
            color: #ff6b00 !important;
            font-weight: 600;
            font-size: 16px;
            margin-top: 30px;
            margin-bottom: 15px;
            border-bottom: 2px solid #ff6b00;
            padding-bottom: 8px;
            text-align: left;
            width: 100%;
            box-sizing: border-box;
        }

        .order-info {
            position: relative;
            width: 100%;
            overflow-x: auto; /* Enable horizontal scrolling on small screens */
        }
        
        .logo {
            max-width: 200px;
            height: auto;
            margin: 30px 0;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        
        .content-wrapper {
            padding: 30px;
            max-width: 100%;
            margin: 0 auto;
            box-sizing: border-box;
        }
        
        .greeting {
            font-size: 15px;
            margin-bottom: 15px;
            color: #2d3748 !important;
            text-align: left;
            font-family: 'Open Sans', sans-serif;
        }
        
        .signature {
            margin-top: 20px;
            padding-top: 20px;
            color: #2d3748 !important;
            text-align: left;
            font-family: 'Open Sans', sans-serif;
            font-size: 15px;
            line-height: 1.6;
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-sizing: border-box;
            border: none;
        }
        
        .orders-table th,
        .orders-table td {
            padding: 12px;
            text-align: left;
            border: none;
            border-bottom: 1px solid #e2e8f0;
            word-wrap: break-word;
        }

        .orders-table th {
            background-color: #f8fafc;
            font-weight: 600;
            color: #4a5568;
        }

        .orders-table .portions-column {
            width: 15%;
        }
        
        .orders-table .name-column {
            width: 25%;
        }

        .orders-table .time-column {
            width: 20%;
        }

        .orders-table .remarks-column {
            width: 40%;
        }
        
        .time-header {
            line-height: 1.2;
        }
        
        /* Warning message styling - exact styling from cancel.blade.php */
        .warning-message {
            background-color: #fff3cd; 
            border: 1px solid #ffeeba; 
            border-radius: 4px; 
            color: #856404; 
            padding: 10px; 
            margin-top: 15px; 
            margin-bottom: 15px;
        }
        
        @media only screen and (max-width: 640px) {
            .main-container {
                width: 100% !important;
            }
            
            .content-wrapper {
                padding: 15px;
            }

            .orders-table {
                font-size: 14px;
            }

            .orders-table th,
            .orders-table td {
                padding: 8px 6px;
            }
            
            .greeting, .signature {
                font-size: 14px;
            }
            
            /* Stack table headers for better mobile viewing */
            .orders-table .portions-column {
                width: 20%;
            }
            
            .orders-table .name-column {
                width: 30%;
            }

            .orders-table .time-column {
                width: 20%;
            }

            .orders-table .remarks-column {
                width: 30%;
            }
        }
        
        @media only screen and (max-width: 480px) {
            .content-wrapper {
                padding: 10px;
            }
            
            .order-section {
                padding: 15px;
            }
            
            .orders-table {
                font-size: 13px;
            }
            
            .orders-table th,
            .orders-table td {
                padding: 6px 4px;
            }
            
            .logo {
                margin: 20px 0;
                max-width: 160px;
            }
        }
    </style>
    
    <!--[if mso]>
    <style type="text/css">
        body, table, td, p, a, span {
            font-family: Arial, Helvetica, sans-serif !important;
        }
        /* EMAIL CONTAINER RONDE HOEKEN */
        .email-container {
            border-radius: 8px !important;
            mso-border-radius-alt: 8px !important;
        }
        table.email-container {
            border-radius: 8px !important;
            mso-border-radius-alt: 8px !important;
        }
        .order-section {
            border-radius: 8px !important;
            mso-border-radius-alt: 8px !important;
        }
        .warning-message {
            border-radius: 4px !important;
            mso-border-radius-alt: 4px !important;
        }
        /* Table styling for Outlook */
        .orders-table {
            border-collapse: collapse !important;
        }
        .orders-table th,
        .orders-table td {
            border-bottom: 1px solid #e2e8f0 !important;
        }
    </style>
    <![endif]-->
</head>
<body>
    <!--[if mso]>
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td style="background-color: #f5f7fa;" align="center">
                <div style="border-radius: 8px; mso-border-radius-alt: 8px; background: #ffffff; padding: 30px; max-width: 640px; margin: 0 auto;">
    <![endif]-->

    <!-- Aangepast naar 120% (3x 40%) -->
    <table class="main-container" width="80%" style="width:80% !important; max-width:640px !important;" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#f5f7fa">
        <tr>
            <td class="container-td" align="center" valign="top" style="width:100% !important; max-width:960px !important;">
                <!--[if mso]>
                <table width="640" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td>
                <![endif]-->
                
                <div class="content-wrapper" style="width:100% !important; max-width:640px !important;">
                    <div class="order-section" style="width:100% !important;">
                        <div style="text-align:center;">
                            <a href="{{ config('app.url') }}">
                                <!--[if mso]>
                                <img src="{{ config('app.url') }}/img/logo.png" alt="{{ config('app.name') }}" width="200" style="max-width: 200px; width: 100%; height: auto; display: block; border: 0;">
                                <![endif]-->
                                <!--[if !mso]><!-->
                                <img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }}" class="logo" style="max-width: 200px; height: auto; margin: 30px 0; display: block; margin-left: auto; margin-right: auto; border: 0; outline: none;"/>
                                <!--<![endif]-->
                            </a>
                        </div>
                        
                        <div class="greeting">
                            Hallo {{ $cook->user->username }},<br><br>
                            Het uiterlijke bestelmoment is verlopen van advertentie {{ $shortUuid }}, 
                            {{ $advert->dish->title }}. Hierbij ontvang je een samenvatting van de bestellingen. De bestellingen zullen afgehaald worden op 
                            {{ $advert->getParsedPickupFrom()->translatedFormat('l j F') }}, {{ substr($advert->getPickupFrom(), 0, 5) }} - {{ substr($advert->getPickupTo(), 0, 5) }}.
                        </div>

                        <!-- Hier is de nieuwe waarschuwing die je toevoegt -->
                        @if($hasOrdersInProcess)
                        <div style="background-color: #fff3cd; border: 1px solid #ffeeba; border-radius: 4px; color: #856404; padding: 10px; margin-top: 15px; margin-bottom: 15px;">
                            <strong>Let op:</strong> Op dit moment loopt er nog een betaling. Bekijk over 15 minuten je Dashboard om de definitieve aantal bestellingen te zien.
                        </div>
                        @endif

                        <div class="section-title">Bestelgegevens</div>
                        <div class="order-info">
                            @if($activeOrders->count() > 0)
                                <table class="orders-table">
                                    <thead>
                                        <tr>
                                            <th class="name-column">Klantnaam</th>
                                            <th class="portions-column">Aantal</th>
                                            <th class="time-column">Verwacht</th>
                                            <th class="remarks-column">Opmerking</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activeOrders as $order)
                                            <tr>
                                                <td>{{ $order->client->name }}</td>
                                                <td>{{ $order->portion_amount }}</td>
                                                <td>{{ \Carbon\Carbon::parse($order->expected_pickup_time)->format('H:i') }}</td>
                                                <td>{{ $order->remarks }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p>Er zijn geen actieve bestellingen aanwezig.</p>
                            @endif
                        </div>

                        <div class="signature">
                            Met vriendelijke groet,<br>
                            Het team van {{ config('app.name') }}
                        </div>
                    </div>
                </div>
                
                <!--[if mso]>
                        </td>
                    </tr>
                </table>
                <![endif]-->
            </td>
        </tr>
    </table>

    <!--[if mso]>
                </div>
            </td>
        </tr>
    </table>
    <![endif]-->
</body>
</html>