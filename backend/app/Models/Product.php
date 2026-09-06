<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'category_id',
    'name',
    'description',
    'ingredients',
    'price',
    'tax_rate',
    'image_url',
    'rating',
    'calories',
    'spice_level',
    'prep_time_minutes',
    'extras',
    'is_popular',
    'is_available',
])]
class Product extends Model
{
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'tax_rate' => 'decimal:4',
            'rating' => 'decimal:1',
            'calories' => 'integer',
            'prep_time_minutes' => 'integer',
            'extras' => 'array',
            'is_popular' => 'boolean',
            'is_available' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function productExtras(): HasMany
    {
        return $this->hasMany(ProductExtra::class)->orderBy('sort_order')->orderBy('id');
    }
}
