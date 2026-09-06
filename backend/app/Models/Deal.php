<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'product_id',
    'title',
    'description',
    'deal_price',
    'image_url',
    'badge_color',
    'tags',
    'starts_at',
    'ends_at',
    'is_active',
    'wordpress_post_id',
    'synced_at',
])]
class Deal extends Model
{
    protected function casts(): array
    {
        return [
            'deal_price' => 'decimal:2',
            'tags' => 'array',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
            'synced_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where('ends_at', '>', now())
            ->where(function (Builder $q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            });
    }
}
