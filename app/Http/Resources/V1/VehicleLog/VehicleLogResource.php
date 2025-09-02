<?php

namespace App\Http\Resources\V1\VehicleLog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class VehicleLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'vehicleLogId' => $this->id,
            'vehicleId' => $this->vehicle_id,
            'weight' => $this->weight,
            'weightType' => $this->weight_type,
            'date' => $this->date,
            'note' => $this->date??''
        ];
    }
}
