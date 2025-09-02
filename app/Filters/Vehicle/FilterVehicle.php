<?php

namespace App\Filters\Vehicle;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FilterVehicle implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return $query->where(function ($query) use ($value) {
            $query->where('license_plate', 'like', '%' . $value . '%')
                ->orWhere('company_name', 'like', '%' . $value . '%')
                ->orWhere('type', 'like', '%' . $value . '%');
        });
    }
}
