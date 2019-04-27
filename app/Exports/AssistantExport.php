<?php

namespace App\Exports;

use App\Assistant;
use Maatwebsite\Excel\Concerns\FromCollection;

class AssistantExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Assistant::all();
    }
}
