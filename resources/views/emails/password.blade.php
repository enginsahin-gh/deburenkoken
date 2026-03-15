<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>Wachtwoord reset {{ env('APP_NAME') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    
    <style type="text/css">
        /* Reset en basis styling */
        body { 
            margin: 0; 
            padding: 0; 
            background: #f5f7fa;
            font-family: 'Open Sans', Verdana, sans-serif;
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
        
        .content-wrapper {
            padding: 40px;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .order-section {
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
            margin: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: relative;
        }
        
        .logo {
            max-width: 200px;
            height: auto;
            margin: 30px 0;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        
        .greeting {
            font-size: 14px;
            margin-bottom: 20px;
            color: #000000;
            text-align: left;
        }
        
        .message {
            font-size: 14px;
            line-height: 25px;
            margin-bottom: 25px;
            color: #000000;
            text-align: left;
        }
        
        .signature {
            font-size: 14px;
            line-height: 25px;
            margin-top: 25px;
            color: #000000;
            text-align: left;
        }
        
        .social-links {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
        }
        
        /* Button styling - aangepast naar werkende versie */
        .button-container {
            text-align: center;
            margin: 30px 0;
            width: 100%;
        }
        
        .button-table {
            border-collapse: separate !important;
            margin: 0 auto;
            border-radius: 8px !important;
            -webkit-border-radius: 8px !important;
            -moz-border-radius: 8px !important;
            overflow: hidden;
            background: #f3723b;
            background: linear-gradient(to right, #f3723b, #e54750);
        }
        
        .button-link {
            display: inline-block !important;
            min-width: 200px;
            padding: 8px 30px;
            border: 0 !important;
            border-radius: 8px !important;
            -webkit-border-radius: 8px !important;
            -moz-border-radius: 8px !important;
            background-color: #f3723b !important;
            background: linear-gradient(to right, #f3723b, #e54750) !important;
            color: #ffffff !important;
            text-align: center;
            vertical-align: middle;
            text-decoration: none !important;
            font-weight: bold;
            font-family: 'Open Sans', Arial, Helvetica, sans-serif;
            font-size: 16px;
            line-height: 36px;
            overflow: hidden;
        }
        
        /* Mobile styles */
        @media only screen and (max-width: 640px) {
            .content-wrapper {
                padding: 20px;
                width: 85% !important;
                max-width: none !important;
            }
            
            .button-link {
                width: 80% !important;
                min-width: 250px !important;
                padding: 10px 20px;
                line-height: 40px;
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
            padding: 8px 30px !important;
            border-radius: 8px !important;
            mso-border-radius-alt: 8px !important;
            line-height: 36px !important;
            background-color: #f3723b !important;
            color: #ffffff !important;
            text-decoration: none !important;
            font-weight: bold !important;
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
                        <div>
                            <a href="{{ env('APP_URL') }}">
                                <!--[if mso]>
                                <img src="{{env('APP_URL')}}/img/logo.png" alt="{{ env('APP_NAME') }}" width="200" style="max-width: 200px; width: 100%; height: auto; display: block; border: 0;">
                                <![endif]-->
                                <!--[if !mso]><!-->
                                <img src="{{ asset('img/logo.png') }}" alt="{{ env('APP_NAME') }}" class="logo" style="max-width: 200px; height: auto; margin: 30px 0; display: block; margin-left: auto; margin-right: auto; border: 0; outline: none;"/>
                                <!--<![endif]-->
                            </a>
                        </div>
                        
                        <div class="greeting">
                            Hallo {{ $user->getUsername() }},
                        </div>
                        
                        <div class="message">
                            Je hebt een aanvraag ingediend om een nieuw wachtwoord in te stellen. Geen zorgen, dit kan gemakkelijk via de onderstaande link.
                        </div>
                        
                        <div class="button-container" style="text-align: center; margin: 30px 0; width: 100%;">
                            <!--[if mso]>
                            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ route('login.forgot.reset', ['token' => $token, 'email' => $user->getEmail()]) }}" style="height:52px;v-text-anchor:middle;width:260px;" arcsize="15%" strokecolor="#f3723b" strokeweight="0pt" fillcolor="#f3723b">
                              <w:anchorlock/>
                              <center style="color:#ffffff;font-family:Arial, Helvetica, sans-serif;font-size:16px;font-weight:bold;">Stel een nieuw wachtwoord in</center>
                            </v:roundrect>
                            <![endif]-->
                            <!--[if !mso]><!-->
                            <div style="display: inline-block; border-radius: 8px !important; -webkit-border-radius: 8px !important; -moz-border-radius: 8px !important; overflow: hidden; background: linear-gradient(to right, #f3723b, #e54750);">
                                <a href="{{ route('login.forgot.reset', ['token' => $token, 'email' => $user->getEmail()]) }}" target="_blank" class="button-link" style="display: inline-block !important; min-width: 200px; padding: 8px 30px; border: 0 !important; border-radius: 8px !important; -webkit-border-radius: 8px !important; -moz-border-radius: 8px !important; background: linear-gradient(to right, #f3723b, #e54750) !important; color: #ffffff !important; text-align: center; vertical-align: middle; text-decoration: none !important; font-weight: bold; font-family: 'Open Sans', Arial, Helvetica, sans-serif; font-size: 16px; line-height: 36px;">Stel een nieuw wachtwoord in</a>
                            </div>
                            <!--<![endif]-->
                        </div>
                        
                        <div class="signature">
                            Met vriendelijke groet,<br>
                            Het team van DeBurenKoken.nl
                        </div>
                        
                        @if(env('SOCIAL_FACEBOOK') || env('SOCIAL_INSTAGRAM') || env('SOCIAL_TWITTER'))
                        <div class="social-links">
                            @if(env('SOCIAL_FACEBOOK'))
                            <a href="{{ env('SOCIAL_FACEBOOK') }}">
                                <!--[if mso]>
                                <img src="{{ asset('img/facebook.png') }}" width="24" height="24" alt="Facebook" style="width: 24px !important; height: 24px !important; max-width: 24px !important; max-height: 24px !important; border: 0;" />
                                <![endif]-->
                                <!--[if !mso]><!-->
                                <img src="{{ asset('img/facebook.png') }}" width="24" height="24" alt="Facebook" />
                                <!--<![endif]-->
                            </a>
                            @endif
                            @if(env('SOCIAL_TWITTER'))
                            <a href="{{ env('SOCIAL_TWITTER') }}">
                                <!--[if mso]>
                                <img src="{{ asset('img/twitter.png') }}" width="24" height="24" alt="Twitter" style="width: 24px !important; height: 24px !important; max-width: 24px !important; max-height: 24px !important; border: 0;" />
                                <![endif]-->
                                <!--[if !mso]><!-->
                                <img src="{{ asset('img/twitter.png') }}" width="24" height="24" alt="Twitter" />
                                <!--<![endif]-->
                            </a>
                            @endif
                            @if(env('SOCIAL_INSTAGRAM'))
                            <a href="{{ env('SOCIAL_INSTAGRAM') }}">
                                <!--[if mso]>
                                <img src="{{ asset('img/instagram.png') }}" width="24" height="24" alt="Instagram" style="width: 24px !important; height: 24px !important; max-width: 24px !important; max-height: 24px !important; border: 0;" />
                                <![endif]-->
                                <!--[if !mso]><!-->
                                <img src="{{ asset('img/instagram.png') }}" width="24" height="24" alt="Instagram" />
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