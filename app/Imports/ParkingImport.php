<?php

namespace App\Imports;

use App\Parking;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ParkingImport implements ToModel, WithHeadingRow
{
     /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Parking([
            'parking_name'     => $row['parking_name'],
            'parking_description'    => $row['parking_description'], 
            'parking_slot'    => $row['parking_slot'], 
            'parking_area'    => $row['parking_area'], 
            'parking_block'    => $row['parking_block'], 
            'parking_status'    => $row['parking_status'], 
            'status' => $row['status'],
        ]);
    }
}
