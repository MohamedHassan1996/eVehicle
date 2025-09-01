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
}
