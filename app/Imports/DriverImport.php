<?php

namespace App\Imports;

use App\Driver;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DriverImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Driver([
            'supplier_ids'    => $row['supplierids'], 
            'supplier_names'    => $row['suppliername'], 
            'logistics_company'    => $row['logisticcompany'], 
            'first_name'    => $row['firstname'], 
            'last_name'    => $row['lastname'], 
            'mobile_number'    => $row['mobilenumber'], 
            'company_id_number'    => $row['companyidnumber'], 
            'license_number'    => $row['licensenumber'], 
            'dateOfSafetyOrientation'    => $row['dateofsafetyorientation'], 
            'isApproved'    => $row['isapproved'], 
            'status'    => $row['status'], 
        ]);
    }
}
