<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Parking extends Model
{
     protected $fillable = [
        'parking_name','parking_description','parking_slot','parking_area','parking_block','parking_status', 'status'
    ];
}
