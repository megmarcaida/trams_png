<?php

namespace App\Exports;

use App\Assistant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AssistantExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Assistant::all();
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
            'MobileNumber',
            'CompanyIDNumber',
            'ValidIDPresent',
            'ValidIDNumber',
            'DateofSafetyOrientation',
            'IsApproved',
            'Status',
            '-',
            'DateCreated',
            'DateUpdated'

        ];
    }
}
