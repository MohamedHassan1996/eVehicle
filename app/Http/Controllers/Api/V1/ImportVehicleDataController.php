<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel as ExcelType;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;


class ImportVehicleDataController extends Controller
{
public function import(Request $request)
{
    try {
        DB::beginTransaction();

        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        $data = Excel::toArray([], $request->file('file'));
        if (empty($data) || count($data[0]) < 2) {
            return response()->json(['message' => 'Empty or invalid file'], 400);
        }

        $rows = array_slice($data[0], 1); // skip header
        $grouped = collect($rows)->groupBy(fn($row) => $row[1]); // group by Tractor

        foreach ($grouped as $tractor => $tractorRows) {
            if (empty($tractor)) continue;

            $firstRow = $tractorRows->first();

            // ✅ Create vehicle
            $vehicle = Vehicle::create([
                'license_plate' => 'TRACTOR-' . $tractor,
                'type' => 'Tractor',
                'company_name' => $firstRow[4] ?? 'Unknown',
            ]);

            // ✅ Tara (empty weight)
            VehicleLog::create([
                'vehicle_id'  => $vehicle->id,
                'date'        => $this->parseExcelDate($firstRow[0])?->startOfDay()?->format('Y-m-d H:i:s'),
                'weight'      => $firstRow[2],
                'note'        => '',
                'weight_type' => 0, // Tara
            ]);

            // ✅ Loaded logs (KG)
            foreach ($tractorRows->skip(1) as $row) {
                if (empty($row[0]) || empty($row[3])) continue;

                VehicleLog::create([
                    'vehicle_id'  => $vehicle->id,
                    'date'        => $this->parseExcelDate($row[0])?->startOfDay()?->format('Y-m-d H:i:s'),
                    'weight'      => $row[3],
                    'note'        => '',
                    'weight_type' => 1, // full load
                ]);
            }
        }

        DB::commit();
        return response()->json(['message' => 'All tractors imported successfully']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Error processing file: ' . $e->getMessage()], 500);
    }
}

private function parseExcelDate($value)
{
    if (empty($value)) {
        return null;
    }

    // 1️⃣ If numeric (Excel serial)
    if (is_numeric($value)) {
        try {
            $value = "0".$value;
            return Carbon::instance(ExcelDate::excelToDateTimeObject($value));
        } catch (\Exception $e) {
            return null;
        }
    }

    // 2️⃣ Try known string formats
    $formats = ['m/d/Y', 'n/j/Y', 'Y-m-d', 'd/m/Y', 'd-n-Y'];
    foreach ($formats as $format) {
        try {

            return Carbon::createFromFormat($format, trim($value));
        } catch (\Exception $e) {
            continue;
        }
    }

    return null;
}

}
