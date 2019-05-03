<?php

namespace App\Imports;

use App\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;

class SupplierImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Supplier([
            'vendor_code'     => $row['vendor_code'],
            'supplier_name'    => $row['supplier_name'], 
            'delivery_type'    => $row['delivery_type'], 
            'ordering_days'    => $row['ordering_days'], 
            'module'    => $row['module'], 
            'spoc_firstname'    => $row['spoc_firstname'], 
            'spoc_lastname'    => $row['spoc_lastname'], 
            'spoc_contact_number'    => $row['spoc_contact_number'], 
            'spoc_email_address'    => $row['spoc_email_address'], 
        ]);
    }
}
