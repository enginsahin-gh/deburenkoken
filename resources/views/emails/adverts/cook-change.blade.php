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
        table { border-spacing: 0; border-coll apse: collapse;}
        .ReadMsgBody { width: 100%; background-color: #FFFFFF; }
        .ExternalClass { width: 100%; background-color: #FFFFFF; }
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalCl ass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
        a,a:hover { text-decoration:none; color:#FFF;}
        img { display: block !important; }
        table td { border-collapse: collapse; }

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

</head>

<!-- <body yahoo="fix" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#f0f3f8">
    <tr>
        <td align="center" valign="top">
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
                                                    <img src="{{asset('img/logo.png')}}" width="480" alt="{{env('APP_NAME')}}" />
                                                </a>
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
                                                            Hallo {{$username}},
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td height="20" align="center" valign="top" style="font-size:10px; line-height:10px;">&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left" valign="top">
                                                            {{$dishTitle}} is gewijzigd. Je klanten zullen via de mail geinformeerd worden.
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
                                                                <img src="{{asset('img/facebook.png')}}" width="22" height="22" alt="" />
                                                            </a>
                                                        </td>
                                                        @endif
                                                        @if(env('SOCIAL_TWITTER'))
                                                        <td align="center" valign="top">
                                                            <a href="{{env('SOCIAL_TWITTER')}}">
                                                                <img src="{{asset('img/twitter.png')}}" width="22" height="22" alt="" />
                                                            </a>
                                                        </td>
                                                        @endif
                                                        @if(env('SOCIAL_INSTAGRAM') )
                                                        <td align="center" valign="top">
                                                            <a href="{{env('SOCIAL_INSTAGRAM')}}">
                                                                <img src="{{asset('img/instagram.png')}}" width="22" height="22" alt="" />
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
</table> -->
<!--Main Table End-->
</body>
</html>
