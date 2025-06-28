<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use SoftDeletes, LogsActivity;

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

    public function reservations(): HasManyThrough
    {
        return $this->hasManyThrough(
            StockReservation::class,
            Stock::class,
        );
    }

    // used to track inventory per warehouse
    public function stock(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function getAvailableQuantityAttribute(): int
    {
        $total = $this->stock->sum('quantity');
        $reserved = $this->reservations()
            ->where('status', 'reserved')
            ->sum('stock_reservations.quantity');

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name']);
    }

}
