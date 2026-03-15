<!-- resources/views/emails/alert-next-cancellation-cost.blade.php -->
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>Grens kosteloos annuleren bereikt - {{env('APP_NAME')}}</title>
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
        
        .section-title {
            color: #ff6b00 !important;
            font-weight: 600;
            font-size: 18px !important;
            margin-bottom: 20px;
            border-bottom: 2px solid #ff6b00;
            padding-bottom: 8px;
            text-align: left;
            font-family: 'Open Sans', sans-serif !important;
        }
        
        .greeting, .message-content {
            font-size: 16px !important;
            margin: 20px 0 !important;
            color: #2d3748 !important;
            text-align: left !important;
            padding: 0 20px !important;
            font-family: 'Open Sans', sans-serif !important;
            line-height: 1.6;
        }
        
        .signature {
            margin-top: 20px;
            padding-top: 20px;
            color: #2d3748 !important;
            text-align: left;
            font-size: 16px !important;
            font-family: 'Open Sans', sans-serif !important;
            padding: 0 20px !important;
        }
        
        .social-links {
            text-align: center;
            margin-top: 20px;
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
            
            .greeting, .message-content, .signature {
                padding: 0 10px !important;
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
                        <a href="{{env('APP_URL')}}">
                            <!--[if mso]>
                            <img src="{{env('APP_URL')}}/img/logo.png" alt="{{env('APP_NAME')}}" width="200" style="max-width: 200px; width: 100%; height: auto; display: block; border: 0;">
                            <![endif]-->
                            <!--[if !mso]><!-->
                            <img src="{{asset('img/logo.png')}}" class="logo" alt="{{env('APP_NAME')}}" style="max-width: 200px; height: auto; margin: 20px auto; display: block; border: 0; outline: none;" />
                            <!--<![endif]-->
                        </a>
                        
                        <div class="section-title">
                            Grens kosteloos annuleren bereikt
                        </div>
                        
                        <div class="greeting">
                            Hallo {{$user->getUsername()}},
                        </div>
                        
                        <div class="message-content">
                            De grens voor kosteloos annuleren is bereikt. We zijn bij een volgende annulering genoodzaakt om transactiekosten van {{number_format($costs, 2, ',', '.')}} euro in rekening te brengen. De volgende maand zal kosteloos annuleren weer mogelijk zijn.
                        </div>
                        
                        <div class="signature">
                            Met vriendelijke groet,<br>
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