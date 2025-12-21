<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    protected $fillable = [
        'email',
        'claimed_at',
    ];

    protected function casts(): array
    {
        return [
            'claimed_at' => 'datetime',
        ];
    }

    /**
     * Check if an email has an unclaimed invite.
     */
    public static function isAllowed(string $email): bool
    {
        return self::where('email', strtolower($email))
            ->whereNull('claimed_at')
            ->exists();
    }

    /**
     * Mark an invite as claimed.
     */
    public static function claim(string $email): void
    {
        self::where('email', strtolower($email))
            ->whereNull('claimed_at')
            ->update(['claimed_at' => now()]);
    }
}
