<?php

namespace App\Services\Vehicle;

use App\Filters\Vehicle\FilterVehicle;
use App\Models\Vehicle;
use App\Models\VehicleLog;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
class VehicleLogService
{

    public function __construct()
    {
    }
    public function allVehicleLogs()
    {
        $perPage = request()->get('pageSize', 10);

        $vehicleLogs = QueryBuilder::for(VehicleLog::class)
            ->allowedFilters([
                //AllowedFilter::custom('search', new FilterVehicle()),
                AllowedFilter::exact('vehicleId', 'vehicle_id'),
            ])
            ->with('vehicle')
            ->paginate($perPage); // Pagination applied here


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
