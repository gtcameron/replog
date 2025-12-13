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
        'activity_type_id',
        'equipment_type',
        'muscle_group',
        'description',
        'instructions',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'equipment_type' => EquipmentType::class,
        ];
    }

    /**
     * @return BelongsTo<ActivityType, $this>
     */
    public function activityType(): BelongsTo
    {
        return $this->belongsTo(ActivityType::class);
    }

    /**
     * @return BelongsTo<Family, $this>
     */
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * @return HasMany<ActivityLog, $this>
     */
    public function logs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
