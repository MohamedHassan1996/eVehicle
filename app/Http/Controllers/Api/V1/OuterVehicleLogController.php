<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ResponseCode\HttpStatusCode;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OuterVehicleLogController extends Controller
{
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
             $data = $request->all();

        $vehicleLog = VehicleLog::create([
            'vehicle_id' => $data['vehicleId'],
            'weight' => $data['weight'],
            'weight_type' => $data['weightType']??1,
            'note' => $data['note']?? null,
            'date' => $data['date'],
        ]);
        DB::commit();
        return ApiResponse::success([
            'message' => 'Vehicle log created successfully',
        ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return ApiResponse::error('Failed to create vehicle log', [], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
