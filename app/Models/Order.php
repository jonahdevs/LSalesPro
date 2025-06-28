<?php

namespace App\Models;

use App\Notifications\OrderConfirmation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    //
    protected $fillable = [
        'order_number',
        'customer_id',
        'subtotal',
        'discount_total',
        'tax_total',
        'total',
        'notes',
        'status'
    ];

    // relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }


    // events
    protected static function booted()
    {
        static::updated(function (self $order) {

            // Notify customer when order status is confirmed
            if ($order->status === 'confirmed') {
                $order->customer->notify(new OrderConfirmation($order));
            }
        });
    }
}
