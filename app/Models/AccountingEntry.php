<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class AccountingEntry extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'date',
        'type',
        'amount',
        'reference_type',
        'reference_id',
        'description',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['date', 'type', 'amount', 'description', 'status'])
            ->logOnlyDirty();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function modifications()
    {
        return $this->hasMany(AccountingModification::class, 'entry_id');
    }

    public function pendingModifications()
    {
        return $this->modifications()->where('status', 'pending');
    }
}
