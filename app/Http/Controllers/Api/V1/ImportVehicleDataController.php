<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class ImportVehicleDataController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        // Load Excel file
        $path = $request->file('file')->getRealPath();
        $data = Excel::toArray([], $path);

        if (empty($data) || count($data[0]) < 2) {
            return response()->json(['message' => 'Excel file is empty or invalid.'], 400);
        }

        $rows = $data[0];
        $header = array_map('trim', $rows[0]); // First row = headers

        // Expected headers
        $expected = ['Date', 'Tractor', 'Tara', 'KG'];
        foreach ($expected as $col) {
            if (!in_array($col, $header)) {
                return response()->json(['message' => "Missing column: $col"], 400);
            }
        }

        // Map header indexes
        $indexes = array_flip($header);

        $firstRow = $rows[1]; // Second row (first record)

        // Create Vehicle
        $vehicle = Vehicle::create([
            'license_plate' => $firstRow[$indexes['Tractor']],
            'company_name' => 'unknown',
            'type' => 'unknown',
        ]);

        // ---- Create first VehicleLog (Tara) ----
        VehicleLog::create([
            'vehicle_id'  => $vehicle->id,
            'weight'      => $firstRow[$indexes['Tara']],
            'weight_type' => 0, // empty weight
            'note'        => '',
            'date'        => Carbon::parse($firstRow[$indexes['Date']])->format('Y-m-d 00:00:00'),
        ]);

        // ---- Create rest VehicleLogs (KG) ----
        for ($i = 0; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (empty($row[$indexes['KG']])) continue;

            VehicleLog::create([
                'vehicle_id'  => $vehicle->id,
                'weight'      => $row[$indexes['KG']],
                'weight_type' => 1, // full load
                'note'        => '',
                'date'        => Carbon::parse($row[$indexes['Date']])->format('Y-m-d 00:00:00'),
            ]);
        }

        return response()->json([
            'message' => 'Vehicle and logs imported successfully.',
            'vehicle' => $vehicle,
        ]);

    }

}
