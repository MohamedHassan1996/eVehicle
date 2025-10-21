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
//     public function export(Request $request)
// {
//     // Leggo parametri senza obbligare a usare "filter"
//     $vehicleIds = isset($request->filter['vehicleId']) ? explode(',', $request->filter['vehicleId']) : [];
//     $startAt = isset($request->filter['startAt']) ? $request->filter['startAt']: null;
//     $endAt = isset($request->filter['endAt']) ? $request->filter['endAt']: null;
//     $company = isset($request->filter['company']) ? $request->filter['company']: null;
//     $vehicleLogIds = isset($request->filter['vehicleLogIds']) ? explode(',', $request->filter['vehicleLogIds']) : [];


//     $logs = VehicleLog::with('vehicle')
//     ->where('weight_type', 1)
//         ->when(!empty($vehicleIds), function ($query) use ($vehicleIds) {
//             return $query->whereIn('vehicle_id', $vehicleIds);
//         })
//         ->when(!empty($vehicleLogIds), function ($query) use ($vehicleLogIds) {
//             return $query->whereIn('id', $vehicleLogIds);
//         })
//         ->when(!empty($company), function ($query) use ($company) {
//             return $query->whereHas('vehicle', function ($q) use ($company) {
//                 $q->where('company_name', $company);
//             });
//         })
//         ->when($startAt && $endAt, function ($query) use ($startAt, $endAt) {
//             return $query->whereBetween('date', [
//                 Carbon::parse($startAt)->startOfDay(),
//                 Carbon::parse($endAt)->endOfDay()
//             ]);
//         })
//         ->when($startAt && !$endAt, function ($query) use ($startAt) {
//             return $query->where('date', '>=', Carbon::parse($startAt)->startOfDay());
//         })
//         ->when(!$startAt && $endAt, function ($query) use ($endAt) {
//             return $query->where('date', '<=', Carbon::parse($endAt)->endOfDay());
//         })
//          ->orderBy('date', 'ASC')
//         ->get();

//     // Se nessun dato, restituisco messaggio
//     if ($logs->isEmpty()) {
//         return response()->json([
//             'message' => 'Nessun dato trovato per i filtri selezionati.'
//         ], 404);
//     }

//     // Genero il PDF
//     $pdf = Pdf::loadView('pdf.vehicle_logs', [
//         'logs' => $logs
//     ]);

//     $fileName = 'logs_' . Carbon::now()->format('Ymd_His') . '.pdf';
//     $filePath = 'exports/' . $fileName;

//     Storage::disk('public')->put($filePath, $pdf->output());
//     $url = Storage::url($filePath);

//     return response()->json([
//         'message' => 'PDF generato con successo',
//         'url' => asset($url)
//     ]);
// }

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
                  ->whereNull('deleted_at'); // ensure vehicle not deleted
            });
        })
        ->when(!$company, function ($q) {
            // still ensure vehicle not deleted even if company not filtered
            $q->whereHas('vehicle', fn($v) => $v->whereNull('deleted_at'));
        })
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

    // --- Generate PDF ---
    try {
        $pdf = Pdf::loadView('pdf.vehicle_logs', ['logs' => $logs]);

        $fileName = 'logs_' . Carbon::now()->format('Ymd_His') . '.pdf';
        $filePath = 'exports/' . $fileName;

        Storage::disk('public')->put($filePath, $pdf->output());
        $url = Storage::url($filePath);

        return response()->json([
            'message' => 'PDF generato con successo',
            'url' => asset($url)
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'Errore durante la generazione del PDF.',
            'error' => $e->getMessage()
        ], 500);
    }
}



}
