<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    //
    protected $fillable = [
        'name',
        'type',
        'category',
        'contact_person',
        'phone',
        'email',
        'tax_id',
        'payment_terms',
        'credit_limit',
        'current_balance',
        'latitude',
        'longitude',
        'address',
        'territory_id'
    ];

    // relationships
    public function territory(): BelongsTo
    {
        return $this->belongsTo(Territory::class, 'territory_id');
    }

    public function getAvailableCreditAttribute(): float
    {
        return $this->credit_limit - $this->current_balance;
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
