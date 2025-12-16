<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workout extends Model
{
    /** @use HasFactory<\Database\Factories\WorkoutFactory> */
    use HasFactory;

    protected $fillable = [
        'family_id',
        'user_id',
        'started_by_id',
        'name',
        'started_at',
        'ended_at',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Family, $this>
     */
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * The user who is performing the workout.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The user who started/created this workout.
     *
     * @return BelongsTo<User, $this>
     */
    public function startedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by_id');
    }

    /**
     * @return HasMany<WorkoutActivity, $this>
     */
    public function workoutActivities(): HasMany
    {
        return $this->hasMany(WorkoutActivity::class);
    }

    /**
     * Check if the workout is still active (not ended).
     */
    public function isActive(): bool
    {
        return $this->ended_at === null;
    }

    /**
     * Get the duration in seconds.
     */
    public function getDurationAttribute(): int
    {
        if (! $this->ended_at) {
            return now()->diffInSeconds($this->started_at);
        }

        return $this->ended_at->diffInSeconds($this->started_at);
    }
}
