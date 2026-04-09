<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GhostInvoice extends Model
{
    use HasFactory;

    protected $table = 'ghost_invoices';

    protected $fillable = [
        'real_invoice_id',
        'number',
        'type',
        'status',
        'client_name',
        'client_phone',
        'total',
        'advance_amount',
        'balance',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'advance_amount' => 'decimal:2',
            'balance' => 'decimal:2',
        ];
    }

    public function items()
    {
        return $this->hasMany(GhostInvoiceItem::class, 'ghost_invoice_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
