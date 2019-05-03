<?php

namespace App\Imports;

use App\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;

class SupplierImport implements ToModel
{
    public $model = Supplier::class; // Only needed for globalization purpose
    
    // Excel file header    
    public $header = [
        'vendor_code',  'supplier_name', 'delivery_type','ordering_days', 'module', 'SPOC_Firstname', 'SPOC_Lastname', 'SPOC_Contact_Number','SPOC_Email_Address','Status'
    ];

    public $verifyHeader = true; // Header verification toggle

    public $truncate = true; // We want to truncate table before the import

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new $this->model($row);
    }
}
