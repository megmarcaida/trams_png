<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    protected $fillable = [
        'supplier_ids','trucking_company','plate_number', 'brand','model','type','status'
    ];

    public function supplier()
    {
        return $this->hasMany('App\Supplier');
    }
}
