<?php

namespace App\Exports;

use App\Driver;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DriverExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Driver::all();
    }

    public function headings(): array
    {
        return [
        	'#',
            'Supplier IDs',
            'Supplier Name',
            'Logistic Company',
            'First Name',
            'Last Name',
            'Mobile Number',
            'Company ID Number',
            'License Number',
            'Date of Safety Orientation',
            'Is Approved',
            'Status',
            '-',
            'Date Created',
            'Date Updated'

        ];
    }
}
