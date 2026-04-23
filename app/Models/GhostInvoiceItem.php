<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GhostInvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'ghost_invoice_items';

    protected $fillable = [
        'ghost_invoice_id',
        'product_id',
        'designation',
        'unit_sold',
        'quantity_sold',
        'quantity_deducted',
        'unit_price',
        'original_price',
        'total_price',
    ];

    protected function casts(): array
    {
        return [
            'quantity_sold' => 'decimal:2',
            'quantity_deducted' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    public function ghostInvoice()
    {
        return $this->belongsTo(GhostInvoice::class, 'ghost_invoice_id');
    }
}
