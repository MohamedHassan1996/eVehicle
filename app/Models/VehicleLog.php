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
}
