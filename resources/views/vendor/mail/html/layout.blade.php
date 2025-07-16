<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>{{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }

            :root {
                color-scheme: light dark;
            }

            body {
                background-color: #212743 !important;
            }

            .header,
            .footer {
                text-align: center;
            }

            .header .logo-text {
                color: #ffffff !important;
                font-size: 20px;
                font-weight: bold;
                vertical-align: middle;
            }

            .header .logo {
                margin-right: 12px;
                vertical-align: middle;
            }

            .content-cell,
            p,
            li {
                color: #dcdcdc !important;
            }

            h1,
            h2,
            h3 {
                color: #ffffff !important;
            }

            .subcopy .break-all,
            .subcopy a {
                color: #bbbbbb !important;
            }

            .wrapper,
            .content,
            .preheader,
            .footer {
                background-color: #212743 !important;
                border: none;
            }

            .button-biru-tua {
                background-color: #1A2D7C !important;
                border-color: #1A2D7C !important;
                color: #ffffff !important;
            }
        }
    </style>
    {!! $head ?? '' !!}
</head>

<body>

    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    {!! $header ?? '' !!}

                    <!-- Email Body -->
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0"
                            style="border: hidden !important;">
                            <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0"
                                role="presentation">
                                <!-- Body content -->
                                <tr>
                                    <td class="content-cell">
                                        {!! Illuminate\Mail\Markdown::parse($slot) !!}

                                        {!! $subcopy ?? '' !!}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {!! $footer ?? '' !!}
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
