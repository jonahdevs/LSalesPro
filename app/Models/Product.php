<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'tax_rate',
        'packaging',
        'min_order_quantity',
        'reorder_level',
        'unit',
        'category_id'
    ];

    // relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(StockReservation::class, 'product_id');
    }

    // used to track inventory per warehouse
    public function stock(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function getAvailableQuantityAttribute(): int
    {
        $total = $this->stock->sum('quantity');
        $reserved = $this->reservations()->where('status', 'reserved')->sum('quantity');

        return max($total - $reserved, 0);
    }


    public function warehouses()
    {
        return $this->hasManyThrough(
            Warehouse::class,
            Stock::class,
            'product_id',
            'id',
            'id',
            'warehouse_id'
        );
    }

}
