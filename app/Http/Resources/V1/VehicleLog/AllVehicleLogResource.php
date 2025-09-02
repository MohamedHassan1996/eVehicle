<?php

namespace App\Http\Resources\V1\VehicleLog;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllVehicleLogResource extends JsonResource
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
            'date' => Carbon::parse($this->date)->format('d/m/Y'),
            'time' => Carbon::parse($this->date)->format('H:i'),
            'emptyVehicleWeight' => $this->lastest_empty_vehicle_weight,
            'vehicleLoad' => $this->weight - $this->lastest_empty_vehicle_weight
        ];
    }
}
