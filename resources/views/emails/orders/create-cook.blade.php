<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>Orderbevestiging {{env('APP_NAME')}}</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    
    <style type="text/css">
        /* Reset en basis styling */
        body { 
            margin: 0; 
            padding: 0; 
            background: #f5f7fa;
            font-family: 'Open Sans', sans-serif;
            font-size: 15px;
            line-height: 1.6;
            color: #2d3748;
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
        
        .order-section {
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: relative;
        }
        
        .section-title {
            color: #ff6b00 !important;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 15px;
            border-bottom: 2px solid #ff6b00 !important;
            padding-bottom: 8px;
            text-align: left;
        }
        
        .order-info {
            position: relative;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        
        .label {
            color: #000000 !important;
            font-weight: 600;
            width: 150px;
            text-align: left;
            flex-shrink: 0;
        }
        
        .value {
            color: #2d3748;
            text-align: left;
            position: absolute;
            left: 200px;
        }
        
        .logo {
            max-width: 200px;
            height: auto;
            margin: 30px 0;
        }
        
        .content-wrapper {
            padding: 40px;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .greeting {
            font-size: 16px;
            margin-bottom: 30px;
            color: #2d3748;
            text-align: left;
        }
        
        .signature {
            margin-top: 40px;
            padding-top: 20px;
            color: #2d3748; /* Aangepast naar de juiste kleur */
            text-align: left;
        }
        
        .social-links {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
        }
        
        @media only screen and (max-width: 640px) {
            .content-wrapper {
                padding: 20px;
            }
            
            .info-row {
                flex-direction: column;
            }
            
            .value {
                position: relative;
                left: 0;
            }
            
            .label {
                width: auto;
                margin-bottom: 5px;
            }
        }
        .logo {
            max-width: 200px !important;
            height: auto !important;
            margin: 30px auto !important;
            display: block !important;
        }
        .section-divider {
            border-bottom: 2px solid #f3723b !important;
            width: 100% !important;
            margin: 10px 0 !important;
        }
    </style>
    
    <!--[if mso]>
    <style type="text/css">
        body, table, td, p, a, span {
            font-family: Arial, Helvetica, sans-serif !important;
        }
        .social-links img {
            width: 24px !important;
            height: 24px !important;
            max-width: 24px !important;
            max-height: 24px !important;
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
    </style>
    <![endif]-->
</head>

<body>
    <!--[if mso]>
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td style="background-color: #f5f7fa;" align="center">
                <div style="border-radius: 8px; mso-border-radius-alt: 8px; background: #ffffff; padding: 30px; max-width: 600px; margin: 0 auto;">
    <![endif]-->
    
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#f5f7fa">
        <tr>
            <td align="center" valign="top">
                <!--[if mso]>
                <table width="600" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td>
                <![endif]-->
                
                <div class="content-wrapper">
                    <div class="order-section">
                        <div style="text-align: left;">
                            <a href="{{env('APP_URL')}}">
                                <!--[if mso]>
                                <img src="{{env('APP_URL')}}/img/logo.png" alt="{{env('APP_NAME')}}" width="200" style="max-width: 200px; width: 100%; height: auto; display: block; border: 0;">
                                <![endif]-->
                                <!--[if !mso]><!-->
                                <img src="{{asset('img/logo.png')}}" class="logo" width="480" alt="{{env('APP_NAME')}}" style="max-width: 200px !important; height: auto !important; margin: 30px auto !important; display: block !important; border: 0; outline: none;" />
                                <!--<![endif]-->
                            </a>
                        </div>
                        
                        <div class="greeting">
                            Hallo {{$user->getUsername()}},
                            <br><br>
                            Er is een nieuwe bestelling geplaatst via DeBurenKoken. Hieronder vind je alle details.
                        </div>
                        
                        <div class="section-title">Bestelgegevens</div>
                        <div class="order-info">
                            <div class="info-row">
                                <div class="label">Bestelnummer:</div>
                                <div class="value">{{ str_replace('Bestelnummer: ', '', $order->getParsedOrderUuid()) }}</div>
                            </div>
                            <div class="info-row">
                                <div class="label">Advertentie ID:</div>
                                <div class="value">{{$order->getParsedAdvertUuid()}}</div>
                            </div>
                            <div class="info-row">
                                <div class="label">Gerecht:</div>
                                <div class="value">{{$dish->getTitle()}}</div>
                            </div>
                            <div class="info-row">
                                <div class="label">Aantal porties:</div>
                                <div class="value">{{$order?->getPortionAmount()}}</div>
                            </div>
                            <div class="info-row">
                                <div class="label">Afhaalmoment:</div>
                                <div class="value">{{ \Carbon\Carbon::parse($advert->getPickupDate())->translatedFormat('l j F') }}, {{ substr($advert->getPickupFrom(), 0, 5) }} - {{ substr($advert->getPickupTo(), 0, 5) }}</div>
                            </div>
                                <div class="info-row">
                                        <div class="label">Verwacht afhaalmoment:</div>
                                        <div class="value">{{ \Carbon\Carbon::parse($order->getExpectedPickupTime())->format('H') }}:{{ \Carbon\Carbon::parse($order->getExpectedPickupTime())->format('i') }}</div>
                                    </div>
                            <div class="info-row">
                                <div class="label">Opmerking:</div>
                                <div class="value">{{$order->getRemarks()}}</div>
                            </div>
                        </div>
                        
                        <br> <!-- Extra witregel toegevoegd -->
                        <div class="section-title">Klantgegevens</div>
                        <div class="order-info">
                            <div class="info-row">
                                <div class="label">Naam:</div>
                                <div class="value">{{$client->getName()}}</div>
                            </div>
                            <div class="info-row">
                                <div class="label">E-mail:</div>
                                <div class="value">{{$client->getEmail()}}</div>
                            </div>
                            <div class="info-row">
                                <div class="label">Telefoon:</div>
                                <div class="value">{{$client->getPhoneNumber()}}</div>
                            </div>
                        </div>
                    
                        <div class="signature">
                            Met vriendelijke groet,
                            <br>
                            Het team van DeBurenKoken.nl
                        </div>
                    
                        @if(env('SOCIAL_FACEBOOK') || env('SOCIAL_INSTAGRAM') || env('SOCIAL_TWITTER'))
                        <div class="social-links">
                            @if(env('SOCIAL_FACEBOOK'))
                            <a href="{{env('SOCIAL_FACEBOOK')}}">
                                <!--[if mso]>
                                <img src="{{asset('img/facebook.png')}}" width="24" height="24" alt="Facebook" style="width: 24px !important; height: 24px !important; max-width: 24px !important; max-height: 24px !important; border: 0;" />
                                <![endif]-->
                                <!--[if !mso]><!-->
                                <img src="{{asset('img/facebook.png')}}" width="24" height="24" alt="Facebook" />
                                <!--<![endif]-->
                            </a>
                            @endif
                            @if(env('SOCIAL_TWITTER'))
                            <a href="{{env('SOCIAL_TWITTER')}}">
                                <!--[if mso]>
                                <img src="{{asset('img/twitter.png')}}" width="24" height="24" alt="Twitter" style="width: 24px !important; height: 24px !important; max-width: 24px !important; max-height: 24px !important; border: 0;" />
                                <![endif]-->
                                <!--[if !mso]><!-->
                                <img src="{{asset('img/twitter.png')}}" width="24" height="24" alt="Twitter" />
                                <!--<![endif]-->
                            </a>
                            @endif
                            @if(env('SOCIAL_INSTAGRAM'))
                            <a href="{{env('SOCIAL_INSTAGRAM')}}">
                                <!--[if mso]>
                                <img src="{{asset('img/instagram.png')}}" width="24" height="24" alt="Instagram" style="width: 24px !important; height: 24px !important; max-width: 24px !important; max-height: 24px !important; border: 0;" />
                                <![endif]-->
                                <!--[if !mso]><!-->
                                <img src="{{asset('img/instagram.png')}}" width="24" height="24" alt="Instagram" />
                                <!--<![endif]-->
                            </a>
                            @endif
                        </div>
                        @endif
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