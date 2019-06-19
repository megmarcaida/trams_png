<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BannedIssue extends Model
{
    protected $fillable = [
        'name', 'location', 'date_time', 'violation', 'reason', 'additional_information', 'supplier','status'
    ];
}
