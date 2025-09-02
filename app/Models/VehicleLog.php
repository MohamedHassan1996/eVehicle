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

    public function getLastestEmptyVehicleWeightAttribute()
    {
        return VehicleLog::where('vehicle_id', $this->vehicle_id)
            ->where('weight_type', 0)
            ->orderBy('created_at', 'desc')
            ->value('weight');
    }
}
