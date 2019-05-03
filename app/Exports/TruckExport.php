<?php

namespace App\Exports;

use App\Truck;
use App\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TruckExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Truck::all();
    }

    public function headings(): array
    {
        return [
        	'#',
            'Supplier IDs',
            'Supplier Name',
            'Trucking Company',
            'Plate Number',
            'Brand',
            'Model',
            'Type',
            'Status',
            '-',
            'Date Created',
            'Date Updated'

        ];
    }
}
