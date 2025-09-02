<?php

namespace App\Http\Resources\V1\VehicleLog;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AllVehicleLogCollection extends ResourceCollection
{
    private $pagination;

    public function __construct($resource)
    {
        $this->pagination = [
            'total' => $resource->total(),
            'count' => $resource->count(),
            'perPage' => $resource->perPage(),
            'currentPage' => $resource->currentPage(),
            'totalPages' => $resource->lastPage(),
        ];

        // Pass the actual collection (use getCollection() to get the data)
        parent::__construct($resource->getCollection());
    }

    public function toArray(Request $request): array
    {
        return [
            'vehicleLogs' => AllVehicleLogResource::collection($this->collection),
            'pagination' => $this->pagination,
        ];
    }
}
