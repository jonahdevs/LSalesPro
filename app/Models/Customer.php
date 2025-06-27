<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        return $this->belongsTo(Territory::class);
    }

    public function getAvailableCreditAttribute(): float
    {
        return $this->credit_limit - $this->current_balance;
    }
}
