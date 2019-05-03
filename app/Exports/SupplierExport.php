<?php

namespace App\Exports;

use App\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SupplierExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Supplier::all();
    }

    public function headings(): array
    {
        return [
        	'#',
            'Vendor Code',
            'Supplier Name',
            'Delivery Type',
            'Ordering Days',
            'Module',
            'SPOC First Name',
            '-',
            'SPOC Last Name',
            'SPOC Contact Number',
            'SPOC Email Address',
            'Status',
            'Date Created',
            'Date Updated'

        ];
    }
}
