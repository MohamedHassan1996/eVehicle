<?php

use App\Http\Controllers\Api\V1\Auth\AuthLoginController;
use App\Http\Controllers\Api\V1\Auth\AuthLogoutController;
use App\Http\Controllers\Api\V1\CheckVehicleController;
use App\Http\Controllers\Api\V1\OuterVehicleLogController;
use App\Http\Controllers\Api\V1\User\UserController;
use App\Http\Controllers\Api\V1\Vehicle\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {



    Route::prefix('auth')->group(function () {
        Route::post('login', AuthLoginController::class);
        Route::post('logout', AuthLogoutController::class);
    });

    Route::prefix('outer')->group(function () {
        Route::get('vehicle-check', CheckVehicleController::class);
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


});

