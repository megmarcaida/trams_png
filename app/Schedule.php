<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
     protected $fillable = [
        'po_number','dock_id','dock_name','date_of_delivery', 'recurrence','slotting_time','material_list','status','truck_id','ordering_days','driver_id','assistant_id','container_number','supplier_id','reason'
    ];

    public function docker()
    {
        return $this->hasMany('App\Dock');
    }
}
