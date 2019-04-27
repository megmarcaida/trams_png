<?php

namespace App\Exports;

use App\Truck;
use Maatwebsite\Excel\Concerns\FromCollection;

class TruckExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Truck::all();
    }
}
