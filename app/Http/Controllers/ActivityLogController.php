<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityLogRequest;
use App\Http\Requests\UpdateActivityLogRequest;
use App\Models\Activity;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs.
     */
    public function index(): Response
    {
        $family = auth()->user()->family;

        return Inertia::render('ActivityLogs/Index', [
            'logs' => ActivityLog::query()
                ->where('family_id', $family->id)
                ->with(['activity', 'user', 'loggedBy'])
                ->orderByDesc('performed_at')
                ->orderByDesc('created_at')
                ->paginate(20),
            'activities' => Activity::where('family_id', $family->id)
                ->orderBy('name')
                ->get(),
            'members' => $family->members()->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new activity log.
     */
    public function create(): Response
    {
        $family = auth()->user()->family;

        return Inertia::render('ActivityLogs/Create', [
            'activities' => Activity::where('family_id', $family->id)
                ->with('activityType')
                ->orderBy('name')
                ->get(),
            'members' => $family->members()->orderBy('name')->get(),
            'defaultUserId' => auth()->id(),
        ]);
    }

    /**
     * Store a newly created activity log.
     */
    public function store(StoreActivityLogRequest $request): RedirectResponse
    {
        $family = auth()->user()->family;
        $data = $request->validated();

        ActivityLog::create([
            'family_id' => $family->id,
            'activity_id' => $data['activity_id'],
            'user_id' => $data['user_id'],
            'logged_by_id' => auth()->id(),
            'performed_at' => $data['performed_at'],
            'sets' => $data['sets'] ?? null,
            'reps' => $data['reps'] ?? null,
            'weight' => $data['weight'] ?? null,
            'duration_seconds' => $data['duration_seconds'] ?? null,
            'distance' => $data['distance'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('activity-logs.index')
            ->with('success', 'Activity logged successfully.');
    }

    /**
     * Display the specified activity log.
     */
    public function show(ActivityLog $activityLog): Response
    {
        $this->authorizeActivityLog($activityLog);

        return Inertia::render('ActivityLogs/Show', [
            'log' => $activityLog->load(['activity', 'user', 'loggedBy']),
        ]);
    }

    /**
     * Show the form for editing an activity log.
     */
    public function edit(ActivityLog $activityLog): Response
    {
        $this->authorizeActivityLog($activityLog);

        $family = auth()->user()->family;

        return Inertia::render('ActivityLogs/Edit', [
            'log' => $activityLog->load(['activity', 'user']),
            'activities' => Activity::where('family_id', $family->id)
                ->with('activityType')
                ->orderBy('name')
                ->get(),
            'members' => $family->members()->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified activity log.
     */
    public function update(UpdateActivityLogRequest $request, ActivityLog $activityLog): RedirectResponse
    {
        $this->authorizeActivityLog($activityLog);

        $activityLog->update($request->validated());

        return redirect()->route('activity-logs.index')
            ->with('success', 'Activity log updated successfully.');
    }

    /**
     * Remove the specified activity log.
     */
    public function destroy(ActivityLog $activityLog): RedirectResponse
    {
        $this->authorizeActivityLog($activityLog);

        $activityLog->delete();

        return redirect()->route('activity-logs.index')
            ->with('success', 'Activity log deleted successfully.');
    }

    /**
     * Authorize that the activity log belongs to the current user's family.
     */
    private function authorizeActivityLog(ActivityLog $activityLog): void
    {
        if ($activityLog->family_id !== auth()->user()->family_id) {
            abort(403, 'This activity log does not belong to your family.');
        }
    }
}
