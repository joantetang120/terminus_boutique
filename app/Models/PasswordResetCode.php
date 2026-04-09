<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'code',
        'attempts',
        'max_attempts',
        'last_attempt_at',
        'expires_at',
        'used',
    ];

    protected function casts(): array
    {
        return [
            'attempts' => 'integer',
            'max_attempts' => 'integer',
            'last_attempt_at' => 'datetime',
            'expires_at' => 'datetime',
            'used' => 'boolean',
        ];
    }

    public function isExpired(): bool
    {
        return now()->greaterThan($this->expires_at);
    }

    public function isLocked(): bool
    {
        return $this->attempts >= $this->max_attempts;
    }

    public function canAttempt(): bool
    {
        return !$this->isExpired() && !$this->isLocked() && !$this->used;
    }

    public function remainingAttempts(): int
    {
        return max(0, $this->max_attempts - $this->attempts);
    }

    public function remainingSeconds(): int
    {
        return max(0, now()->diffInSeconds($this->expires_at, false));
    }

    /**
     * Generate a new 4-digit code for the given email.
     * Deletes any previous unused codes for that email.
     */
    public static function generateFor(string $email): self
    {
        // Clean up old unused codes
        self::where('email', $email)->where('used', false)->delete();

        $code = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        return self::create([
            'email' => $email,
            'code' => $code,
            'attempts' => 0,
            'max_attempts' => 5,
            'expires_at' => now()->addMinutes(10),
        ]);
    }
}
