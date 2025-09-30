<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tractor Logs Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            padding: 40px 60px;
            color: #333;
        }

        .log-page {
            width: 100%;
        }

        .header-table {
            width: 100%;
            margin-bottom: 50px;
            border: none;
        }

        .header-table td {
            border: none;
            padding: 0;
        }

        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #111;
            line-height: 1.2;
        }

        .company-subtitle {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .date {
            font-size: 15px;
            color: #333;
            text-align: right;
        }

        .divider {
            border-top: 2px dashed #ccc;
            margin: 35px 0;
            height: 0;
        }

        .weight-table {
            width: 100%;
            margin: 35px 0;
            border: none;
        }

        .weight-table td {
            border: none;
            padding: 15px 0;
            font-size: 18px;
        }

        .weight-label {
            font-weight: bold;
            text-transform: uppercase;
            color: #333;
            width: 40%;
            text-align: left;
        }

        .weight-value {
            font-weight: normal;
            color: #333;
            width: 60%;
            text-align: right;
        }

        .netto-label {
            font-weight: bold;
            color: #333;
            width: 40%;
            text-align: left;
        }

        .netto-value {
            font-weight: bold;
            color: #333;
            width: 60%;
            text-align: right;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    @foreach ($logs as $log)
        <div class="log-page">
            <table class="header-table">
                <tr>
                    <td style="width: 50%; vertical-align: top;">
                        <div class="company-name">BBC</div>
                        <div class="company-subtitle">Energy</div>
                    </td>
                    <td style="width: 50%; vertical-align: top;">
                        <div class="date">
                            Date: {{ \Carbon\Carbon::parse($log->date)->format('d/m/y') }}
                        </div>
                    </td>
                </tr>
            </table>

            <div class="divider"></div>

            <table class="weight-table">
                <tr>
                    <td class="weight-label">TYPE</td>
                    <td class="weight-value">KG</td>
                </tr>
                <tr>
                    <td class="weight-label">PESATA</td>
                    <td class="weight-value">{{ $log->weight }}</td>
                </tr>
                <tr>
                    <td class="weight-label">TARA</td>
                    <td class="weight-value">
                        @php
                            $emptyWeight = $log->vehicle->lastestEmptyVehicleWeight;
                            echo $emptyWeight;
                        @endphp
                    </td>
                </tr>
            </table>

            <div class="divider"></div>

            <table class="weight-table">
                <tr>
                    <td class="netto-label">NETTO :</td>
                    <td class="netto-value">{{ $log->weight - $emptyWeight }} KG</td>
                </tr>
            </table>
        </div>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

</body>
</html>
