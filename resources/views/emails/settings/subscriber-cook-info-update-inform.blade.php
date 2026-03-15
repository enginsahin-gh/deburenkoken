<!DOCTYPE html>
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

        .order-section {
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
            margin: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: relative;
        }
        
        .section-title {
            color: #ff6b00 !important;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 15px;
            border-bottom: 2px solid #ff6b00;
            padding-bottom: 8px;
            text-align: left;
        }
        
        .order-info {
            position: relative;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        
        .label {
            color: #2d3748 !important;
            font-weight: 600;
            width: 150px;
            text-align: left;
            flex-shrink: 0;
        }
        
        .value {
            color: #2d3748 !important;
            text-align: left;
            position: absolute;
            left: 200px;
        }
        
        .logo {  max-width: 200px;
            height: auto;
            margin: 30px 0;
            display: block;
            margin-left: auto;
            margin-right: auto;
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
         
        .greeting {
            font-size: 16px;
            margin-bottom: 0;
            color: #2d3748 !important;
            text-align: left;
        }
        
        .signature {
            margin-top: 20px;
            padding-top: 20px;
            color: #2d3748 !important;
            text-align: left;
        }
        
        .info-list {
            list-style-type: disc;
            margin-left: 20px;
            padding-left: 0;
        }
        
        .info-list li {
            margin-bottom: 5px;
            color: #2d3748 !important;
            font-size: 15px !important;
            line-height: 1.6;
            text-align: left;
        }
        
        @media only screen and (max-width: 640px) {
            .content-wrapper {
                padding: 20px;
            }
            
            .info-row {
                flex-direction: column;
            }
            
            .value {
                position: relative;
                left: 0;
            }
            
            .label {
                width: auto;
                margin-bottom: 5px;
            }
        }
    </style>

    <!--[if mso]>
    <style type="text/css">
        body, table, td, p, a, span {
            font-family: Arial, Helvetica, sans-serif !important;
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
                            <a href="{{env('APP_URL')}}">
                                <!--[if mso]>
                                <img src="{{env('APP_URL')}}/img/logo.png" alt="{{env('APP_NAME')}}" width="200" style="max-width: 200px; width: 100%; height: auto; display: block; border: 0;">
                                <![endif]-->
                                <!--[if !mso]><!-->
                                <img src="{{asset('img/logo.png')}}" alt="{{env('APP_NAME')}}" class="logo" style="max-width: 200px; height: auto; margin: 30px 0; display: block; margin-left: auto; margin-right: auto; border: 0; outline: none;"/>
                                <!--<![endif]-->
                            </a>
                        </div>
                        
                        <div class="greeting" style="margin-bottom: 5%">
                            Hallo {{ $client->name }},
                            <br><br>
                            Hierbij informeren we je over een wijziging die {{ $user->getUsername() }} heeft doorgevoerd.
                        </div>
                        
                        <div class="section-title">Wijzigingen</div>
                        <div class="order-info">
                            <ul class="info-list">
                                <li>Nieuwe emailadres: {{$user->getEmail()}}</li>
                                <li>Nieuwe Telefoonnummer: {{$user->userProfile->getPhoneNumber()}}</li>
                            </ul>
                        </div>
                        
                        <div class="signature">
                            Met vriendelijke groet,
                            <br>
                            Het team van DeBurenKoken.nl
                        </div>
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