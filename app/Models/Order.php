<?php

namespace App\Models;

use App\Notifications\OrderConfirmation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Order extends Model
{
    use LogsActivity;
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['order_number', 'status']);
    }
}
