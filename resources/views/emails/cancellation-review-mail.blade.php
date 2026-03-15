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
        }
        
        .logo {
            max-width: 200px;
            height: auto;
            margin: 20px auto;
            display: block;
        }
        
        h1 {
            font-size: 15px;
            color: #2d3748;
            margin: 10px 0;
            font-weight: normal;
            text-align: left;
        }
        
        p {
            margin: 10px 0;
            color: #2d3748;
            text-align: left;
        }
        
        .greeting {
            font-size: 15px;
            text-align: left;
            margin-bottom: 5%;  /* Zorgt voor extra ruimte, zoals in de voorbeeldmail */
        }
        
        .rating {
            text-align: center;
            margin: 20px 0;
        }
        
        .signature {
            margin-top: 20px;
            padding-top: 20px;  /* Toegevoegd om de afsluiting te laten overeenkomen met het voorbeeld */
            color: #2d3748;
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
        }
        
        /* Star rating styles */
        .star-wrapper {
            padding: 0;
            vertical-align: top;
        }
        
        * [lang~="x-star-wrapper"]:hover *[lang~="x-star-number"] {
            color: #119da2 !important;
            border-color: #119da2 !important;
        }
        
        * [lang~="x-star-wrapper"]:hover *[lang~="x-full-star"],
        * [lang~="x-star-wrapper"]:hover ~ *[lang~="x-star-wrapper"] *[lang~="x-full-star"] {
            display: block !important;
            width: auto !important;
            overflow: visible !important;
            float: none !important;
            margin-top: -1px !important;
        }
        
        * [lang~="x-star-wrapper"]:hover *[lang~="x-empty-star"],
        * [lang~="x-star-wrapper"]:hover ~ *[lang~="x-star-wrapper"] *[lang~="x-empty-star"] {
            display: block !important;
            width: 0 !important;
            overflow: hidden !important;
            float: left !important;
            height: 0 !important;
        }
    </style>
    
    <!--[if mso]>
    <style type="text/css">
        body, table, td, p, a, span {
            font-family: Arial, Helvetica, sans-serif !important;
        }
        .social-links img {
            width: 22px !important;
            height: 22px !important;
            max-width: 22px !important;
            max-height: 22px !important;
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
    
    <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#f5f7fa">
        <tr>
            <td align="center" valign="top">
                <!--[if mso]>
                <table width="600" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td>
                <![endif]-->
                
                <div class="content-wrapper">
                    <div class="order-section">
                        <a href="{{env('APP_URL')}}">
                            <!--[if mso]>
                            <img src="{{env('APP_URL')}}/img/logo.png" alt="{{env('APP_NAME')}}" width="200" style="max-width: 200px; width: 100%; height: auto; display: block; border: 0;">
                            <![endif]-->
                            <!--[if !mso]><!-->
                            <img src="{{asset('img/logo.png')}}" class="logo" alt="{{env('APP_NAME')}}" style="max-width: 200px; height: auto; margin: 20px auto; display: block; border: 0; outline: none;" />
                            <!--<![endif]-->
                        </a>
                        
                        <div style="padding: 0 20px;">
                            <!-- Aangepaste aanhef met de gewenste marge -->
                            <div class="greeting">
                                Hallo {{ $cook->user->getUsername() }},<br><br>
                                Hoe heb je het annuleren ervaren? Deel je beoordeling met ons.
                            </div>
                            
                            <div class="rating">
                                <table style="border-collapse: collapse; border-spacing: 0; width: 275px; margin: 0 auto; font-size: 50px; direction: rtl;" dir="rtl">
                                    <tbody>
                                        <tr>
                                            <td style="padding: 0; vertical-align: top;" width="55" class="star-wrapper" lang="x-star-wrapper">
                                                <div style="display: block; text-align: center; float: left; width: 55px; overflow: hidden; line-height: 60px;">
                                                    <a href="{{$url}}?rating=5" class="star" target="_blank" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block; height: 50px; width: 55px; overflow: hidden; line-height: 60px;" tabindex="1">
                                                        <div lang="x-empty-star" style="margin: 0; display: inline-block;">☆</div>
                                                        <div lang="x-full-star" style="margin: 0; display: inline-block; width: 0; overflow: hidden; float: left; display: none; height: 0;">★</div>
                                                    </a>
                                                </div>
                                            </td>
                                            <td style="padding: 0; vertical-align: top;" width="55" class="star-wrapper" lang="x-star-wrapper">
                                                <div style="display: block; text-align: center; float: left; width: 55px; overflow: hidden; line-height: 60px;">
                                                    <a href="{{$url}}?rating=4" class="star" target="_blank" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block; height: 50px; width: 55px; overflow: hidden; line-height: 60px;" tabindex="2">
                                                        <div lang="x-empty-star" style="margin: 0; display: inline-block;">☆</div>
                                                        <div lang="x-full-star" style="margin: 0; display: inline-block; width: 0; overflow: hidden; float: left; display: none; height: 0;">★</div>
                                                    </a>
                                                </div>
                                            </td>
                                            <td style="padding: 0; vertical-align: top;" width="55" class="star-wrapper" lang="x-star-wrapper">
                                                <div style="display: block; text-align: center; float: left; width: 55px; overflow: hidden; line-height: 60px;">
                                                    <a href="{{$url}}?rating=3" class="star" target="_blank" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block; height: 50px; width: 55px; overflow: hidden; line-height: 60px;" tabindex="3">
                                                        <div lang="x-empty-star" style="margin: 0; display: inline-block;">☆</div>
                                                        <div lang="x-full-star" style="margin: 0; display: inline-block; width: 0; overflow: hidden; float: left; display: none; height: 0;">★</div>
                                                    </a>
                                                </div>
                                            </td>
                                            <td style="padding: 0; vertical-align: top;" width="55" class="star-wrapper" lang="x-star-wrapper">
                                                <div style="display: block; text-align: center; float: left; width: 55px; overflow: hidden; line-height: 60px;">
                                                    <a href="{{$url}}?rating=2" class="star" target="_blank" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block; height: 50px; width: 55px; overflow: hidden; line-height: 60px;" tabindex="4">
                                                        <div lang="x-empty-star" style="margin: 0; display: inline-block;">☆</div>
                                                        <div lang="x-full-star" style="margin: 0; display: inline-block; width: 0; overflow: hidden; float: left; display: none; height: 0;">★</div>
                                                    </a>
                                                </div>
                                            </td>
                                            <td style="padding: 0; vertical-align: top;" width="55" class="star-wrapper" lang="x-star-wrapper">
                                                <div style="display: block; text-align: center; float: left; width: 55px; overflow: hidden; line-height: 60px;">
                                                    <a href="{{$url}}?rating=1" class="star" target="_blank" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block; height: 50px; width: 55px; overflow: hidden; line-height: 60px;" tabindex="5">
                                                        <div lang="x-empty-star" style="margin: 0; display: inline-block;">☆</div>
                                                        <div lang="x-full-star" style="margin: 0; display: inline-block; width: 0; overflow: hidden; float: left; display: none; height: 0;">★</div>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="signature">
                                Met vriendelijke groet,<br>
                                Het team van DeBurenKoken.nl
                            </div>
                        </div>
                        
                        @if(env('SOCIAL_FACEBOOK') || env('SOCIAL_INSTAGRAM') || env('SOCIAL_TWITTER'))
                        <div class="social-links">
                            @if(env('SOCIAL_FACEBOOK'))
                            <a href="{{env('SOCIAL_FACEBOOK')}}">
                                <!--[if mso]>
                                <img src="{{asset('img/facebook.png')}}" width="22" height="22" alt="Facebook" style="width: 22px !important; height: 22px !important; max-width: 22px !important; max-height: 22px !important; border: 0;" />
                                <![endif]-->
                                <!--[if !mso]><!-->
                                <img src="{{asset('img/facebook.png')}}" width="22" height="22" alt="Facebook" />
                                <!--<![endif]-->
                            </a>
                            @endif
                            @if(env('SOCIAL_TWITTER'))
                            <a href="{{env('SOCIAL_TWITTER')}}">
                                <!--[if mso]>
                                <img src="{{asset('img/twitter.png')}}" width="22" height="22" alt="Twitter" style="width: 22px !important; height: 22px !important; max-width: 22px !important; max-height: 22px !important; border: 0;" />
                                <![endif]-->
                                <!--[if !mso]><!-->
                                <img src="{{asset('img/twitter.png')}}" width="22" height="22" alt="Twitter" />
                                <!--<![endif]-->
                            </a>
                            @endif
                            @if(env('SOCIAL_INSTAGRAM'))
                            <a href="{{env('SOCIAL_INSTAGRAM')}}">
                                <!--[if mso]>
                                <img src="{{asset('img/instagram.png')}}" width="22" height="22" alt="Instagram" style="width: 22px !important; height: 22px !important; max-width: 22px !important; max-height: 22px !important; border: 0;" />
                                <![endif]-->
                                <!--[if !mso]><!-->
                                <img src="{{asset('img/instagram.png')}}" width="22" height="22" alt="Instagram" />
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