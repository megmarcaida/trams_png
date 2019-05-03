<?php

namespace App\Imports;

use App\Truck;
use Maatwebsite\Excel\Concerns\ToModel;

class TruckImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Truck([
            //
        ]);
    }
}
