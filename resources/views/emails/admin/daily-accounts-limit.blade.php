<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <title>Account Limiet Waarschuwing {{env('APP_NAME')}}</title>
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
        table td { border-collapse: collapse; padding: 10px; }
        th { background-color: #f5f5f5; padding: 10px; text-align: left; }
    </style>
</head>
<body yahoo="fix" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#f0f3f8">
        <tr>
            <td align="center" valign="top">
                <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="main">
                    <tr>
                        <td align="center" valign="top" style="background:#FFF;">
                            <table width="500" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td height="40">&nbsp;</td>
                                </tr>
                                <!-- <tr>
                                    <td align="center">
                                        <img src="{{asset('img/logo.png')}}" width="480" alt="{{env('APP_NAME')}}" />
                                    </td>
                                </tr> -->
                                <tr>
                                    <td height="40">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="left" style="font-family:'Open Sans',sans-serif; font-size:14px; color:#000;">
                                        <p>Hallo Administrator,</p>

                                        <p>Er is vandaag het maximaal aantal accounts ({{$limit}}) bereikt.</p>

                                        <p>Details:</p>
                                        <ul>
                                            <li>Datum: {{$date}}</li>
                                            <li>Aantal aangemaakte accounts: {{$count}}</li>
                                            <li>Account limiet: {{$limit}}</li>
                                        </ul>

                                        <p>Hieronder vindt u een overzicht van de thuiskokken:</p>
                                        <table width="100%" style="border-collapse: collapse; border: 1px solid #ddd;">
                                            <thead>
                                                <tr>
                                                    <th>Thuiskoknaam</th>
                                                    <th>E-mailverificatie voltooid?</th>
                                                    <th>IBAN verificatie voltooid?</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @if($users->isEmpty())
                                                <tr>
                                                    <td colspan="3" style="text-align: center;">No accounts created today</td>
                                                </tr>
                                            @else
                                                @foreach($users as $user)
                                                <tr>
                                                    <td>{{ $user->username }}</td>
                                                    <td>{{ $user->email_verified_at ? 'Ja' : 'Nee' }}</td>
                                                    <td>{{ $user->banking && !empty($user->banking->iban) ? 'Ja' : 'Nee' }}</td>
                                                </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>

                                        <p>Met vriendelijke groet,<br>
                                        DeBurenKoken.nl</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="40">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>