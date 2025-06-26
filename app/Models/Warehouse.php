<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        "code",
        "name",
        "type",
        "address",
        "capacity",
        "latitude",
        "longitude",
        "user_id"
    ];

    // relationships
}
