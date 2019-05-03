<?php

namespace App\Imports;

use App\Truck;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TruckImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Truck([
            'supplier_ids'    => $row['supplierids'], 
            'supplier_names'    => $row['suppliername'], 
            'trucking_company'    => $row['truckingcompany'], 
            'plate_number'    => $row['platenumber'], 
            'brand'    => $row['brand'], 
            'model'    => $row['model'], 
            'type'    => $row['type'], 
            'status'    => $row['status'], 
        ]);
    }
}
