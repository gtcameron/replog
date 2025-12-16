<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use App\Models\WorkoutActivity;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class MemberStatsController extends Controller
{
    /**
     * Display a listing of members with their activity stats.
     */
    public function index(): Response
    {
        $family = auth()->user()->family;
        $members = $family->members()->orderBy('name')->get();

        $memberStats = $members->map(function (User $member) use ($family) {
            $logs = WorkoutActivity::query()
                ->where('family_id', $family->id)
                ->where('user_id', $member->id)
                ->get();

            return [
                'member' => $member,
                'totalSessions' => $logs->count(),
                'activitiesCount' => $logs->unique('activity_id')->count(),
                'lastActive' => $logs->max('performed_at'),
            ];
        });

        return Inertia::render('Stats/Members/Index', [
            'memberStats' => $memberStats,
        ]);
    }

    /**
     * Display a member's progression in all activities.
     */
    public function show(User $member): Response
    {
        $this->authorizeMember($member);

        $family = auth()->user()->family;

        $logs = WorkoutActivity::query()
            ->where('family_id', $family->id)
            ->where('user_id', $member->id)
            ->with(['activity', 'sets'])
            ->orderBy('performed_at')
            ->get();

        $progressions = $logs
            ->groupBy('activity_id')
            ->map(function (Collection $activityLogs) {
                $activity = $activityLogs->first()->activity;

                $data = $activityLogs->map(function (WorkoutActivity $log) use ($activity) {
                    return [
                        'date' => $log->performed_at->format('Y-m-d'),
                        'value' => $this->getBestValue($log, $activity),
                    ];
                })->values();

                $values = $data->pluck('value')->filter();

                return [
                    'activity' => $activity,
                    'data' => $data,
                    'personalBest' => $values->max() ?? 0,
                    'lastValue' => $values->last() ?? 0,
                    'totalSessions' => $activityLogs->count(),
                ];
            })
            ->values();

        return Inertia::render('Stats/Members/Show', [
            'member' => $member,
            'progressions' => $progressions,
        ]);
    }

    /**
     * Get the best/primary value from a workout activity based on what the activity tracks.
     * Uses max value across all sets.
     */
    private function getBestValue(WorkoutActivity $log, Activity $activity): ?float
    {
        $sets = $log->sets;

        if ($sets->isEmpty()) {
            return null;
        }

        if ($activity->tracks_weight) {
            $maxWeight = $sets->max('weight');
            if ($maxWeight) {
                return (float) $maxWeight;
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

        if ($activity->tracks_reps) {
            $maxReps = $sets->max('reps');
            if ($maxReps) {
                return (float) $maxReps;
            }
        }

        return null;
    }

    /**
     * Authorize that the member belongs to the current user's family.
     */
    private function authorizeMember(User $member): void
    {
        if ($member->family_id !== auth()->user()->family_id) {
            abort(403, 'This member does not belong to your family.');
        }
    }
}
