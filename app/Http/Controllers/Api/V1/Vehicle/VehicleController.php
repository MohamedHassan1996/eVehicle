<?php

namespace App\Http\Controllers\Api\V1\Vehicle;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Vehicle\CreateVehicleRequest;
use App\Http\Requests\V1\Vehicle\UpdateVehicleRequest;
use App\Http\Resources\V1\Vehicle\AllVehicleCollection;
use App\Http\Resources\V1\Vehicle\VehicleResource;
use App\Services\Vehicle\VehicleService;
use App\Services\Vehicle\VehicleLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;


class VehicleController extends Controller implements HasMiddleware
{
    public function __construct(protected VehicleService $vehicleService, protected VehicleLogService $vehicleLogService)
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
        $vehicles = $this->vehicleService->allVehicles();

        return ApiResponse::success(new AllVehicleCollection($vehicles));


        //return ApiResponse::success(new AllUserCollection(PaginateCollection::paginate($users->getCollection(), $request->pageSize?$request->pageSize:10)));

    }

    /**
     * Show the form for creating a new resource.
     */

    public function store(CreateVehicleRequest $createVehicleRequest)
    {
        try {
            DB::beginTransaction();

            $vehicle = $this->vehicleService->createVehicle($createVehicleRequest->validated());

            if(isset($createVehicleRequest->validated()['vehicleLogs'])){
                foreach($createVehicleRequest->validated()['vehicleLogs'] as $vehicleLogData){
                    $vehicleLogData['vehicleId'] = $vehicle->id;
                    $this->vehicleLogService->createVehicleLog($vehicleLogData);
                }
            }

            DB::commit();

            return ApiResponse::success([], __('crud.created'));


        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }


    }

    /**
     * Show the form for editing the specified resource.
     */

    public function show($vehicle)
    {
        $vehicle  =  $this->vehicleService->editVehicle($vehicle);
        return ApiResponse::success(new VehicleResource($vehicle));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($vehicle,UpdateVehicleRequest $updateVehicleRequest)
    {
        try {
            DB::beginTransaction();
            $this->vehicleService->updateVehicle($vehicle, $updateVehicleRequest->validated());
            DB::commit();
            return ApiResponse::success([], __('crud.updated'));

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($vehicle)
    {

        try {
            DB::beginTransaction();
            $this->vehicleService->deleteVehicle($vehicle);
            DB::commit();
            return response()->json([
                'message' => __('crud.updated')
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    }

}
