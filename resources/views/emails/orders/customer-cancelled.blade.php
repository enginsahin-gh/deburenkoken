<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>Orderannulering {{env('APP_NAME')}}</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    
    <style type="text/css">
        /* Reset en basis styling */
        body { 
            margin: 0; 
            padding: 0; 
            background: #f5f7fa;
            font-family: 'Open Sans', sans-serif;
            font-size: 15px !important;
            line-height: 1.6;
            color: #2d3748 !important;
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
            margin: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: relative;
        }
        
        .section-title {
            color: #ff6b00 !important;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 15px;
            border-bottom: 2px solid #ff6b00;
            padding-bottom: 8px;
            text-align: left;
            position: relative;
        }
        .section-title::before { 
            content: '';
            display: block;
            height: 20px; 
        }
        .order-info {
            position: relative;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        
        .label {
            color: #2d3748 !important;
            font-weight: 600;
            width: 150px;
            text-align: left;
            flex-shrink: 0;
        }
        
        .value {
            color: #2d3748 !important;
            text-align: left;
            position: relative;
            left: 0;
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
            padding: 40px;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .header-section {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .greeting {
            font-size: 16px;
            margin-bottom: 0;
            color: #2d3748 !important;
            text-align: left;
        }
        
        .signature {
            margin-top: 20px;
            padding-top: 20px;
            color: #2d3748 !important;
            text-align: left;
        }
        
        .social-links {
            text-align: center;
            margin-top: 20px;
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
        .header-section {
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
                        <div>
                            <a href="{{env('APP_URL')}}">
                                <!--[if mso]>
                                <img src="{{env('APP_URL')}}/img/logo.png" alt="{{env('APP_NAME')}}" width="200" style="max-width: 200px; width: 100%; height: auto; display: block; border: 0;">
                                <![endif]-->
                                <!--[if !mso]><!-->
                                <img src="{{asset('img/logo.png')}}" alt="{{env('APP_NAME')}}" class="logo" style="max-width: 200px; height: auto; margin: 30px 0; display: block; margin-left: auto; margin-right: auto; border: 0; outline: none;"/>
                                <!--<![endif]-->
                            </a>
                        </div>
                        
                        <div class="greeting" style="margin-bottom: 5%">
                            Hallo {{$order->client->getName()}},
                            <br><br>
                            Je hebt de onderstaande bestelling geannuleerd. {{$user->getUsername()}} is hierover geïnformeerd.
                        </div>
                 
                        <div class="section-title">Bestelgegevens</div>
                        <div class="order-info">
                            <div class="info-row">
                                <div class="label">Bestelnummer:</div>
                                <div class="value" style="position: relative; left: 0;">{{-- Strip any potential "Bestelnummer:" text from the output --}}
                                    @php
                                        $orderNumber = $order->getParsedOrderUuid();
                                        // Remove "Bestelnummer:" if it exists in the string
                                        $orderNumber = str_replace('Bestelnummer:', '', $orderNumber);
                                        // Remove any extra spaces
                                        $orderNumber = trim($orderNumber);
                                        echo $orderNumber;
                                    @endphp
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="label">Advertentie ID:</div>
                                <div class="value">{{ substr($order->getParsedAdvertUuid(), -6) }}</div>
                            </div>
                            <div class="info-row">
                                <div class="label">Gerecht:</div>
                                <div class="value">{{$order->dish->getTitle()}}</div>
                            </div>
                            <div class="info-row">
                                <div class="label">Aantal porties:</div>
                                <div class="value">{{$order->getPortionAmount()}}</div>
                            </div>
                            <div class="info-row">
                                <div class="label">Afhaalmoment:</div>
                                <div class="value">
                                    {{ \Carbon\Carbon::parse($order->advert->getPickupDate())->locale('nl')->translatedFormat('l j F') }}, 
                                    {{ substr($order->advert->getPickupFrom(), 0, 5) }} - {{ substr($order->advert->getPickupTo(), 0, 5) }}
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="label">Verwacht afhaalmoment:</div>
                                <div class="value">{{ \Carbon\Carbon::parse($order->getExpectedPickupTime())->format('H:i') }}</div>
                            </div>
                            <div class="info-row">
                                <div class="label">Opmerking:</div>
                                <div class="value">{{$order->getRemarks()}}</div>
                            </div>
                        </div>
                   
                        <!-- Extra witregel toegevoegd -->
                        <br>
                        <div class="section-title">Thuiskokgegevens</div>
                        <div class="order-info">
                            <div class="info-row">
                                <div class="label">Naam:</div>
                                <div class="value">{{$user->getUsername()}}</div>
                            </div>
                            <div class="info-row">
                                <div class="label">E-mail:</div>
                                <div class="value">{{$user->getEmail()}}</div>
                            </div>
                            <div class="info-row">
                                <div class="label">Telefoon:</div>
                                <div class="value">{{$user->userProfile->getPhoneNumber()}}</div>
                            </div>
                            <div class="info-row">
                                <div class="label">Adres:</div>
                                <div class="value">{{$user->cook?->getStreet()}} {{$user->cook?->getHouseNumber()}}{{$user->cook?->getAddition()}} {{$user->cook?->getCity()}}</div>
                            </div>
                        </div>
                        
                        <!-- Added Cancellation Message Section -->
                        <br>
                        <div class="section-title">Annuleringsbericht</div>
                        <div class="order-info">
                        <div class="info-row">
                            <div class="value" style="text-align: left; margin-left: 0;">{{ $cancelMessage }}</div>
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