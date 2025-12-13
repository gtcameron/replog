<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Family extends Model
{
    /** @use HasFactory<\Database\Factories\FamilyFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * @return HasMany<User, $this>
     */
    public function members(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany<Activity, $this>
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * @return HasMany<ActivityType, $this>
     */
    public function activityTypes(): HasMany
    {
        return $this->hasMany(ActivityType::class);
    }

    /**
     * @return HasMany<ActivityLog, $this>
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
