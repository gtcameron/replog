<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkoutActivityLogRequest;
use App\Models\Activity;
use App\Models\Workout;
use App\Models\WorkoutActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class WorkoutActivityLogController extends Controller
{
    /**
     * Store a new activity log for the workout.
     */
    public function store(StoreWorkoutActivityLogRequest $request, Workout $workout): RedirectResponse
    {
        $this->authorizeWorkout($workout);

        if (! $workout->isActive()) {
            return redirect()->route('workouts.show', $workout)
                ->with('error', 'Cannot add activities to a completed workout.');
        }

        $data = $request->validated();

        $workoutActivity = WorkoutActivity::create([
            'family_id' => $workout->family_id,
            'workout_id' => $workout->id,
            'activity_id' => $data['activity_id'],
            'user_id' => $workout->user_id,
            'logged_by_id' => auth()->id(),
            'performed_at' => now()->toDateString(),
            'notes' => $data['notes'] ?? null,
        ]);

        foreach ($data['sets'] as $setData) {
            $workoutActivity->sets()->create($setData);
        }

        return redirect()->route('workouts.show', $workout)
            ->with('success', 'Activity logged!');
    }

    /**
     * Get activity history for the workout context.
     * Returns last 3 logs and 3-month chart data.
     */
    public function activityHistory(Workout $workout, Activity $activity): JsonResponse
    {
        $this->authorizeWorkout($workout);
        $this->authorizeActivity($activity);

        $userId = $workout->user_id;
        $threeMonthsAgo = now()->subMonths(3)->startOfDay();

        // Get last 3 workout activities for this activity by this user
        $recentLogs = WorkoutActivity::query()
            ->where('activity_id', $activity->id)
            ->where('user_id', $userId)
            ->with('sets')
            ->orderByDesc('performed_at')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'performed_at' => $log->performed_at,
                    'sets' => $log->sets->map(fn ($set) => [
                        'set_number' => $set->set_number,
                        'reps' => $set->reps,
                        'weight' => $set->weight,
                        'duration_seconds' => $set->duration_seconds,
                        'distance' => $set->distance,
                    ]),
                ];
            });

        // Get chart data for last 3 months (using max weight from sets)
        $chartData = WorkoutActivity::query()
            ->where('activity_id', $activity->id)
            ->where('user_id', $userId)
            ->where('performed_at', '>=', $threeMonthsAgo)
            ->with('sets')
            ->orderBy('performed_at')
            ->get()
            ->map(function ($log) use ($activity) {
                $value = $this->calculateProgressionValue($log, $activity);

                return [
                    'date' => $log->performed_at->toDateString(),
                    'value' => $value,
                ];
            });

        return response()->json([
            'recentLogs' => $recentLogs,
            'chartData' => $chartData,
            'activity' => $activity->only(['id', 'name', 'tracks_reps', 'tracks_weight', 'tracks_duration', 'tracks_distance']),
        ]);
    }

    /**
     * Calculate a progression value for charting based on what the activity tracks.
     * Uses max value across all sets.
     */
    private function calculateProgressionValue(WorkoutActivity $log, Activity $activity): ?float
    {
        $sets = $log->sets;

        if ($sets->isEmpty()) {
            return null;
        }

        // Priority: weight > reps > duration > distance
        // Use max value across all sets
        if ($activity->tracks_weight) {
            $maxWeight = $sets->max('weight');
            if ($maxWeight) {
                return (float) $maxWeight;
            }
        }

        if ($activity->tracks_reps) {
            $maxReps = $sets->max('reps');
            if ($maxReps) {
                return (float) $maxReps;
            }
        }

        if ($activity->tracks_duration) {
            $maxDuration = $sets->max('duration_seconds');
            if ($maxDuration) {
                return (float) $maxDuration;
            }
        }

        if ($activity->tracks_distance) {
            $maxDistance = $sets->max('distance');
            if ($maxDistance) {
                return (float) $maxDistance;
            }
        }

        return null;
    }

    /**
     * Authorize that the workout belongs to the current user's family.
     */
    private function authorizeWorkout(Workout $workout): void
    {
        if ($workout->family_id !== auth()->user()->family_id) {
            abort(403, 'This workout does not belong to your family.');
        }
    }

    /**
     * Authorize that the activity belongs to the current user's family.
     */
    private function authorizeActivity(Activity $activity): void
    {
        if ($activity->family_id !== auth()->user()->family_id) {
            abort(403, 'This activity does not belong to your family.');
        }
    }
}
