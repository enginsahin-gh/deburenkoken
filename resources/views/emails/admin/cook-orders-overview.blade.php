<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>Contactformulier {{env('APP_NAME')}}</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    
    <style type="text/css">
        body { 
            margin: 0; 
            padding: 0; 
            background: #f5f7fa;
            font-family: 'Open Sans', sans-serif;
            font-size: 15px;
            line-height: 1.6;
            color: #2d3748;
        }
        .header-section {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
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
            color: #ff6b00;
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
            color: #718096;
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
            border-top: 1px solid #e2e8f0;
            color: #718096;
            text-align: left;
        }
        
        .social-links {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
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
</head>

<body yahoo="fix" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#f5f7fa">
        <tr>
            <td align="center" valign="top">
                <div class="content-wrapper">
                    <div style="text-align: left;">
                        <a href="{{env('APP_URL')}}">
                            <img src="{{asset('img/logo.png')}}" alt="{{env('APP_NAME')}}" class="logo"/>
                        </a>
                    </div>
                    
                    <div class="greeting">  
                        Hallo {{$user->getUsername()}},
                        <br><br>
                        Hier is een overzicht van de bestellingen die behoren tot uw advertentie met het advertentienummer: {{ $advertUuid }}.
                    </div>
                    
                    <div class="order-section">
                        <div class="section-title">Bestellingen</div>
                        <div class="order-info">
                            <!-- Hier kunnen de bestellingen worden weergegeven -->
                            <div class="info-row">
                                <div class="label">Advertentienummer:</div>
                                <div class="value">{{ $advertUuid }}</div>
                            </div>
                            <!-- Voeg hier meer bestelgegevens toe indien nodig -->
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
                            <img src="{{asset('img/facebook.png')}}" width="24" height="24" alt="Facebook" />
                        </a>
                        @endif
                        @if(env('SOCIAL_TWITTER'))
                        <a href="{{env('SOCIAL_TWITTER')}}">
                            <img src="{{asset('img/twitter.png')}}" width="24" height="24" alt="Twitter" />
                        </a>
                        @endif
                        @if(env('SOCIAL_INSTAGRAM'))
                        <a href="{{env('SOCIAL_INSTAGRAM')}}">
                            <img src="{{asset('img/instagram.png')}}" width="24" height="24" alt="Instagram" />
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
            </td>
        </tr>
    </table>
</body>
</html>