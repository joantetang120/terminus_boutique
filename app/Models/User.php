<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Support\LogOptions;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'created_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'is_active', 'created_by'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(function (string $eventName) {
                $translations = [
                    'created' => 'créé',
                    'updated' => 'modifié',
                    'deleted' => 'supprimé',
                ];
                return "Utilisateur " . ($translations[$eventName] ?? $eventName);
            });
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        // Customize description for activation/deactivation
        if ($eventName === 'updated') {
            $properties = $activity->properties->toArray();
            $old = $properties['old'] ?? [];
            $new = $properties['attributes'] ?? [];

            if (isset($old['is_active']) && isset($new['is_active'])) {
                $activity->description = $new['is_active'] ? 'Utilisateur activé' : 'Utilisateur désactivé';
            }
        }
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdUsers()
    {
        return $this->hasMany(User::class, 'created_by');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'created_by');
    }

    public function cancelledInvoices()
    {
        return $this->hasMany(Invoice::class, 'cancelled_by');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'created_by');
    }

    public function accountingEntries()
    {
        return $this->hasMany(AccountingEntry::class, 'created_by');
    }
}
