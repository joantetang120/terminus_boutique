<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUnitConversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'unit_type',
        'unit',
        'conversion_rate',
        'sale_price',
        'sale_margin_percentage',
    ];

    protected $casts = [
        'conversion_rate' => 'integer',
        'sale_price' => 'decimal:2',
        'sale_margin_percentage' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopePurchase($query)
    {
        return $query->where('unit_type', 'purchase');
    }

    public function scopeSale($query)
    {
        return $query->where('unit_type', 'sale');
    }

    /**
     * Calculate minimum sale price based on margin percentage
     */
    public function getMinimumPriceAttribute(): ?float
    {
        if (!$this->sale_price || !$this->sale_margin_percentage) {
            return null;
        }

        return round($this->sale_price * (1 - ($this->sale_margin_percentage / 100)), 2);
    }
}
