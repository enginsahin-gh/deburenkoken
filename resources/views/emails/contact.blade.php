<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
  <title>Contactformulier {{ env('APP_NAME') }}</title>
  <link href="http://fonts.googleapis.com/css?family=Montserrat:400,500,300,600,700" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Condiment" rel="stylesheet">

  <style type="text/css">
    /* Reset en basis styling */
    body {
      margin: 0;
      padding: 0;
      -webkit-text-size-adjust: none;
      -ms-text-size-adjust: none;
      background: #f0f3f8;
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

    span.preheader { display: none; font-size: 1px; }
    html { width: 100%; }
    table { border-spacing: 0; border-collapse: collapse; }
    .ReadMsgBody { width: 100%; background-color: #FFFFFF; }
    .ExternalClass { width: 100%; background-color: #FFFFFF; }
    .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
    a, a:hover { text-decoration: none; color: #FFF; }
    img { display: block !important; }
    table td { border-collapse: collapse; }

    @media only screen and (max-width:640px) {
      body { width: auto !important; }
      table[class=main] { width: 85% !important; }
      table[class=full] { width: 100% !important; margin: 0px auto; }
      table[class=two-left-inner] { width: 90% !important; margin: 0px auto; }
      td[class="two-left"] { display: block; width: 100% !important; }
      table[class=menu-icon] { display: none; }
      img[class="image-full"] { width: 100% !important; }
    }

    @media only screen and (max-width:479px) {
      body { width: auto !important; }
      table[class=main] { width: 93% !important; }
      table[class=full] { width: 100% !important; margin: 0px auto; }
      td[class="two-left"] { display: block; width: 100% !important; }
      table[class=two-left-inner] { width: 90% !important; margin: 0px auto; }
      table[class=menu-icon] { display: none; }
      img[class="image-full"] { width: 100% !important; }
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

    .content-container {
      padding: 0 40px;
      text-align: left;
    }
    /* Consistente tekststijl */
    .content-container p {
      font-family: 'Open Sans', sans-serif;
      font-size: 14px;
      color: #000000;
      line-height: 25px;
      margin: 0 0 20px 0;
    }

    .signature {
      margin-top: 0px;
      padding-top: 0px;
      padding-left: 40px; 
      padding-right: 40px; 
      font-family: 'Open Sans', sans-serif;
      font-size: 14px;
      line-height: 25px;
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

  <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#f0f3f8">
    <tr>
      <td align="center" valign="top">
        <!--[if mso]>
        <table width="600" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td>
        <![endif]-->

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

        <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td align="center" valign="top">
              <table width="500" border="0" align="center" cellpadding="0" cellspacing="0" class="main">
                <tr>
                  <td align="center" valign="top" style="background:#FFF;">
                    <a href="{{ env('APP_URL') }}">
                      <!--[if mso]>
                      <img src="{{env('APP_URL')}}/img/logo.png" alt="{{ env('APP_NAME') }}" width="200" style="max-width: 200px; width: 100%; height: auto; display: block; border: 0;">
                      <![endif]-->
                      <!--[if !mso]><!-->
                      <img src="{{ asset('img/logo.png') }}" class="logo" width="480" alt="{{ env('APP_NAME') }}" style="max-width: 200px !important; height: auto !important; margin: 30px auto !important; display: block !important; border: 0; outline: none;" />
                      <!--<![endif]-->
                    </a>
                  </td>
                </tr>
                <tr>
                  <td align="left" valign="top" style="background:#FFF;">
                    <div class="content-container">
                      <!-- Aanhef -->
                      <p>
                        Hallo {{ isset($name) ? $name : '[Klantnaam]' }},
                      </p>
                      <!-- Extra ruimte tussen aanhef en vervolgtekst -->
                      <p>
                        @if(isset($admin))
                          {{ $name }} heeft het contactformulier ingevuld op de website.
                        @else
                          Wij hebben je vraag ontvangen en streven ernaar om binnen 3 werkdagen te reageren.
                        @endif
                      </p>
                      @if(isset($admin))
                        @if(isset($email))
                          <p>
                            Met het volgende e-mailadres: 
                            <a href="mailto:{{ $email }}" style="color:black;">{{ $email }}</a>
                          </p>
                        @endif
                        @if(isset($phone))
                          <p>
                            Met het volgende telefoonnummer: 
                            <a href="tel:{{ $phone }}" style="color:black;">{{ $phone }}</a>
                          </p>
                        @endif
                      @endif
                      @if (isset($admin))
                        <p>
                          Ingevuld op het formulier:
                        </p>
                        <p style="font-weight:normal;">
                          {{ $msg }}
                        </p>
                      @endif
                    </div>
                  </td>
                </tr>
                <tr>
                  <td align="left" valign="top" style="background:#FFF;">
                    <div class="signature">
                      Met vriendelijke groet,<br>
                      Het team van DeBurenKoken.nl
                    </div>
                  </td>
                </tr>
                <tr>
                  <td align="center" valign="top" style="background:#FFF; padding: 20px;">
                    @if(env('SOCIAL_FACEBOOK') || env('SOCIAL_INSTAGRAM') || env('SOCIAL_TWITTER'))
                      <table width="85" border="0" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                          @if(env('SOCIAL_FACEBOOK'))
                            <td align="center" valign="top">
                              <a href="{{ env('SOCIAL_FACEBOOK') }}">
                                <!--[if mso]>
                                <img src="{{ asset('img/facebook.png') }}" width="22" height="22" alt="Facebook" style="width: 22px !important; height: 22px !important; max-width: 22px !important; max-height: 22px !important; border: 0;" />
                                <![endif]-->
                                <!--[if !mso]><!-->
                                <img src="{{ asset('img/facebook.png') }}" width="22" height="22" alt="" />
                                <!--<![endif]-->
                              </a>
                            </td>
                          @endif
                          @if(env('SOCIAL_TWITTER'))
                            <td align="center" valign="top">
                              <a href="{{ env('SOCIAL_TWITTER') }}">
                                <!--[if mso]>
                                <img src="{{ asset('img/twitter.png') }}" width="22" height="22" alt="Twitter" style="width: 22px !important; height: 22px !important; max-width: 22px !important; max-height: 22px !important; border: 0;" />
                                <![endif]-->
                                <!--[if !mso]><!-->
                                <img src="{{ asset('img/twitter.png') }}" width="22" height="22" alt="" />
                                <!--<![endif]-->
                              </a>
                            </td>
                          @endif
                          @if(env('SOCIAL_INSTAGRAM'))
                            <td align="center" valign="top">
                              <a href="{{ env('SOCIAL_INSTAGRAM') }}">
                                <!--[if mso]>
                                <img src="{{ asset('img/instagram.png') }}" width="22" height="22" alt="Instagram" style="width: 22px !important; height: 22px !important; max-width: 22px !important; max-height: 22px !important; border: 0;" />
                                <![endif]-->
                                <!--[if !mso]><!-->
                                <img src="{{ asset('img/instagram.png') }}" width="22" height="22" alt="" />
                                <!--<![endif]-->
                              </a>
                            </td>
                          @endif
                        </tr>
                      </table>
                    @endif
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>

        <table width="85%" border="0" align="center" cellpadding="0" cellspacing="0">
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