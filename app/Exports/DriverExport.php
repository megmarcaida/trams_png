<?php

namespace App\Exports;

use App\Driver;
use Maatwebsite\Excel\Concerns\FromCollection;

class DriverExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Driver::all();
    }
}
