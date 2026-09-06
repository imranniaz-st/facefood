<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'order_number',
    'user_id',
    'address_id',
    'delivery_address',
    'subtotal',
    'delivery_fee',
    'tax',
    'total',
    'payment_method',
    'status',
    'verification_code',
    'note',
    'estimated_minutes',
])]
class Order extends Model
{
    public const PAYMENT_METHODS = [
        'cash_on_delivery',
        'jazzcash',
        'card',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'estimated_minutes' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
