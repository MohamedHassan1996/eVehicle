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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;


class VehiclePdfExportController extends Controller
{
    public function export(Request $request)
    {

        $request->validate([
            'vehicleLogIds' => 'required', // license_plate (Tractor)
        ]);

        // Build query for logs
        $logs = VehicleLog::whereIn('id' ,$request->vehicleLogIds)->with('vehicle')->get();

        //$vehicle = Vehicle::where('id', $log->vehicle_id)->first();

        //$vehicleEmptyWeight = VehicleLog::where('vehicle_id', $vehicle->id)->where('weight_type', 0)->first()?->weight??0;

        // Generate PDF
        $pdf = Pdf::loadView('pdf.vehicle_logs', [
            'logs' => $logs,
        ]);

        // Return as downloadable file
        // ✅ Define filename and save path
        $fileName =  'logs_' . Carbon::now()->format('Ymd_His') . '.pdf';
        $filePath = 'exports/' . $fileName;

        // ✅ Save the file to `storage/app/public/exports`
        Storage::disk('public')->put($filePath, $pdf->output());

        // ✅ Generate public URL
        $url = Storage::url($filePath);

        // ✅ Return the URL as JSON
        return response()->json([
            'message' => 'PDF exported successfully',
            'url' => asset($url)
        ]);

    }

}
