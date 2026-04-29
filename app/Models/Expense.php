<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Expense extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'category',
        'label',
        'amount',
        'expense_date',
        'note',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'expense_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['category', 'label', 'amount', 'expense_date', 'note'])
            ->logOnlyDirty();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get common expense categories for dropdown suggestions
     */
    public static function getCommonCategories(): array
    {
        return [
            'Loyer',
            'Salaires',
            'Électricité',
            'Eau',
            'Transport',
            'Fournitures',
            'Maintenance',
            'Marketing',
            'Communication',
            'Assurances',
            'Impôts',
            'Frais bancaires',
            'Autres',
        ];
    }
}
