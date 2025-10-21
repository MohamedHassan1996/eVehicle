<?php

namespace App\Services\Vehicle;

use App\Filters\Vehicle\FilterVehicle;
use App\Models\Vehicle;
use App\Models\VehicleLog;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Carbon\Carbon;

class VehicleLogService
{

    public function __construct()
    {
    }
    // public function allVehicleLogs()
    // {
    //     $request = request();
    //     $perPage = request()->get('pageSize', 10000);

    //     // $vehicleLogs = QueryBuilder::for(VehicleLog::class)
    //     //     ->allowedFilters([
    //     //         //AllowedFilter::custom('search', new FilterVehicle()),
    //     //         AllowedFilter::exact('vehicleId', 'vehicle_id'),
    //     //     ])
    //         $vehicleIds = isset($request->filter['vehicleId'])?explode(',', $request->filter['vehicleId']): [];

    //         $startAt = $request->filter['startAt'] ?? null;
    //         $endAt = $request->filter['endAt'] ?? null;
    //         $company = $request->filter['company'] ?? null;
    //         $vehicleLogs = VehicleLog::query()
    //         ->when($vehicleIds, function ($query) use ($request, $vehicleIds) {
    //             return $query->whereIn('vehicle_id', $vehicleIds);
    //         })
    //         ->when($company, function ($query) use ($request) {
    //             return $query->whereHas('vehicle', function ($q) use ($request) {
    //                 $q->where('company_name', $request->filter['company']);
    //             });
    //         })
    //         ->when($startAt && $endAt, function ($query) use ($request) {
    //             return $query->whereBetween('date', [Carbon::parse($request->filter['startAt'])->startOfDay(), Carbon::parse($request->filter['endAt'])->endOfDay()]);
    //         })
    //         ->when($startAt && !$endAt, function ($query) use ($request) {
    //             return $query->where('date', '>=', Carbon::parse($request->filter['startAt'])->startOfDay());
    //         })
    //         ->when(!$startAt && $endAt, function ($query) use ($request) {
    //             return $query->where('date', '<=', Carbon::parse($request->filter['endAt'])->endOfDay());
    //         })
    //         ->with('vehicle')
    //         ->orderBy('created_at', 'desc')
    //         ->paginate($perPage);



    //     return $vehicleLogs;
    // }

    public function allVehicleLogs()
{
    $request = request();
    $perPage = $request->get('pageSize', 10000);

    $vehicleIds = isset($request->filter['vehicleId'])
        ? explode(',', $request->filter['vehicleId'])
        : [];

    $startAt = $request->filter['startAt'] ?? null;
    $endAt = $request->filter['endAt'] ?? null;
    $company = $request->filter['company'] ?? null;

    $vehicleLogs = VehicleLog::query()
        ->when($vehicleIds, function ($query) use ($vehicleIds) {
            $query->whereIn('vehicle_id', $vehicleIds);
        })
        ->when($company, function ($query) use ($request) {
            $query->whereHas('vehicle', function ($q) use ($request) {
                $q->where('company_name', $request->filter['company'])
                  ->whereNull('deleted_at'); // ✅ Only active vehicles
            });
        })
        ->when(!$company, function ($query) {
            // ✅ Still exclude deleted vehicles even if no company filter
            $query->whereHas('vehicle', function ($q) {
                $q->whereNull('deleted_at');
            });
        })
        ->when($startAt && $endAt, function ($query) use ($startAt, $endAt) {
            $query->whereBetween('date', [
                Carbon::parse($startAt)->startOfDay(),
                Carbon::parse($endAt)->endOfDay(),
            ]);
        })
        ->when($startAt && !$endAt, function ($query) use ($startAt) {
            $query->where('date', '>=', Carbon::parse($startAt)->startOfDay());
        })
        ->when(!$startAt && $endAt, function ($query) use ($endAt) {
            $query->where('date', '<=', Carbon::parse($endAt)->endOfDay());
        })
        ->with('vehicle')
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);

    return $vehicleLogs;
}


    public function createVehicleLog(array $vehicleLogData): VehicleLog
    {

        $vehicleLog = VehicleLog::create([
            'vehicle_id' => $vehicleLogData['vehicleId'],
            'weight' => $vehicleLogData['weight'],
            'weight_type' => $vehicleLogData['weightType']?? 1,
            'note' => $vehicleLogData['note']?? null,
            'date' => $vehicleLogData['date'] ? $vehicleLogData['date'] : now()
        ]);

        return $vehicleLog;
    }


    public function editVehicleLog(int $vehicleLogId)
    {
        return VehicleLog::with('vehicle')->findOrFail($vehicleLogId);
    }

    public function updateVehicleLog(int $vehicleLogId, array $vehicleLogData)
    {
        $vehicleLog = VehicleLog::find($vehicleLogId);
        $vehicleLog->weight = $vehicleLogData['weight'];
        $vehicleLog->weight_type = $vehicleLogData['weightType'];
        $vehicleLog->note = $vehicleLogData['note'] ?? null;
        $vehicleLog->date = $vehicleLogData['date'];

        $vehicleLog->save();

        return $vehicleLog;
    }

    public function deleteVehicleLog(int $vehicleLogId)
    {

        $vehicleLog = VehicleLog::find($vehicleLogId);
        $vehicleLog->delete();

    }

}
