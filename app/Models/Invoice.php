<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Invoice extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'number',
        'status',
        'client_name',
        'client_phone',
        'total',
        'paid_amount',
        'balance',
        'due_date',
        'created_by',
        'cancelled_by',
        'cancelled_at',
        'cancel_reason',
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

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            // Auto-set due_date to created_at + 10 days if not provided
            if (empty($invoice->due_date)) {
                $invoice->due_date = now()->addDays(10)->format('Y-m-d');
            }
            // Initialize balance
            $invoice->balance = $invoice->total - $invoice->paid_amount;
        });

        static::updating(function (Invoice $invoice) {
            // Recalculate balance when paid_amount changes
            if ($invoice->isDirty('paid_amount') || $invoice->isDirty('total')) {
                $invoice->balance = $invoice->total - $invoice->paid_amount;
            }
        });
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
            ->logOnly(['number', 'status', 'client_name', 'total', 'balance', 'due_date'])
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
        return $this->status === 'ANNULEE';
    }

    public function isPaid(): bool
    {
        return $this->status === 'SOLDEE';
    }

    public function isUnpaid(): bool
    {
        return $this->status === 'IMPAYEE';
    }

    public function isPartial(): bool
    {
        return $this->status === 'PARTIELLE';
    }

    public function updateStatusFromPayment(): void
    {
        if ($this->paid_amount >= $this->total) {
            $this->status = 'SOLDEE';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'PARTIELLE';
        } else {
            $this->status = 'IMPAYEE';
        }
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'SOLDEE' => 'badge-success',
            'PARTIELLE' => 'badge-warning',
            'IMPAYEE' => 'badge-info',
            'EN_RETARD' => 'badge-danger',
            'ANNULEE' => 'badge-danger',
            default => 'badge-secondary',
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'SOLDEE' => 'Soldée',
            'PARTIELLE' => 'Partielle',
            'IMPAYEE' => 'Impayée',
            'EN_RETARD' => 'En retard',
            'ANNULEE' => 'Annulée',
            default => $this->status,
        };
    }
}
