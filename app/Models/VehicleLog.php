<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleLog extends Model
{
    use CreatedUpdatedBy, SoftDeletes;
    protected $fillable = [
        'vehicle_id',
        'weight',
        'weight_type',
        'note',
        'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getLastestEmptyVehicleWeightAttribute()
    {
        return VehicleLog::where('vehicle_id', $this->vehicle_id)
            ->where('weight_type', 0)
            ->orderBy('created_at', 'desc')
            ->value('weight');
    }

    public function getNearestEmptyWeight($logId)
{
    // Get the current log (the one with the given ID)
    $currentLog = VehicleLog::find($logId);


    if (!$currentLog) {
        return 0; // If no log found, return 0 or handle as needed
    }

    // Find the nearest previous "empty" (weight_type = 0) before this log's date
    $previousEmpty = VehicleLog::where('vehicle_id', $currentLog->vehicle_id)
        ->where('weight_type', 0)
        ->where('date', '<=', $currentLog->date)
        ->orderByDesc('date')
        ->first();


    // If not found, get the latest empty weight overall
    if (!$previousEmpty) {
        $previousEmpty = VehicleLog::where('vehicle_id', $currentLog->vehicle_id)
            ->where('weight_type', 0)
            ->orderByDesc('date')
            ->first();
    }

    return $previousEmpty->weight ?? 0;
}

}
