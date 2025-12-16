<?php

namespace App\Models;

use App\Enums\EquipmentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    /** @use HasFactory<\Database\Factories\ActivityFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'family_id',
        'equipment_type',
        'muscle_group',
        'description',
        'instructions',
        'tracks_reps',
        'tracks_weight',
        'tracks_duration',
        'tracks_distance',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'equipment_type' => EquipmentType::class,
            'tracks_reps' => 'boolean',
            'tracks_weight' => 'boolean',
            'tracks_duration' => 'boolean',
            'tracks_distance' => 'boolean',
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
     * @return HasMany<WorkoutActivity, $this>
     */
    public function workoutActivities(): HasMany
    {
        return $this->hasMany(WorkoutActivity::class);
    }
}
