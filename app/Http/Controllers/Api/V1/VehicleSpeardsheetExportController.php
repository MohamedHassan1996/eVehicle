<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Exports\VehicleLogsExport; // 👈 Create this export class

class VehicleSpeardsheetExportController extends Controller
{
    public function export(Request $request)
    {
        $filters = $request->input('filter', []);

        $vehicleIds = !empty($filters['vehicleId']) ? explode(',', $filters['vehicleId']) : [];
        $vehicleLogIds = !empty($filters['vehicleLogIds']) ? explode(',', $filters['vehicleLogIds']) : [];
        $startAt = $filters['startAt'] ?? null;
        $endAt = $filters['endAt'] ?? null;
        $company = $filters['company'] ?? null;

        // --- Query logs safely ---
        $logs = VehicleLog::with('vehicle')
            ->where('weight_type', 1)
            ->when($vehicleIds, fn($q) => $q->whereIn('vehicle_id', $vehicleIds))
            ->when($vehicleLogIds, fn($q) => $q->whereIn('id', $vehicleLogIds))
            ->when($company, function ($q) use ($company) {
                $q->whereHas('vehicle', function ($v) use ($company) {
                    $v->where('company_name', $company)
                      ->whereNull('deleted_at');
                });
            })
            ->when(!$company, fn($q) => $q->whereHas('vehicle', fn($v) => $v->whereNull('deleted_at')))
            ->when($startAt && $endAt, function ($q) use ($startAt, $endAt) {
                $q->whereBetween('date', [
                    Carbon::parse($startAt)->startOfDay(),
                    Carbon::parse($endAt)->endOfDay()
                ]);
            })
            ->when($startAt && !$endAt, fn($q) => $q->where('date', '>=', Carbon::parse($startAt)->startOfDay()))
            ->when(!$startAt && $endAt, fn($q) => $q->where('date', '<=', Carbon::parse($endAt)->endOfDay()))
            ->orderBy('date', 'asc')
            ->get();

        // --- Handle empty data ---
        if ($logs->isEmpty()) {
            return response()->json([
                'message' => 'Nessun dato trovato per i filtri selezionati.'
            ], 404);
        }

        // --- Generate XLSX ---
        try {
            $fileName = 'logs_' . Carbon::now()->format('Ymd_His') . '.xlsx';
            $filePath = 'exports/' . $fileName;

            // Store XLSX file in /storage/app/public/exports/
            Excel::store(new VehicleLogsExport($logs), $filePath, 'public');

            $url = Storage::url($filePath);

            return response()->json([
                'message' => 'File Excel generato con successo',
                'url' => asset($url)
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Errore durante la generazione del file Excel.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
