<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
     protected $fillable = [
        'po_number','dock_id','dock_name','date_of_delivery', 'recurrence','slotting_time','material_list','status','truck_id','ordering_days','driver_id','assistant_id','container_number','supplier_id','reason','process_status','gate_in_timestamp','parking_timestamp','dock_in_timestamp','dock_out_timestamp','egress_timestamp','gate_out_timestamp','truck_turnaround_timestamp','unloading_timestamp','recurrent_dateend','recurrent_id','conflict_id'
    ];

    public function docker()
    {
        return $this->hasMany('App\Dock');
    }
}
