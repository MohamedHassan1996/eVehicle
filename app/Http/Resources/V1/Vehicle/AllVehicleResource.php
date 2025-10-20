<?php

namespace App\Http\Resources\V1\Vehicle;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllVehicleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {


        return [
            'vehicleId' => $this->id,
            'licensePlate' => $this->license_plate,
            'type' => $this->type??'',
            'companyName' => $this->company_name??'',
            'emptyVehicleWeight' => $this->lastestEmptyVehicleWeight($this->id),
        ];
    }
}
