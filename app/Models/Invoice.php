<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Invoice extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'number',
        'type',
        'status',
        'client_name',
        'client_phone',
        'total',
        'advance_amount',
        'balance',
        'note',
        'created_by',
        'cancelled_by',
        'cancelled_at',
        'cancel_reason',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'advance_amount' => 'decimal:2',
            'balance' => 'decimal:2',
            'cancelled_at' => 'datetime',
        ];
    }

    public static function generateNumber(): string
    {
        $date = now()->format('Y-m-d');
        $count = self::whereDate('created_at', today())->count() + 1;
        return 'FAC-' . now()->format('Ymd') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['number', 'type', 'status', 'client_name', 'total', 'balance'])
            ->logOnlyDirty();
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function payments()
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function ghostInvoice()
    {
        return $this->hasOne(GhostInvoice::class, 'real_invoice_id');
    }

    public function isCancelled(): bool
    {
        return $this->status === 'annulee';
    }

    public function isPaid(): bool
    {
        return $this->status === 'payee';
    }

    public function isCredit(): bool
    {
        return $this->status === 'credit';
    }
}
