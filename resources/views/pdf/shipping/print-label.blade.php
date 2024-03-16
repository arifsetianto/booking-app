<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Order #{{ $order->code }}</title>
    <style>
        @font-face {
            font-family: 'Figtree';
            font-weight: normal;
            font-style: normal;
            font-variant: normal;
            src: url({{ storage_path("fonts/Figtree-Regular.ttf") }}) format('truetype');
        }

        @font-face {
            font-family: 'Figtree Bold';
            font-weight: normal;
            font-style: normal;
            font-variant: normal;
            src: url({{ storage_path("fonts/Figtree-Bold.ttf") }}) format('truetype');
        }

        @font-face {
            font-family: 'TH Sarabun';
            font-weight: normal;
            font-style: normal;
            font-variant: normal;
            src: url({{ storage_path("fonts/TH-Sarabun-Bold.ttf") }}) format('truetype');
        }

        body {
            font-family: 'Figtree', sans-serif;
            font-size: 11px;
        }

        .text-bold {
            font-family: 'Figtree Bold', sans-serif;
            font-size: 11px;
        }

        .th-text {
            font-family: 'TH Sarabun', sans-serif;
            font-size: 15px;
        }

        .container {
            background-color: black;
            color: white;
            padding: 12px;
            position: relative;
            width: 40px;
        }

        .text {
            position: absolute;
            top: 2px;
            left: 8px;
            margin: 0;
        }
    </style>
</head>
<body>
<div style="border: 1px solid gray; border-collapse: collapse; padding: 15px;">
    <div style="margin-bottom: 15px;">
        <img src="{{ $image }}" alt="Logo" width="12%">
    </div>
    <table width="100%" style="border: 1px solid gray; border-collapse: collapse;">
        <tr>
            <td width="50%" style="border: 1px solid gray; border-collapse: collapse; vertical-align: top;">
                <div class="container">
                    <p class="text">Sender</p>
                </div>
                <table style="padding: 10px;">
                    <tr>
                        <td style="vertical-align: top; text-align: left;" class="text-bold">Name</td>
                        <td style="vertical-align: top; text-align: left; padding-left: 15px;">Thai Quran Foundation</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; text-align: left;" class="text-bold">Tel</td>
                        <td style="vertical-align: top; text-align: left; padding-left: 15px;">0612873477</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; text-align: left;" class="text-bold">Address</td>
                        <td style="vertical-align: top; text-align: left; padding-left: 15px;">Masjid Jamek (Yameay), 47 Soi Talingchan<br/>Tambon Taladyai, Amphoe Phuket,<br/>Thailand Tambon Talat Yai<br/>Amphur Mueang Phuket Phuket Province<br/><span style="font-size: 13px;">83000</span></td>
                    </tr>
                </table>
            </td>
            <td width="50%" style="border: 1px solid gray; border-collapse: collapse; vertical-align: top;">
                <div class="container">
                    <p class="text">Receiver</p>
                </div>
                <table style="padding: 10px;">
                    <tr>
                        <td style="vertical-align: top; text-align: left;" class="text-bold">Name</td>
                        <td style="vertical-align: top; text-align: left; padding-left: 15px;">{{ $order->shipping->name }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; text-align: left;" class="text-bold">Tel</td>
                        <td style="vertical-align: top; text-align: left; padding-left: 15px;">{{ $order->shipping->phone }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; text-align: left;" class="text-bold">Address</td>
                        <td style="vertical-align: top; text-align: left; padding-left: 15px;">{{ $order->shipping->address }},<br/>{{ $order->shipping->subDistrict->en_name }}, {{ $order->shipping->subDistrict->district->en_name }}, {{ $order->shipping->subDistrict->district->city->en_name }}, <span class="th-text">{{ $order->shipping->subDistrict->district->city->region->name }}</span><br/><span style="font-size: 13px;">{{ $order->shipping->subDistrict->zip_code }}</span></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="50%" style="vertical-align: top; padding: 15px;">
                <table>
                    <tr>
                        <td style="text-align: left;" class="text-bold">Order No.</td>
                        <td style="text-align: left; padding-left: 15px;">{{ $order->code }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;" class="text-bold">Order Date</td>
                        <td style="text-align: left; padding-left: 15px;">{{ $order->confirmed_at->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;" class="text-bold">Shipping</td>
                        <td style="text-align: left; padding-left: 15px;">EMS</td>
                    </tr>
                </table>
            </td>
            <td width="50%" style="vertical-align: top; padding: 15px;">
                &nbsp;
            </td>
        </tr>
    </table>
</div>
</body>
</html>
