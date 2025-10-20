<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use CreatedUpdatedBy, SoftDeletes;
    protected $fillable = [
        'license_plate',
        'company_name',
        'type',
    ];

    public function vehicleLogs()
    {
        return $this->hasMany(VehicleLog::class);
    }

    // public function getLastestEmptyVehicleWeightAttribute()
    // {
    //     return $this->vehicleLogs()
    //         ->where('weight_type', 0)
    //         ->orderBy('created_at', 'desc')
    //         ->value('weight')??0;
    // }

    public function lastestEmptyVehicleWeight($id)
    {

        // Find the latest record where weight_type = 1 for this vehicle
        $latestLoaded = VehicleLog::where('vehicle_id', $id)
            ->where('weight_type', 1)
            ->orderByDesc('date')
            ->first();

        // If we have a loaded record, find the nearest empty (type 0) before it
        if ($latestLoaded) {
            $previousEmpty = VehicleLog::where('vehicle_id', $id)
                ->where('weight_type', 0)
                ->where('date', '<=', $latestLoaded->date)
                ->orderByDesc('date')
                ->first();

            if ($previousEmpty) {
                return $previousEmpty->weight ?? 0;
            }
        }

        // If no previous empty found, get the latest empty one
        return VehicleLog::where('vehicle_id', $this->id)
            ->where('weight_type', 0)
            ->orderByDesc('date')
            ->value('weight') ?? 0;
    }

}
