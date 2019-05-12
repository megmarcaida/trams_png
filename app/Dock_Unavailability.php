<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dock_Unavailability extends Model
{
    protected $fillable = [
        'dock_id','dock_name','date_of_unavailability', 'recurrence','time','type','status','emergency','ordering_days','reason'
    ];

    public function docker()
    {
        return $this->hasMany('App\Dock');
    }
}
