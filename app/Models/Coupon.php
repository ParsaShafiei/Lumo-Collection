<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'min_order_value',
        'max_uses',
        'used_count',
        'expires_at',
    ];

    protected $casts = ['expires_at' => 'datetime'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isValid(float $orderTotal): bool
    {
        return (!$this->expires_at || $this->expires_at->isFuture())
            && (!$this->max_uses || $this->used_count < $this->max_uses)
            && $orderTotal >= $this->min_order_value;
    }

    public function calculateDiscount(float $subtotal): float
    {
        return $this->discount_type === 'percentage'
            ? $subtotal * ($this->discount_value / 100)
            : min($this->discount_value, $subtotal);
    }
}
