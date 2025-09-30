<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tractor Logs Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }
        table th {
            background-color: #f0f0f0;
        }

        /* ✅ Force page break after each log */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    @foreach ($logs as $log)
        <div class="log-page">
            <h2>Tractor Load Report</h2>

            <p><strong>Targa:</strong> {{ $log->vehicle->license_plate }}</p>
            <p><strong>Data:</strong> {{ now()->format('d/m/y') }}</p>
            <p><strong>Ora:</strong> {{ now()->format('H:i') }}</p>

            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Data</th>
                        <th>Ora</th>
                        <th>Total Weight</th>
                        <th>Empty Weight</th>
                        <th>Difference</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>{{ \Carbon\Carbon::parse($log->date)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($log->date)->format('H:i') }}</td>
                        <td>{{ $log->weight }}</td>
                        <td>
                            @php
                                $emptyWeight = $log->vehicle->lastestEmptyVehicleWeight;
                                echo $emptyWeight;
                            @endphp
                        </td>
                        <td>{{ $log->weight - $emptyWeight }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- ✅ Add a page break between logs (except after the last one) --}}
        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

</body>
</html>
