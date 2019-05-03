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
            'VendorCode',
            'SupplierName',
            'DeliveryType',
            'OrderingDays',
            'Module',
            'SPOCFirstName',
            '-',
            'SPOCLastName',
            'SPOCContactNumber',
            'SPOCEmailAddress',
            'Status',
            'DateCreated',
            'DateUpdated'

        ];
    }
}
