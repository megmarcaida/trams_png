<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dock extends Model
{
    protected $fillable = [
        'dock_name', 'module','user_type', 'status',
    ];
}
