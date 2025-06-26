<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Territory extends Model
{
    //
    protected $fillable = [
        'name',
        'slig',
        'description'
    ];

    // relationships
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    // events
    protected static function booted(): void
    {
        static::creating(function (self $territory) {
            $territory->slug = $territory->slug ?: Str::slug($territory->name, '-');
        });
    }
}
