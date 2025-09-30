<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tractor Logs Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            padding: 40px;
        }

        .log-page {
            max-width: 600px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #111;
        }

        .company-subtitle {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .date {
            font-size: 14px;
        }

        .divider {
            border-top: 2px dashed #ccc;
            margin: 30px 0;
        }

        .weight-row {
            display: flex;
            justify-content: space-between;
            margin: 25px 0;
            font-size: 16px;
        }

        .weight-label {
            font-weight: bold;
            text-transform: uppercase;
        }

        .weight-value {
            font-weight: normal;
        }

        .netto-row {
            display: flex;
            justify-content: space-between;
            margin: 25px 0;
            font-size: 16px;
            font-weight: bold;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    @foreach ($logs as $log)
        <div class="log-page">
            <div class="header">
                <div>
                    <div class="company-name">{{ $log->vehicle->company_name }}</div>
                </div>
                <div class="date">
                    Date: {{ \Carbon\Carbon::parse($log->date)->format('d/m/y') }}
                </div>
            </div>

            <div class="divider"></div>

            <div class="weight-row">
                <span class="weight-label">TYPE</span>
                <span class="weight-value">KG</span>
            </div>

            <div class="weight-row">
                <span class="weight-label">PESATA</span>
                <span class="weight-value">{{ $log->weight }}</span>
            </div>

            <div class="weight-row">
                <span class="weight-label">TARA</span>
                <span class="weight-value">
                    @php
                        $emptyWeight = $log->vehicle->lastestEmptyVehicleWeight;
                        echo $emptyWeight;
                    @endphp
                </span>
            </div>

            <div class="divider"></div>

            <div class="netto-row">
                <span>NETTO :</span>
                <span>{{ $log->weight - $emptyWeight }} KG</span>
            </div>
        </div>

        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

</body>
</html>
