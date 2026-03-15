<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>Contactformulier {{env('APP_NAME')}}</title>
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
        
        .content-wrapper {
            padding: 40px !important;
            max-width: 600px !important;
            margin: 0 auto !important;
        }
        
        .email-container {
            background: #ffffff !important;
            border-radius: 8px !important;
            padding: 20px !important;
            margin: 0 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important;
            position: relative !important;
        }
        
        .logo {
            max-width: 200px !important;
            height: auto !important;
            margin: 30px auto !important;
            display: block !important;
        }
        
        .text-container {
            font-size: 16px !important;
            margin: 20px 0 !important;
            color: #2d3748 !important;
            text-align: left !important;
            padding: 0 20px !important;
            border-radius: 6px !important;
        }
        
        .button-container {
            text-align: center !important;
            margin-top: 20px !important;
            border-radius: 6px !important;
        }

        .orange-button {
            background: linear-gradient(to right, #f3723b 0%, #e54750 100%) !important;
            color: #ffffff !important;
            padding: 12px 25px !important;
            border-radius: 6px !important;
            text-decoration: none !important;
            font-weight: bold !important;
            font-family: 'Open Sans', sans-serif !important;
            display: inline-block !important;
            border: none !important;
            cursor: pointer !important;
        }

        .orange-button:hover {
            background: linear-gradient(to right, #e54750 0%, #f3723b 100%) !important;
        }

        @media only screen and (max-width: 640px) {
            .content-wrapper {
                padding: 20px !important;
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
            padding: 12px 25px !important;
            border-radius: 6px !important;
            mso-border-radius-alt: 6px !important;
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
                    <div class="email-container">
                        <div>
                            <a href="{{env('APP_URL')}}">
                                <!--[if mso]>
                                <img src="{{env('APP_URL')}}/img/logo.png" alt="{{env('APP_NAME')}}" width="200" style="max-width: 200px; width: 100%; height: auto; display: block; border: 0;">
                                <![endif]-->
                                <!--[if !mso]><!-->
                                <img src="{{asset('img/logo.png')}}" alt="{{env('APP_NAME')}}" class="logo" style="max-width: 200px !important; height: auto !important; margin: 30px auto !important; display: block !important; border: 0; outline: none;"/>
                                <!--<![endif]-->
                            </a>
                        </div>
                        
                        <div class="text-container">
                            Hallo {{$username}},
                            <br><br>
                            Je aanvraag voor het wijzigen van je e-mailadres is succesvol ontvangen.
                            Rond je aanvraag af door je e-mailadres te verifiëren.
                        </div>

                        <div class="button-container">
                            <!--[if mso]>
                            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{$link}}" style="height:52px;v-text-anchor:middle;width:260px;" arcsize="12%" strokecolor="#f3723b" strokeweight="0pt" fillcolor="#f3723b">
                              <w:anchorlock/>
                              <center style="color:#ffffff;font-family:Arial, Helvetica, sans-serif;font-size:16px;font-weight:bold;">E-mailadres verifiëren</center>
                            </v:roundrect>
                            <![endif]-->
                            <!--[if !mso]><!-->
                            <table cellspacing="0" cellpadding="0" border="0" align="center">
                                <tr>
                                    <td align="center" bgcolor="#f3723b" 
                                        style="border-radius: 6px !important; background: linear-gradient(to right, #f3723b 0%, #e54750 100%);">
                                        <a href="{{$link}}" target="_blank" 
                                        style="display: block; font-size: 16px; font-weight: bold; color: #ffffff; 
                                                text-decoration: none; padding: 12px 25px; border: none; 
                                                font-family: 'Open Sans', sans-serif;">
                                            E-mailadres verifiëren
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <!--<![endif]-->
                        </div>

                        <div class="text-container">
                            Met vriendelijke groet,<br>
                            Het team van DeBurenKoken.nl
                        </div>

                        @if(env('SOCIAL_FACEBOOK') || env('SOCIAL_INSTAGRAM') || env('SOCIAL_TWITTER'))
                        <div class="button-container">
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