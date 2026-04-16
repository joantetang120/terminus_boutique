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
    ];

    protected $casts = [
        'conversion_rate' => 'integer',
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
}
