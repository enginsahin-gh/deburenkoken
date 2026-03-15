<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>Contactformulier {{env('APP_NAME')}}</title>
    <link href='http://fonts.googleapis.com/css?family=Montserrat:400,500,300,600,700' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700' rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Condiment" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">

    <style type="text/css">
        /* Reset en basis styling */
        body { 
            margin: 0; 
            padding: 0; 
            background: #f5f7fa;
            font-family: 'Open Sans', Arial, Helvetica, sans-serif;
            font-size: 16px !important;
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

        span.preheader{display: none; font-size: 1px;}
        html { width: 100%; }
        table { border-spacing: 0; border-collapse: collapse;}
        .ReadMsgBody { width: 100%; background-color: #FFFFFF; }
        .ExternalClass { width: 100%; background-color: #FFFFFF; }
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
        a,a:hover { text-decoration:none; color:#FFF;}
        img { display: block !important; }
        table td { border-collapse: collapse; }

        .logo {
            max-width: 300px !important;
            width: 100%;
            height: auto;
            margin: 20px auto;
            display: block;
        }

        .content-wrapper {
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
            width: 100%;
        }

        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
            color: #2d3748 !important;
            text-align: left;
            font-family: 'Open Sans', Arial, Helvetica, sans-serif;
        }

        .message-content {
            text-align: left;
            margin: 20px 0;
            padding: 0 25px;
        }

        .signature {
            margin-top: 20px;
            padding-top: 20px;
            color: #2d3748 !important;
            text-align: left;
            font-family: 'Open Sans', Arial, Helvetica, sans-serif;
            font-size: 16px;
        }

        .social-links {
            text-align: center;
            margin-top: 17px;
            padding-top: 17px;
        }

        .social-links a {
            margin: 0 10px;
            display: inline-block;
        }

        .social-links img {
            width: 24px;
            height: 24px;
        }

        @media only screen and (max-width:640px)
        {
            body {width:auto!important;}
            table [class=main] {width:85% !important;}
            table [class=full] {width:100% !important; margin:0px auto;}
            table [class=two-left-inner] {width:90% !important; margin:0px auto;}
            td[class="two-left"] { display: block; width: 100% !important; }
            table [class=menu-icon] { display:none;}
            img[class="image-full"] { width: 100% !important; }
            table[class=menu-icon] { display:none;}
            .content-wrapper {
                padding: 10px;
            }
            .message-content {
                padding: 0 10px;
            }
        }

        @media only screen and (max-width:479px)
        {
            body {width:auto!important;}
            table [class=main] {width:93% !important;}
            table [class=full] {width:100% !important; margin:0px auto;}
            td[class="two-left"] { display: block; width: 100% !important; }
            table [class=two-left-inner] {width:90% !important; margin:0px auto;}
            table [class=menu-icon] { display:none;}
            img[class="image-full"] { width: 100% !important; }
            table[class=menu-icon] { display:none;}
        }

        .orange-button {
            background: linear-gradient(to right, #f3723b 0%, #e54750 100%) !important;
            color: #ffffff !important;
            padding: 10px 20px !important;
            border-radius: 6px !important;
            text-decoration: none !important;
            display: inline-block !important;
            font-weight: bold !important;
            border: none !important;
            cursor: pointer !important;
        }

        .orange-button:hover {
            background: linear-gradient(to right, #e54750 0%, #f3723b 100%) !important;
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
    </style>
    <![endif]-->

</head>

<body yahoo="fix" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

    <!--[if mso]>
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td style="background-color: #f5f7fa;" align="center">
                <div style="border-radius: 8px; mso-border-radius-alt: 8px; background: #ffffff; padding: 30px; max-width: 600px; margin: 0 auto;">
    <![endif]-->

<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color: #f5f7fa;">
    <tr>
        <td align="center" valign="top">
            <!--[if mso]>
            <table width="600" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td>
            <![endif]-->

            <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#f5f7fa">
                <tr>
                    <td align="center" valign="top">
                        <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center" valign="top">
                                    <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" class="main">
                                        <tr>
                                            <td height="60" align="center" valign="top" style="font-size:60px; line-height:60px;">&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <div class="content-wrapper">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" class="email-container" style="background: #ffffff; border-radius: 8px; -webkit-border-radius: 8px; -moz-border-radius: 8px; mso-border-radius-alt: 8px; padding: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); max-width: 600px; margin: 0 auto;">
                                <!-- LOGO -->
                                <tr>
                                    <td align="center" style="padding-bottom: 20px;">
                                        <!--[if mso]>
                                        <img src="{{env('APP_URL')}}/img/logo.png" alt="{{env('APP_NAME')}}" width="300" style="max-width: 300px; width: 100%; height: auto; display: block; border: 0;">
                                        <![endif]-->
                                        <!--[if !mso]><!-->
                                        <a href="{{env('APP_URL')}}">
                                            <img src="{{asset('img/logo.png')}}" alt="{{env('APP_NAME')}}" class="logo" width="300" style="max-width: 300px; width: 100%; height: auto; margin: 20px auto; display: block; border: 0; outline: none;"/>
                                        </a>
                                        <!--<![endif]-->
                                        <div class="section-divider"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="message-content">
                                        <div class="greeting">
                                            Hallo {{$username}},
                                            <br><br>
                                            {{$dishTitle}} is gewijzigd. Je klanten zullen via de mail geinformeerd worden.
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="message-content">
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
                                                <img src="{{asset('img/facebook.png')}}" width="22" height="22" alt="Facebook" />
                                                <!--<![endif]-->
                                            </a>
                                            @endif
                                            @if(env('SOCIAL_TWITTER'))
                                            <a href="{{env('SOCIAL_TWITTER')}}">
                                                <!--[if mso]>
                                                <img src="{{asset('img/twitter.png')}}" width="24" height="24" alt="Twitter" style="width: 24px !important; height: 24px !important; max-width: 24px !important; max-height: 24px !important; border: 0;" />
                                                <![endif]-->
                                                <!--[if !mso]><!-->
                                                <img src="{{asset('img/twitter.png')}}" width="22" height="22" alt="Twitter" />
                                                <!--<![endif]-->
                                            </a>
                                            @endif
                                            @if(env('SOCIAL_INSTAGRAM'))
                                            <a href="{{env('SOCIAL_INSTAGRAM')}}">
                                                <!--[if mso]>
                                                <img src="{{asset('img/instagram.png')}}" width="24" height="24" alt="Instagram" style="width: 24px !important; height: 24px !important; max-width: 24px !important; max-height: 24px !important; border: 0;" />
                                                <![endif]-->
                                                <!--[if !mso]><!-->
                                                <img src="{{asset('img/instagram.png')}}" width="22" height="22" alt="Instagram" />
                                                <!--<![endif]-->
                                            </a>
                                            @endif
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center" valign="top">
                                    <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" class="main">
                                        <tr>
                                            <td height="55" align="center" valign="top" style="font-size:55px; line-height:55px;">&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

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

<!--Main Table End-->
</body>
</html>