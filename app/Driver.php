<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'supplier_ids','supplier_names','logistics_company','first_name','middle_name','last_name', 'company_id_number','license_number','status','mobile_number', 'dateOfSafetyOrientation', 'isApproved','full_name','expirationDate'
    ];

    public function supplier()
    {
        return $this->hasMany('App\Supplier');
    }
}
