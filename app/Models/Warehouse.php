<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    protected $fillable = [
        "code",
        "name",
        "type",
        "address",
        "manager_email",
        "phone",
        "capacity",
        "latitude",
        "longitude",
        "user_id"
    ];

    // relationships
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(StockReservation::class);
    }

    public function stock(): HasMany
    {
        return $this->hasMany(Stock::class);
    }
}
