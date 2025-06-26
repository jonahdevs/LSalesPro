<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    //
    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    // relationships
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // events
    protected static function booted(): void
    {
        static::creating(function (self $category) {
            $category->slug = $category->slug ?: Str::slug($category->name, '-');
        });
    }
}
