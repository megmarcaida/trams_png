<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BannedIssue extends Model
{
    protected $fillable = [
        'dock_name', 'module', 'status',
    ];
}
