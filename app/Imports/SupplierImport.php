<?php

namespace App\Imports;

use App\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SupplierImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Supplier([
            'vendor_code'     => $row['vendorcode'],
            'supplier_name'    => $row['suppliername'], 
            'delivery_type'    => $row['deliverytype'], 
            'ordering_days'    => $row['orderingdays'], 
            'module'    => $row['module'], 
            'spoc_firstname'    => $row['spocfirstname'], 
            'spoc_lastname'    => $row['spoclastname'], 
            'spoc_contact_number'    => $row['spoccontactnumber'], 
            'spoc_email_address'    => $row['spocemailaddress'], 
            'status' => $row['status'],
        ]);
    }
}
