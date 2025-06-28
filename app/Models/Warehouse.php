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

    public function reservations()
    {
        return $this->hasManyThrough(
            StockReservation::class,
            Stock::class,
            'product_id',
            'stock_id',
            'id',
            'id'
        );
    }


    public function stock(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function totalStockedQuantity()
    {
        return $this->stock()->sum("quantity");
    }


}
