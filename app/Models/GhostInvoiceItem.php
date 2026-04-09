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
        'designation',
        'unit',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    public function ghostInvoice()
    {
        return $this->belongsTo(GhostInvoice::class, 'ghost_invoice_id');
    }
}
