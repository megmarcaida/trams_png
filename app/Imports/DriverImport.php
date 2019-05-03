<?php

namespace App\Imports;

use App\Driver;
use Maatwebsite\Excel\Concerns\ToModel;

class DriverImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Driver([
            //
        ]);
    }
}
