<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockReservation extends Model
{
    protected $fillable = [
        'stock_id',
        'reserved_by',
        'quantity',
        'status',
        'expires_at'
    ];

    // relationships
    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function reservedBy(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'reserved_by');
    }

}
