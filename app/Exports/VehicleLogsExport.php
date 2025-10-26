<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VehicleLogsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $logs;

    public function __construct($logs)
    {
        $this->logs = $logs;
    }

    public function collection()
    {
        return $this->logs;
    }

    public function headings(): array
    {
        return [
            'licensePlate',
            'Company',
            'Date',
            'Time',
            'Weight Type',
            'Full Load',
            'Empty Vehicle Weight',
            'Vehicle Load'
        ];
    }

    public function map($log): array
    {
        return [
            $log->vehicle?->license_plate??'',
            $log->vehicle->company_name ?? '-',
            Carbon::parse($log->date)->format('d/m/Y'),
            Carbon::parse($log->date)->format('H:i'),
            $log->weight_type ? 'Loaded' : 'Empty',
            $log->weight,
            $log->getNearestEmptyWeight($log->id),
            $log->weight - $log->getNearestEmptyWeight($log->id)

        ];
    }
}

