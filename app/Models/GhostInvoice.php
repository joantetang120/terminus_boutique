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
        'status',
        'client_name',
        'client_phone',
        'total',
        'paid_amount',
        'balance',
        'due_date',
        'cancelled_at',
        'cancel_reason',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance' => 'decimal:2',
            'due_date' => 'date',
            'cancelled_at' => 'datetime',
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
