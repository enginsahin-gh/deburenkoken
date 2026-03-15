<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>Nieuwe advertentie {{env('APP_NAME')}}</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    
    <style type="text/css">
        /* Reset en basis styling */
        body { 
            margin: 0; 
            padding: 0; 
            background: #f5f7fa;
            font-family: 'Open Sans', Arial, sans-serif;
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
        
        .content-section {
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .section-title {
            color: #ff6b00 !important;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            text-align: left;
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
        
        .logo {
            max-width: 200px !important;
            height: auto !important;
            margin: 30px auto !important;
            display: block !important;
        }
        
        .message-content {
            text-align: left;
            margin: 20px 0;
            padding: 0 20px;
        }
        
        /* Aangepaste button styling voor betere compatibiliteit */
        .button-container {
            text-align: center;
            margin: 25px 0;
        }
        
        .signature {
            margin-top: 20px;
            padding-top: 10px;
            color: #2d3748;
            font-size: 14px;
            text-align: left;
        }
        
        .unsubscribe {
            margin-top: 20px;
            font-size: 12px;
            color: #718096;
        }
        
        .unsubscribe a {
            color: #718096;
            text-decoration: underline;
        }
        
        .unsubscribe a:hover {
            color: #f3723b;
        }
        
        .social-links {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
        }
        
        .social-links a {
            margin: 0 10px;
            display: inline-block;
        }
        
        .social-links img {
            width: 24px;
            height: 24px;
            transition: opacity 0.3s ease;
        }
        
        .social-links img:hover {
            opacity: 0.8;
        }
        
        @media only screen and (max-width: 640px) {
            .content-wrapper {
                padding: 20px;
            }
            
            .message-content {
                padding: 0 10px;
            }
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
        .button-container {
            text-align: center !important;
        }
        .button-table {
            margin: 0 auto !important;
        }
        .button-link {
            padding: 10px 15px !important;
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
    
    <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#f5f7fa">
        <tr>
            <td align="center" valign="top">
                <!--[if mso]>
                <table width="600" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td>
                <![endif]-->
                
                <div class="content-wrapper">
                    <div class="header-section">
                        <a href="{{env('APP_URL')}}">
                            <!--[if mso]>
                            <img src="{{env('APP_URL')}}/img/logo.png" alt="{{env('APP_NAME')}}" width="200" style="max-width: 200px; width: 100%; height: auto; display: block; border: 0;">
                            <![endif]-->
                            <!--[if !mso]><!-->
                            <img src="{{asset('img/logo.png')}}" class="logo" width="480" alt="{{env('APP_NAME')}}" style="max-width: 200px !important; height: auto !important; margin: 30px auto !important; display: block !important; border: 0; outline: none;" />
                            <!--<![endif]-->
                        </a>
                        <div class="section-title"></div>
                        
                        <div class="message-content">
                            <p>Hallo {{$client->getName() ?? 'gebruiker'}},</p>
                            
                            <p>{{$cook->user->getUsername()}} heeft een nieuwe advertentie geplaatst.</p>
                        </div>
                   
                        <div class="message-content">
                            <!-- Verbeterde button met table-based layout voor betere compatibiliteit -->
                            <div class="button-container">
                                <!--[if mso]>
                                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{$url}}" style="height:52px;v-text-anchor:middle;width:200px;" arcsize="12%" strokecolor="#f3723b" strokeweight="2pt" fillcolor="#f3723b">
                                  <w:anchorlock/>
                                  <center style="color:#ffffff;font-family:Arial, Helvetica, sans-serif;font-size:15px;font-weight:bold;">Advertentie bekijken</center>
                                </v:roundrect>
                                <![endif]-->
                                <!--[if !mso]><!-->
                                <!-- Table-based button voor maximale compatibiliteit -->
                                <table width="200" border="0" cellspacing="0" cellpadding="0" align="center">
                                    <tr>
                                        <td align="center" bgcolor="#f3723b" style="background: #f3723b; background: linear-gradient(to right, #f3723b 0%, #e54750 100%); border-radius: 6px; border: 2px solid #f3723b;">
                                            <a href="{{$url}}" target="_blank" style="color: #ffffff; font-family: 'Open Sans', Arial, sans-serif; font-size: 15px; font-weight: bold; text-decoration: none; padding: 10px 15px; display: block; border-radius: 4px;">
                                                Advertentie bekijken
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                                <!--<![endif]-->
                                
                                <!-- Fallback link in geval de button niet werkt -->
                                <!-- <div style="text-align: center; margin-top: 10px; font-size: 12px;">
                                    <p>Als de knop niet werkt, gebruik dan deze link: <a href="{{$url}}" style="color: #f3723b;">{{$url}}</a></p>
                                </div> -->
                            </div>

                            <div class="section-title"></div>
                            
                            <div class="signature">
                                <p>Met vriendelijke groet,<br>Het team van DeBurenKoken.nl</p>
                            </div>
                            
                            <div class="unsubscribe">
                                <a href="{{ route('mailinglist.unsubscribe', ['cookUuid' => $cook->getUuid(), 'clientUuid' => $client->getUuid()]) }}" target="_blank">
                                    Geen mails meer ontvangen? Klik dan hier om af te melden
                                </a>
                            </div>
                        </div>
                        
                        @if(env('SOCIAL_FACEBOOK') || env('SOCIAL_INSTAGRAM') || env('SOCIAL_TWITTER'))
                        <div class="social-links">
                            @if(env('SOCIAL_FACEBOOK'))
                            <a href="{{env('SOCIAL_FACEBOOK')}}">
                                <!--[if mso]>
                                <img src="{{asset('img/facebook.png')}}" width="24" height="24" alt="Facebook" style="width: 24px !important; height: 24px !important; max-width: 24px !important; max-height: 24px !important; border: 0;" />
                                <![endif]-->
                                <!--[if !mso]><!-->
                                <img src="{{asset('img/facebook.png')}}" alt="Facebook" />
                                <!--<![endif]-->
                            </a>
                            @endif
                            @if(env('SOCIAL_TWITTER'))
                            <a href="{{env('SOCIAL_TWITTER')}}">
                                <!--[if mso]>
                                <img src="{{asset('img/twitter.png')}}" width="24" height="24" alt="Twitter" style="width: 24px !important; height: 24px !important; max-width: 24px !important; max-height: 24px !important; border: 0;" />
                                <![endif]-->
                                <!--[if !mso]><!-->
                                <img src="{{asset('img/twitter.png')}}" alt="Twitter" />
                                <!--<![endif]-->
                            </a>
                            @endif
                            @if(env('SOCIAL_INSTAGRAM'))
                            <a href="{{env('SOCIAL_INSTAGRAM')}}">
                                <!--[if mso]>
                                <img src="{{asset('img/instagram.png')}}" width="24" height="24" alt="Instagram" style="width: 24px !important; height: 24px !important; max-width: 24px !important; max-height: 24px !important; border: 0;" />
                                <![endif]-->
                                <!--[if !mso]><!-->
                                <img src="{{asset('img/instagram.png')}}" alt="Instagram" />
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