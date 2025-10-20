<?php

namespace App\Http\Controllers\Api\V1\Vehicle;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\VehicleLog\CreateVehicleLogRequest;
use App\Http\Requests\V1\VehicleLog\UpdateVehicleLogRequest;
use App\Http\Resources\V1\VehicleLog\AllVehicleLogCollection;
use App\Http\Resources\V1\VehicleLog\VehicleLogResource;
use App\Services\Vehicle\VehicleLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;


class VehicleLogController extends Controller implements HasMiddleware
{
    public function __construct(protected VehicleLogService $vehicleLogService)
    {
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth:api'),
            // new Middleware('permission:all_users', only:['index']),
            // new Middleware('permission:create_user', only:['create']),
            // new Middleware('permission:edit_user', only:['edit']),
            // new Middleware('permission:update_user', only:['update']),
            // new Middleware('permission:destroy_user', only:['destroy']),
        ];
    }


    public function index(Request $request)
    {
        $vehicleLogs = $this->vehicleLogService->allVehicleLogs();

        return ApiResponse::success(new AllVehicleLogCollection($vehicleLogs));


        //return ApiResponse::success(new AllUserCollection(PaginateCollection::paginate($users->getCollection(), $request->pageSize?$request->pageSize:10)));

    }

    /**
     * Show the form for creating a new resource.
     */

    public function store(CreateVehicleLogRequest $createVehicleLogRequest)
    {
        try {
            DB::beginTransaction();

            $this->vehicleLogService->createVehicleLog($createVehicleLogRequest->validated());

            DB::commit();

            return ApiResponse::success([], 'created successfully !');


        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }


    }

    /**
     * Show the form for editing the specified resource.
     */

    public function show($vehicleLog)
    {
        $vehicleLog  =  $this->vehicleLogService->editVehicleLog($vehicleLog);
        return ApiResponse::success(new VehicleLogResource($vehicleLog));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($vehicleLog, UpdateVehicleLogRequest $updateVehicleLogRequest)
    {
        try {
            DB::beginTransaction();
            $this->vehicleLogService->updateVehicleLog($vehicleLog, $updateVehicleLogRequest->validated());
            DB::commit();
            return ApiResponse::success([], 'updated successfully !');

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($vehicleLog)
    {

        try {
            DB::beginTransaction();
            $this->vehicleLogService->deleteVehicleLog($vehicleLog);
            DB::commit();
            return ApiResponse::success([], 'deleted successfully !');

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

}
