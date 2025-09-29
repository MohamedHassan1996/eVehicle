<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Validator;

class CheckVehicleController extends Controller
{
    public function __invoke()
{
    $licensePlate = request('licensePlate');

    // Validate license plate format: 2 letters, 3 numbers, 2 letters
    $validator = Validator::make(
        ['licensePlate' => $licensePlate],
        [
            'licensePlate' => [
                'required',
                'string',
                'regex:/^[a-zA-Z]{2}\d{3}[a-zA-Z]{2}$/i'
            ]
        ],
        [
            'licensePlate.required' => 'License plate is required.',
            'licensePlate.regex' => 'License plate must be in format: 2 letters, 3 numbers, 2 letters (e.g., TT568HM).'
        ]
    );

    if ($validator->fails()) {
        return ApiResponse::error(
            $validator->errors()->first(),
            422
        );
    }

    // Normalize to uppercase for consistency
    $licensePlate = strtoupper($licensePlate);

    $vehicle = Vehicle::where('license_plate', $licensePlate)->first();

    if(!$vehicle) {
        $vehicle = Vehicle::create([
            'license_plate' => $licensePlate,
            'company_name' => 'Unknown',
            'type' => 0,
        ]);
    }

    return ApiResponse::success([
        'exists' => true,
        'vehicleId' => $vehicle->id
    ]);
}

}
