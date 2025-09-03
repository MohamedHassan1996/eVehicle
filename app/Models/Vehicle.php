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

    public function getLastestEmptyVehicleWeightAttribute()
    {
        return $this->vehicleLogs()
            ->where('weight_type', 0)
            ->orderBy('created_at', 'desc')
            ->value('weight')??0;
    }
}
