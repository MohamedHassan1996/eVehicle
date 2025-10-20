<?php

namespace App\Services\Select;

use Illuminate\Support\Facades\DB;

class VehicleSelectService
{
    public function getAllVehicles(string|null $companyName = null)
    {
       $clients = DB::table('vehicles')
        ->select('id as value', 'license_plate as label')
        ->when($companyName, function ($query, $companyName) {
            return $query->where('company_name', $companyName);
        })
        ->whereNotNull('license_plate')
        ->get();

        return $clients;
    }

}
