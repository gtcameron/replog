<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkoutActivityLogRequest;
use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\Workout;
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

        ActivityLog::create([
            'family_id' => $workout->family_id,
            'workout_id' => $workout->id,
            'activity_id' => $data['activity_id'],
            'user_id' => $workout->user_id,
            'logged_by_id' => auth()->id(),
            'performed_at' => now()->toDateString(),
            'sets' => $data['sets'] ?? null,
            'reps' => $data['reps'] ?? null,
            'weight' => $data['weight'] ?? null,
            'duration_seconds' => $data['duration_seconds'] ?? null,
            'distance' => $data['distance'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

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

        // Get last 3 logs for this activity by this user
        $recentLogs = ActivityLog::query()
            ->where('activity_id', $activity->id)
            ->where('user_id', $userId)
            ->orderByDesc('performed_at')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get(['id', 'performed_at', 'sets', 'reps', 'weight', 'duration_seconds', 'distance']);

        // Get chart data for last 3 months
        $chartData = ActivityLog::query()
            ->where('activity_id', $activity->id)
            ->where('user_id', $userId)
            ->where('performed_at', '>=', $threeMonthsAgo)
            ->orderBy('performed_at')
            ->get()
            ->map(function ($log) use ($activity) {
                // Calculate a representative value based on what the activity tracks
                $value = $this->calculateProgressionValue($log, $activity);

                return [
                    'date' => $log->performed_at->toDateString(),
                    'value' => $value,
                ];
            });

        return response()->json([
            'recentLogs' => $recentLogs,
            'chartData' => $chartData,
            'activity' => $activity->only(['id', 'name', 'tracks_sets', 'tracks_reps', 'tracks_weight', 'tracks_duration', 'tracks_distance']),
        ]);
    }

    /**
     * Calculate a progression value for charting based on what the activity tracks.
     */
    private function calculateProgressionValue(ActivityLog $log, Activity $activity): ?float
    {
        // Priority: weight > reps > duration > distance
        if ($activity->tracks_weight && $log->weight) {
            return (float) $log->weight;
        }

        if ($activity->tracks_reps && $log->reps) {
            return (float) $log->reps;
        }

        if ($activity->tracks_duration && $log->duration_seconds) {
            return (float) $log->duration_seconds;
        }

        if ($activity->tracks_distance && $log->distance) {
            return (float) $log->distance;
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
