<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Product extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'unit',
        'purchase_unit',
        'purchase_conversion_rate',
        'sale_unit',
        'sale_conversion_rate',
        'current_stock',
        'alert_threshold',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'unit' => 'string',
            'purchase_conversion_rate' => 'integer',
            'sale_conversion_rate' => 'integer',
            'current_stock' => 'integer',
            'alert_threshold' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'unit', 'current_stock', 'alert_threshold', 'is_active'])
            ->logOnlyDirty();
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->current_stock <= $this->alert_threshold;
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->alert_threshold;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'alert_threshold');
    }

    /**
     * Check if product has unit conversions configured
     */
    public function hasConversion(): bool
    {
        return $this->purchase_unit !== null || $this->sale_unit !== null;
    }

    /**
     * Get display unit (sale_unit if configured, otherwise base unit)
     */
    public function getDisplayUnitAttribute(): string
    {
        return $this->sale_unit ?? $this->unit;
    }

    /**
     * Convert quantity from any unit to base unit (unit)
     */
    public function convertToBaseUnit(int $quantity, string $fromUnit): int
    {
        if ($fromUnit === $this->unit) {
            return $quantity;
        }

        if ($fromUnit === $this->purchase_unit && $this->purchase_conversion_rate) {
            return $quantity * $this->purchase_conversion_rate;
        }

        if ($fromUnit === $this->sale_unit && $this->sale_conversion_rate) {
            return $quantity * $this->sale_conversion_rate;
        }

        throw new \Exception("Unité '{$fromUnit}' non reconnue pour ce produit");
    }

    /**
     * Convert quantity from base unit to any other unit
     */
    public function convertFromBaseUnit(int $quantity, string $toUnit): int
    {
        if ($toUnit === $this->unit) {
            return $quantity;
        }

        if ($toUnit === $this->purchase_unit && $this->purchase_conversion_rate) {
            return (int) floor($quantity / $this->purchase_conversion_rate);
        }

        if ($toUnit === $this->sale_unit && $this->sale_conversion_rate) {
            return (int) floor($quantity / $this->sale_conversion_rate);
        }

        throw new \Exception("Unité '{$toUnit}' non reconnue pour ce produit");
    }

    /**
     * Get available units for this product (for dropdowns)
     */
    public function getAvailableUnits(): array
    {
        $units = [$this->unit => $this->unit];

        if ($this->purchase_unit) {
            $units[$this->purchase_unit] = $this->purchase_unit . ' (achat: 1 = ' . $this->purchase_conversion_rate . ' ' . $this->unit . ')';
        }

        if ($this->sale_unit && $this->sale_unit !== $this->purchase_unit) {
            $units[$this->sale_unit] = $this->sale_unit . ' (vente: 1 = ' . $this->sale_conversion_rate . ' ' . $this->unit . ')';
        }

        return $units;
    }
}
