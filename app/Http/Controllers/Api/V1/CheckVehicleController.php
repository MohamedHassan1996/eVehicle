<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ResponseCode\HttpStatusCode;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class CheckVehicleController extends Controller
{
    public function __invoke()
    {
        $licensePlate = request('licensePlate');

        $vehicle = Vehicle::where('license_plate', $licensePlate)->first();

        if($vehicle) {
            return ApiResponse::success([
                'exists' => true,
                'vehicleId' => $vehicle->id
            ]);
        }
        return ApiResponse::error('Vehicle not found', [], HttpStatusCode::NOT_FOUND);
    }
}
