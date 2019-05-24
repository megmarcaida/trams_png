<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
     protected $fillable = [
        'vendor_code','supplier_name','delivery_type', 'ordering_days','module','spoc_firstname', 'spoc_lastname','spoc_full_name', 'spoc_contact_number', 'spoc_email_address','status'
    ];

    public function truck()
    {
        return $this->belongsTo('App\Truck');
    }

    public function driver()
    {
        return $this->belongsTo('App\Driver');
    }
}
