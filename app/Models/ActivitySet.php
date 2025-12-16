<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivitySet extends Model
{
    /** @use HasFactory<\Database\Factories\ActivitySetFactory> */
    use HasFactory;

    protected $fillable = [
        'workout_activity_id',
        'set_number',
        'reps',
        'weight',
        'duration_seconds',
        'distance',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'distance' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<WorkoutActivity, $this>
     */
    public function workoutActivity(): BelongsTo
    {
        return $this->belongsTo(WorkoutActivity::class);
    }
}
