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
    <style type="text/css">
        body { margin: 0; font-family: 'Open Sans', sans-serif; font-size: 15px !important; padding: 0; -webkit-text-size-adjust: none; -ms-text-size-adjust: none; background: #f0f3f8; }
        span.preheader { display: none; font-size: 1px; }
        html { width: 100%; }
        table { border-spacing: 0; border-collapse: collapse; }
        .ReadMsgBody { width: 100%; background-color: #FFFFFF; }
        .ExternalClass { width: 100%; background-color: #FFFFFF; }
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
        a, a:hover { text-decoration: none; color: #FFF; }
        img { display: block !important; max-width: 100%; }
        table td { border-collapse: collapse; }

        /* Outlook fixes */
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            outline: none;
            text-decoration: none;
        }

        @media only screen and (max-width: 640px) {
            body { width: auto !important; }
            table [class=main] { width: 500px !important; margin: 0 auto !important; }
            table [class=full] { width: 100% !important; margin: 0px auto; }
            table [class=two-left-inner] { width: 90% !important; margin: 0px auto; }
            td[class="two-left"] { display: block; width: 100% !important; }
            img[class="image-full"] { width: 100% !important; }
            table[class=menu-icon] { display: none; }
        }
        @media only screen and (max-width: 479px) {
            body { width: auto !important; }
            table [class=main] { width: 500px !important; margin: 0 auto !important; }
            table [class=full] { width: 100% !important; margin: 0px auto; }
            td[class="two-left"] { display: block; width: 100% !important; }
            table [class=two-left-inner] { width: 90% !important; margin: 0px auto; }
            img[class="image-full"] { width: 100% !important; }
            table[class=menu-icon] { display: none; }
        }
        .logo { max-width: 480px !important; height: auto !important; display: block !important; margin: 0 auto !important; }
        .orange-button { background: linear-gradient(to right, #f3723b 0%, #e54750 100%) !important; color: #ffffff !important; padding: 10px 20px !important; border-radius: 6px !important; text-decoration: none !important; display: inline-block !important; font-weight: bold !important; border: none !important; cursor: pointer !important; font-family: 'Open Sans', sans-serif; font-size: 15px !important; }
        .orange-button:hover { background: linear-gradient(to right, #e54750 0%, #f3723b 100%) !important; }
        .signature { margin-top: 20px; padding-top: 20px; color: #2d3748 !important; text-align: left; font-family: 'Open Sans', sans-serif; font-size: 15px !important; }
        .section-divider { border-bottom: 2px solid #f3723b !important; width: 100% !important; margin: 10px 0 !important; }
        .social-icons { margin: 0 auto; display: flex; justify-content: center; }
        .social-icons td { padding: 0 10px; }
    </style>

    <!--[if mso]>
    <style type="text/css">
        body, table, td, p, a, span {
            font-family: Arial, Helvetica, sans-serif !important;
        }
        .main {
            border-radius: 8px !important;
            mso-border-radius-alt: 8px !important;
        }
        .social-icons img {
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
    </style>
    <![endif]-->
</head>
<body yahoo="fix" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<!--[if mso]>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td style="background-color: #f0f3f8;" align="center">
            <div style="border-radius: 8px; mso-border-radius-alt: 8px; background: #ffffff; padding: 30px; max-width: 600px; margin: 0 auto;">
<![endif]-->

<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#f0f3f8">
    <tr>
        <td align="center" valign="top">
            <!--[if mso]>
            <table width="600" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td>
            <![endif]-->
            
            <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" class="main">
                <tr>
                    <td height="60" align="center" valign="top" style="font-size:60px; line-height:60px;">&nbsp;</td>
                </tr>
                <tr>
                    <td align="center" valign="top" style="background:#FFF;">
                        <a href="{{env('APP_URL')}}">
                            <!--[if mso]>
                            <img src="{{env('APP_URL')}}/img/logo.png" alt="{{env('APP_NAME')}}" width="480" style="max-width: 480px; width: 100%; height: auto; display: block; border: 0;">
                            <![endif]-->
                            <!--[if !mso]><!-->
                            <img src="{{asset('img/logo.png')}}" class="logo" width="480" alt="{{env('APP_NAME')}}" style="max-width: 480px !important; height: auto !important; display: block !important; margin: 0 auto !important; border: 0; outline: none;" />
                            <!--<![endif]-->
                        </a>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="top" style="background:#FFF;">
                        <table width="350" border="0" cellspacing="0" cellpadding="0" class="two-left-inner">
                            <tr>
                                <td height="80" align="center" valign="top" style="font-size:80px; line-height:80px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">
                                    <table width="350" border="0" align="center" cellpadding="0" cellspacing="0" class="two-left-inner">
                                        <tr>
                                            <td align="left" valign="top">Hallo {{$client->getName()}},</td>
                                        </tr>
                                        <tr>
                                            <td height="20" align="center" valign="top" style="font-size:10px; line-height:10px;">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td align="left" valign="top">Heb je genoten van de maaltijd? Deel je beoordeling met ons.</td>
                                        </tr>
                                        <tr>
                                            <td height="20" align="center" valign="top" style="font-size:10px; line-height:10px;">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td align="left" valign="top">
                                                <div style="width: 100%; text-align: center; float: left;">
                                                    <div class="rating" style="text-align: center; margin: 0; font-size: 50px; width: 275px; margin: 0 auto; margin-top: 10px;">
                                                        <table style="border-collapse: collapse;border-spacing: 0;width: 275px; margin: 0 auto; font-size: 50px; direction: rtl;" dir="rtl">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="padding: 0;vertical-align: top;" width="55" class="star-wrapper" lang="x-star-wrapper">
                                                                        <div style="display: block; text-align: center; float: left;width: 55px;overflow: hidden;line-height: 60px;">
                                                                            <a href="{{$url}}?rating=5" class="star" target="_blank" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="1">
                                                                                <div lang="x-empty-star" style="margin: 0;display: inline-block;">☆</div>
                                                                                <div lang="x-full-star" style="margin: 0;display: inline-block; width:0; overflow:hidden;float:left; display:none; height: 0; max-height: 0;">★</div>
                                                                            </a>
                                                                        </div>
                                                                    </td>
                                                                    <td style="padding: 0;vertical-align: top" width="55" class="star-wrapper" lang="x-star-wrapper">
                                                                        <div style="display: block; text-align: center; float: left;width: 55px;overflow: hidden;line-height: 60px;">
                                                                            <a href="{{$url}}?rating=4" class="star" target="_blank" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="2">
                                                                                <div lang="x-empty-star" style="margin: 0;display: inline-block;">☆</div>
                                                                                <div lang="x-full-star" style="margin: 0;display: inline-block; width:0; overflow:hidden;float:left; display:none; height: 0; max-height: 0;">★</div>
                                                                            </a>
                                                                        </div>
                                                                    </td>
                                                                    <td style="padding: 0;vertical-align: top" width="55" class="star-wrapper" lang="x-star-wrapper">
                                                                        <div style="display: block; text-align: center; float: left;width: 55px;overflow: hidden;line-height: 60px;">
                                                                            <a href="{{$url}}?rating=3" class="star" target="_blank" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="3">
                                                                                <div lang="x-empty-star" style="margin: 0;display: inline-block;">☆</div>
                                                                                <div lang="x-full-star" style="margin: 0;display: inline-block; width:0; overflow:hidden;float:left; display:none; height: 0; max-height: 0;">★</div>
                                                                            </a>
                                                                        </div>
                                                                    </td>
                                                                    <td style="padding: 0;vertical-align: top" width="55" class="star-wrapper" lang="x-star-wrapper">
                                                                        <div style="display: block; text-align: center; float: left;width: 55px;overflow: hidden;line-height: 60px;">
                                                                            <a href="{{$url}}?rating=2" class="star" target="_blank" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="4">
                                                                                <div lang="x-empty-star" style="margin: 0;display: inline-block;">☆</div>
                                                                                <div lang="x-full-star" style="margin: 0;display: inline-block; width:0; overflow:hidden;float:left; display:none; height: 0; max-height: 0;">★</div>
                                                                            </a>
                                                                        </div>
                                                                    </td>
                                                                    <td style="padding: 0;vertical-align: top" width="55" class="star-wrapper" lang="x-star-wrapper">
                                                                        <div style="display: block; text-align: center; float: left;width: 55px;overflow: hidden;line-height: 60px;">
                                                                            <a href="{{$url}}?rating=1" class="star" target="_blank" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="5">
                                                                                <div lang="x-empty-star" style="margin: 0;display: inline-block;">☆</div>
                                                                                <div lang="x-full-star" style="margin: 0;display: inline-block; width:0; overflow:hidden;float:left; display:none; height: 0; max-height: 0;">★</div>
                                                                            </a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="20" align="center" valign="top" style="font-size:10px; line-height:10px;">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td class="signature">Met vriendelijke groet,<br>Het team van DeBurenKoken.nl</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr style="background:#FFF;">
                    <td align="center" valign="top">
                        <table width="260" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <td height="60" align="center" valign="top" style="font-size:60px; line-height:60px;">&nbsp;</td>
                            </tr>
                            @if(env('SOCIAL_FACEBOOK') || env('SOCIAL_INSTAGRAM') || env('SOCIAL_TWITTER'))
                            <tr>
                                <td align="center" valign="top">
                                    <table width="85" border="0" align="center" cellpadding="0" cellspacing="0" class="social-icons">
                                        <tr>
                                            @if(env('SOCIAL_FACEBOOK'))
                                            <td align="center" valign="top">
                                                <a href="{{env('SOCIAL_FACEBOOK')}}">
                                                    <!--[if mso]>
                                                    <img src="{{asset('img/facebook.png')}}" width="22" height="22" alt="Facebook" style="width: 22px !important; height: 22px !important; max-width: 22px !important; max-height: 22px !important; border: 0;" />
                                                    <![endif]-->
                                                    <!--[if !mso]><!-->
                                                    <img src="{{asset('img/facebook.png')}}" width="22" height="22" alt="" />
                                                    <!--<![endif]-->
                                                </a>
                                            </td>
                                            @endif
                                            @if(env('SOCIAL_TWITTER'))
                                            <td align="center" valign="top">
                                                <a href="{{env('SOCIAL_TWITTER')}}">
                                                    <!--[if mso]>
                                                    <img src="{{asset('img/twitter.png')}}" width="22" height="22" alt="Twitter" style="width: 22px !important; height: 22px !important; max-width: 22px !important; max-height: 22px !important; border: 0;" />
                                                    <![endif]-->
                                                    <!--[if !mso]><!-->
                                                    <img src="{{asset('img/twitter.png')}}" width="22" height="22" alt="" />
                                                    <!--<![endif]-->
                                                </a>
                                            </td>
                                            @endif
                                            @if(env('SOCIAL_INSTAGRAM'))
                                            <td align="center" valign="top">
                                                <a href="{{env('SOCIAL_INSTAGRAM')}}">
                                                    <!--[if mso]>
                                                    <img src="{{asset('img/instagram.png')}}" width="22" height="22" alt="Instagram" style="width: 22px !important; height: 22px !important; max-width: 22px !important; max-height: 22px !important; border: 0;" />
                                                    <![endif]-->
                                                    <!--[if !mso]><!-->
                                                    <img src="{{asset('img/instagram.png')}}" width="22" height="22" alt="" />
                                                    <!--<![endif]-->
                                                </a>
                                            </td>
                                            @endif
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td height="10" align="center" valign="top" style="font-size:10px; line-height:10px;">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td height="55" align="center" valign="top" style="font-size:55px; line-height:55px;">&nbsp;</td>
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
</body>
</html>