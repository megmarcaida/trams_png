<?php

namespace App\Imports;

use App\Assistant;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AssistantImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Assistant([
            'supplier_ids'    => $row['supplierids'], 
            'supplier_names'    => $row['suppliername'], 
            'logistics_company'    => $row['logisticcompany'], 
            'first_name'    => $row['firstname'], 
            'last_name'    => $row['lastname'], 
            'mobile_number'    => $row['mobilenumber'], 
            'company_id_number'    => $row['companyidnumber'], 
            'valid_id_present'    => $row['valididpresent'], 
            'valid_id_number'    => $row['valididnumber'], 
            'dateOfSafetyOrientation'    => $row['dateofsafetyorientation'], 
            'isApproved'    => $row['isapproved'], 
            'status'    => $row['status'], 
        ]);
    }
}
