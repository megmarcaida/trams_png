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
            'SupplierIDs',
            'SupplierName',
            'LogisticCompany',
            'FirstName',
            'LastName',
            '-',
            'MobileNumber',
            'CompanyIDNumber',
            'LicenseNumber',
            'DateofSafetyOrientation',
            'IsApproved',
            'Status',
            '-',
            'DateCreated',
            'DateUpdated'

        ];
    }
}
