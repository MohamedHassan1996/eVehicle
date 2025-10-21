<?php

namespace App\Services\Vehicle;

use App\Filters\Vehicle\FilterVehicle;
use App\Models\Vehicle;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
class VehicleService
{

    public function __construct()
    {
    }
    public function allVehicles()
    {
        $perPage = request()->get('pageSize', 10);

        $vehicles = QueryBuilder::for(Vehicle::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new FilterVehicle()),
            ])
            ->with('vehicleLogs')
            ->paginate($perPage); // Pagination applied here


        return $vehicles;
    }


    public function createVehicle(array $vehicleData): Vehicle
    {

        $vehicle = Vehicle::create([
            'license_plate' => $vehicleData['licensePlate'],
            'company_name' => $vehicleData['companyName'],
            'type' => $vehicleData['type'],
        ]);


        return $vehicle;

    }

    public function editVehicle(int $vehicleId)
    {
        return Vehicle::findOrFail($vehicleId);
    }

    public function updateVehicle(int $vehicleId, array $vehicleData)
    {
        $vehicle = Vehicle::find($vehicleId);
        $vehicle->license_plate = $vehicleData['licensePlate'];
        $vehicle->company_name = $vehicleData['companyName'];
        $vehicle->type = $vehicleData['type'];

        $vehicle->save();

        return $vehicle;
    }

    public function deleteVehicle(int $vehicleId)
    {

        $vehicle = Vehicle::find($vehicleId);
        $vehicle->delete();

    }

}
