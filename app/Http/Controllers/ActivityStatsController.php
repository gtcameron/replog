<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityLog;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class ActivityStatsController extends Controller
{
    /**
     * Display a listing of activities with participation stats.
     */
    public function index(): Response
    {
        $family = auth()->user()->family;

        $activities = Activity::query()
            ->where('family_id', $family->id)
            ->with('activityType')
            ->withCount('logs')
            ->orderBy('name')
            ->get();

        $activityStats = $activities->map(function (Activity $activity) use ($family) {
            $logs = ActivityLog::query()
                ->where('family_id', $family->id)
                ->where('activity_id', $activity->id)
                ->get();

            return [
                'activity' => $activity,
                'totalLogs' => $logs->count(),
                'participantsCount' => $logs->unique('user_id')->count(),
                'lastPerformed' => $logs->max('performed_at'),
            ];
        });

        return Inertia::render('Stats/Activities/Index', [
            'activityStats' => $activityStats,
        ]);
    }

    /**
     * Display activity stats with leaderboard and comparison chart.
     */
    public function show(Activity $activity): Response
    {
        $this->authorizeActivity($activity);

        $family = auth()->user()->family;
        $members = $family->members()->orderBy('name')->get();

        $logs = ActivityLog::query()
            ->where('family_id', $family->id)
            ->where('activity_id', $activity->id)
            ->with('user')
            ->orderBy('performed_at')
            ->get();

        // Build leaderboard based on most recent performance
        $leaderboard = $this->buildLeaderboard($logs, $activity);

        // Build comparison chart data (all members over time)
        $chartData = $this->buildComparisonChart($logs, $activity, $members);

        return Inertia::render('Stats/Activities/Show', [
            'activity' => $activity->load('activityType'),
            'leaderboard' => $leaderboard,
            'chartData' => $chartData,
            'members' => $members,
        ]);
    }

    /**
     * Build leaderboard ranked by most recent performance.
     *
     * @return array<int, array{member: \App\Models\User, value: float|null, date: string, rank: int}>
     */
    private function buildLeaderboard(Collection $logs, Activity $activity): array
    {
        $recentByMember = $logs
            ->groupBy('user_id')
            ->map(function (Collection $memberLogs) use ($activity) {
                $mostRecent = $memberLogs->sortByDesc('performed_at')->first();

                return [
                    'member' => $mostRecent->user,
                    'value' => $this->getBestValue($mostRecent, $activity),
                    'date' => $mostRecent->performed_at->format('Y-m-d'),
                ];
            })
            ->filter(fn ($entry) => $entry['value'] !== null)
            ->sortByDesc('value')
            ->values();

        return $recentByMember->map(function ($entry, $index) {
            return array_merge($entry, ['rank' => $index + 1]);
        })->all();
    }

    /**
     * Build comparison chart data with all members' progression over time.
     *
     * @return array{dates: array<string>, series: array<int, array{memberId: int, memberName: string, data: array<int, float|null>}>}
     */
    private function buildComparisonChart(Collection $logs, Activity $activity, Collection $members): array
    {
        // Get all unique dates
        $dates = $logs->pluck('performed_at')
            ->map(fn ($date) => $date->format('Y-m-d'))
            ->unique()
            ->sort()
            ->values()
            ->all();

        // Build series for each member
        $series = $members->map(function ($member) use ($logs, $activity, $dates) {
            $memberLogs = $logs->where('user_id', $member->id);

            $data = collect($dates)->map(function ($date) use ($memberLogs, $activity) {
                $log = $memberLogs->first(fn ($l) => $l->performed_at->format('Y-m-d') === $date);

                return $log ? $this->getBestValue($log, $activity) : null;
            })->all();

            return [
                'memberId' => $member->id,
                'memberName' => $member->name,
                'data' => $data,
            ];
        })->filter(function ($series) {
            return collect($series['data'])->filter()->isNotEmpty();
        })->values()->all();

        return [
            'dates' => $dates,
            'series' => $series,
        ];
    }

    /**
     * Get the best/primary value from an activity log based on what the activity tracks.
     */
    private function getBestValue(ActivityLog $log, Activity $activity): ?float
    {
        if ($activity->tracks_weight && $log->weight) {
            return (float) $log->weight;
        }

        if ($activity->tracks_duration && $log->duration_seconds) {
            return (float) $log->duration_seconds;
        }

        if ($activity->tracks_distance && $log->distance) {
            return (float) $log->distance;
        }

        if ($activity->tracks_reps && $log->reps) {
            return (float) $log->reps;
        }

        return null;
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
