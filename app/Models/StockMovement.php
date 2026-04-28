<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class StockMovement extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'input_quantity',
        'input_unit',
        'unit_cost',
        'total_cost',
        'reference_type',
        'reference_id',
        'note',
        'created_by',
        'cancelled_by',
        'cancelled_at',
        'cancel_reason',
    ];

    protected function casts(): array
    {
        return [
            'type' => 'string',
            'quantity' => 'integer',
            'input_quantity' => 'integer',
            'unit_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'cancelled_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['product_id', 'type', 'quantity', 'reference_type', 'reference_id'])
            ->logOnlyDirty();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->type === 'cancel';
    }

    public function scopeForProduct($query, $id)
    {
        return $query->where('product_id', $id);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
