<?php

namespace App\Imports;

use App\Assistant;
use Maatwebsite\Excel\Concerns\ToModel;

class AssistantImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Assistant([
            //
        ]);
    }
}
