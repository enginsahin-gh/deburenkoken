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
        body{ margin:0; padding:0; -webkit-text-size-adjust: none; -ms-text-size-adjust: none; background:#f0f3f8;}
        span.preheader{display: none; font-size: 1px;}
        html { width: 100%; }
        table { border-spacing: 0; border-collapse: collapse;}
        .ReadMsgBody { width: 100%; background-color: #FFFFFF; }
        .ExternalClass { width: 100%; background-color: #FFFFFF; }
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
        a,a:hover { text-decoration:none; color:#FFF;}
        img { display: block !important; }
        table td { border-collapse: collapse; }

        /* Outlook fixes */
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            outline: none;
            text-decoration: none;
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
        .logo {
            max-width: 200px !important;
            height: auto !important;
            margin: 30px auto !important;
            display: block !important;
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
        .button-container {
            text-align: center !important;
        }
        .button-table {
            margin: 0 auto !important;
        }
        .button-link {
            padding: 10px 20px !important;
            border-radius: 6px !important;
            mso-border-radius-alt: 6px !important;
            line-height: 20px !important;
            background-color: #f3723b !important;
            color: #ffffff !important;
            text-decoration: none !important;
            font-weight: bold !important;
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
            
            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" valign="top">
                        <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="main">
                            <tr>
                                <td height="60" align="center" valign="top" style="font-size:60px; line-height:60px;">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" valign="top">
                        <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="main">
                        </table>
                    </td>
                </tr>
            </table>

            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" valign="top">
                        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center" valign="top">
                                    <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="main">
                                        <tr>
                                            <td align="center" valign="top" style="background:#FFF;">
                                            <a href="{{env('APP_URL')}}">
                                                <!--[if mso]>
                                                <img src="{{env('APP_URL')}}/img/logo.png" alt="{{env('APP_NAME')}}" width="200" style="max-width: 200px; width: 100%; height: auto; display: block; border: 0;">
                                                <![endif]-->
                                                <!--[if !mso]><!-->
                                                <img src="{{asset('img/logo.png')}}"  class="logo" width="480" alt="{{env('APP_NAME')}}" style="max-width: 200px !important; height: auto !important; margin: 30px auto !important; display: block !important; border: 0; outline: none;" />
                                                <!--<![endif]-->
                                                </a>
                                                <div class="section-divider"></div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="main">
                            <tr>
                                <td align="center" valign="top" style="background:#FFF;">
                                    <table width="500" border="0" cellspacing="0" cellpadding="0" class="two-left-inner">
                                        <tr>
                                            <td height="80" align="center" valign="top" style="font-size:80px; line-height:80px;">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td align="center" valign="top">
                                                <table width="350" border="0" align="center" cellpadding="0" cellspacing="0" class="two-left-inner">
                                                    <tr>
                                                        <td align="left" valign="top">
                                                            Hallo {{$client->getName()}},
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td height="20" align="center" valign="top" style="font-size:10px; line-height:10px;">&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left" valign="top">
                                                            Helaas moeten wij je mededelen dat onderstaande bestelling is gewijzigd.
                                                            De Thuiskok heeft een boodschap achtergelaten.
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td align="left" valign="top" style="font-family:'Open Sans', sans-serif, Verdana; font-size:14px; color:#000000; line-height:25px; font-weight:normal;">
                                                            Betreft bestelling:
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left" valign="top" style="font-family:'Open Sans', sans-serif, Verdana; font-size:12px; color:#000000; line-height:25px; font-weight:normal;">
                                                            <li style="list-style-type: disc;">Bestelnummer: {{$order?->getParsedUuid()}}</li>
                                                            <li style="list-style-type: disc;">ID gerecht: {{$dish?->getParsedUuid()}}</li>
                                                            <li style="list-style-type: disc;">Gerechtnaam: {{$dish?->getTitle()}}</li>
                                                            <li style="list-style-type: disc;">{{$order?->getParsedAdvertUuid()}}</li>
                                                            <li style="list-style-type: disc;">Aantal porties: {{$order?->getPortionAmount()}}</li>
                                                            <li style="list-style-type: disc;">Afhaalmoment: {{$dish->advert?->getPickupDate()}} {{$dish->advert?->getPickupFrom()}} {{$dish->advert?->getPickupTo()}}</li>
                                                            <li style="list-style-type: disc;">Verwachte afhaalmoment: {{$order->getExpectedPickupTime()}}</li>
                                                            <li style="list-style-type: disc;">Opmerking: {{$order->getRemarks()}}</li>
                                                            <br>
                                                            <li style="list-style-type: disc;">Thuiskoknaam: {{$dish->cook?->user->getUsername()}}</li>
                                                            <li style="list-style-type: disc;">Thuiskok email: {{$dish->cook?->user->getEmail()}}</li>
                                                            <li style="list-style-type: disc;">Thuiskok Telnr: {{$dish->cook?->user->userProfile->getPhoneNumber()}}</li>
                                                            <li style="list-style-type: disc;">Thuiskok adres: {{$dish->cook?->getStreet()}} {{$dish->cook?->getHouseNumber()}}{{$dish->cook?->getAddition()}} {{$dish?->cook?->getCity()}}</li>
                                                            <br>
                                                            <li style="list-style-type: disc;">Boodschap {{$dish->cook?->user?->getUsername()}}: {{$text}}</li>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td height="20" align="center" valign="top" style="font-size:10px; line-height:10px;">&nbsp;</td>
                                                    </tr>

                                                    <tr>
                                                        <td align="left" valign="top" style="font-family:'Open Sans', sans-serif, Verdana; font-size:14px; color:#000000; line-height:25px; font-weight:normal;">
                                                            Wil je aan de hand van deze wijziging je bestelling annuleren? Klik dan op de knop hieronder.
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left" valign="top">
                                                            <!--[if mso]>
                                                            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{$link}}" style="height:40px;v-text-anchor:middle;width:180px;" arcsize="15%" strokecolor="#f3723b" strokeweight="0pt" fillcolor="#f3723b">
                                                              <w:anchorlock/>
                                                              <center style="color:#ffffff;font-family:Arial, Helvetica, sans-serif;font-size:14px;font-weight:bold;">Annuleer bestelling</center>
                                                            </v:roundrect>
                                                            <![endif]-->
                                                            <!--[if !mso]><!-->
                                                            <a href="{{$link}}" target="_blank" class="orange-button" >Annuleer bestelling</a>
                                                            <!--<![endif]-->
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td height="20" align="center" valign="top" style="font-size:10px; line-height:10px;">&nbsp;</td>
                                                    </tr>

                                                    <tr>
                                                        <td align="left" valign="top" style="font-family:'Open Sans', sans-serif, Verdana; font-size:14px; color:#000000; line-height:25px; font-weight:normal;">
                                                            Met vriendelijke groet,
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left" valign="top" style="font-family:'Open Sans', sans-serif, Verdana; font-size:14px; color:#000000; line-height:25px; font-weight:normal;">
                                                            DeBurenKoken.nl
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" valign="top" >
                        <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="main">
                            <tr style="background:#FFF;">
                                <td align="center" valign="top" >
                                    <table width="260" border="0" align="center" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td height="60" align="center" valign="top" style="font-size:60px; line-height:60px;">&nbsp;</td>
                                        </tr>
                                        @if(env('SOCIAL_FACEBOOK') || env('SOCIAL_INSTAGRAM') || env('SOCIAL_TWITTER'))
                                         <tr>
                                            <td align="center" valign="top">
                                                <table width="85" border="0" align="center" cellpadding="0" cellspacing="0">
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
                                                        @if(env('SOCIAL_INSTAGRAM') )
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
                        </table>
                    </td>
                </tr>
            </table>
            
            <!--[if mso]>
                    </td>
                </tr>
            </table>
            <![endif]-->
            
            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" valign="top">
                        <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="main">
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
            </div>
        </td>
    </tr>
</table>
<![endif]-->

<!--Main Table End-->
</body>
</html>