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
            position: absolute;
            left: 200px;
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
        
        .annuleren-text {
            margin-bottom: 15px;
            color: #2d3748 !important;
            font-size: 15px !important;
            line-height: 1.6;
            text-align: left;
        }
        
        .annuleren-button {
            text-align: center;
            margin-top: 15px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        
        .btn-small {
            padding: 8px 16px;
            font-size: 12px;
        }
        
        .btn-outline {
            background-color: transparent;
            border: 1px solid #ff6b00;
            color: #ff6b00 !important;
        }
        
        .btn-outline:hover {
            background-color: #ff6b00;
            color: #ffffff !important;
        }
        
        .btn-orange {
            background-color: #ff6b00;
            border: 1px solid #ff6b00;
            color: #ffffff;
        }
        
        .btn-orange:hover {
            background-color: #e65a00;
            border-color: #e65a00;
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
        .button-container {
            text-align: center !important;
        }
        .button-table {
            margin: 0 auto !important;
        }
        .button-link {
            padding: 8px 16px !important;
            border-radius: 4px !important;
            mso-border-radius-alt: 4px !important;
            line-height: 16px !important;
            background-color: transparent !important;
            color: #ff6b00 !important;
            text-decoration: none !important;
            font-weight: bold !important;
            font-family: Arial, Helvetica, sans-serif !important;
            border: 1px solid #ff6b00 !important;
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
                            Hallo {{$client->getName()}},
                            <br><br>
                            Je bestelling is succesvol geplaatst. Hieronder vind je de benodigde gegevens.
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
                                <div class="value">
                                    {{ \Carbon\Carbon::parse($advert->getPickupDate())->isoFormat('dddd D MMMM') }}, 
                                    {{ substr($advert->getPickupFrom(), 0, 5) }} - {{ substr($advert->getPickupTo(), 0, 5) }}
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="label">Verwacht afhaalmoment:</div>
                                <div class="value">{{ \Carbon\Carbon::parse($order->getExpectedPickupTime())->format('H:i') }}</div>
                            </div>
                            <div class="info-row" style="margin-bottom: 5%;">
                                <div class="label">Opmerking:</div>
                                <div class="value">{{$order->getRemarks()}}</div>
                            </div>
                        </div>
                   
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
                            <div class="info-row" style="margin-bottom: 5%;">
                                <div class="label">Adres:</div>
                                <div class="value">{{$cook->getStreet()}} {{$cook->getHouseNumber()}}{{$cook->getAddition()}} {{$cook->getCity()}}</div>
                            </div>
                        </div>
                
                        <div class="section-title">Annuleren</div>
                        <div class="order-info">
                            <div class="annuleren-text">
                                Als er onverhoopt iets verandert, kun je de bestelling annuleren tot {{ \Carbon\Carbon::parse($advert->order_date)->isoFormat('D MMMM') }} {{implode(":", array_slice(explode(":", $advert->order_time ), 0, 2));}} via de onderstaande knop.
                            </div>
                            <div class="annuleren-button">
                                <!--[if mso]>
                                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{$link}}" style="height:32px;v-text-anchor:middle;width:160px;" arcsize="12%" strokecolor="#ff6b00" strokeweight="1pt" fillcolor="transparent">
                                  <w:anchorlock/>
                                  <center style="color:#ff6b00;font-family:Arial, Helvetica, sans-serif;font-size:12px;font-weight:bold;">Annuleer bestelling</center>
                                </v:roundrect>
                                <![endif]-->
                                <!--[if !mso]><!-->
                                <a href="{{$link}}" target="_blank" class="btn btn-small btn-outline">Annuleer bestelling</a>
                                <!--<![endif]-->
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