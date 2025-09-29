<?php

use App\Http\Controllers\Api\V1\Auth\AuthLoginController;
use App\Http\Controllers\Api\V1\Auth\AuthLogoutController;
use App\Http\Controllers\Api\V1\CheckVehicleController;
use App\Http\Controllers\Api\V1\ImportVehicleDataController;
use App\Http\Controllers\Api\V1\OuterVehicleLogController;
use App\Http\Controllers\Api\V1\User\UserController;
use App\Http\Controllers\Api\V1\Vehicle\VehicleController;
use App\Http\Controllers\Api\V1\Vehicle\VehicleLogController;
use App\Http\Controllers\Api\V1\VehiclePdfExportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {



    Route::prefix('auth')->group(function () {
        Route::post('login', AuthLoginController::class);
        Route::post('logout', AuthLogoutController::class);
    });

    Route::prefix('outer')->group(function () {
        Route::post('vehicle-check', CheckVehicleController::class);
        Route::post('vehicle-logs', [OuterVehicleLogController::class, 'store']);
    });


    Route::prefix('users')->group(function () {
        Route::get('', [UserController::class, 'index']);
        Route::post('', [UserController::class, 'store']);
        Route::get('{user}', [UserController::class, 'show']);
        Route::put('{user}', [UserController::class, 'update']);
        Route::delete('{user}', [UserController::class, 'destroy']);
    });

    Route::prefix('vehicles')->group(function () {
        Route::get('', [VehicleController::class, 'index']);
        Route::post('', [VehicleController::class, 'store']);
        Route::get('{vehicle}', [VehicleController::class, 'show']);
        Route::put('{vehicle}', [VehicleController::class, 'update']);
        Route::delete('{vehicle}', [VehicleController::class, 'destroy']);
    });

    Route::prefix('vehicle-logs')->group(function () {
        Route::get('', [VehicleLogController::class, 'index']);
        Route::post('', [VehicleLogController::class, 'store']);
        Route::get('{vehicleLog}', [VehicleLogController::class, 'show']);
        Route::put('{vehicleLog}', [VehicleLogController::class, 'update']);
        Route::delete('{vehicleLog}', [VehicleLogController::class, 'destroy']);
    });

    Route::post('import-vehicle-data', [ImportVehicleDataController::class, 'import']);

    Route::get('vehicles-export-pdf', [VehiclePdfExportController::class, 'export']);




});

