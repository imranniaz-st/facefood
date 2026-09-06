<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'label',
    'line1',
    'line2',
    'city',
    'area',
    'latitude',
    'longitude',
    'is_default',
])]
class Address extends Model
{
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fullAddress(): string
    {
        $parts = array_filter([$this->line1, $this->line2, $this->area, $this->city]);

        return implode(', ', $parts);
    }
}
