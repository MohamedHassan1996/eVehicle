<?php

use App\Http\Controllers\Api\V1\CheckVehicleController;
use App\Http\Controllers\Api\V1\OuterVehicleLogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Route::prefix('users')->group(function () {
    //     Route::get('', [UserController::class, 'index']);
    //     Route::post('', [UserController::class, 'store']);
    //     Route::get('{user}', [UserController::class, 'show']);
    //     Route::put('{user}', [UserController::class, 'update']);
    //     Route::delete('{user}', [UserController::class, 'destroy']);
    // });

    Route::prefix('outer')->group(function () {
        Route::get('vehicle-check', CheckVehicleController::class);
        Route::post('vehicle-logs', [OuterVehicleLogController::class, 'store']);
    });



});

