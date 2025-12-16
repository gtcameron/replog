<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityLogRequest;
use App\Http\Requests\UpdateActivityLogRequest;
use App\Models\Activity;
use App\Models\WorkoutActivity;
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
            'logs' => WorkoutActivity::query()
                ->where('family_id', $family->id)
                ->with(['activity', 'user', 'loggedBy', 'sets'])
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

        $workoutActivity = WorkoutActivity::create([
            'family_id' => $family->id,
            'activity_id' => $data['activity_id'],
            'user_id' => $data['user_id'],
            'logged_by_id' => auth()->id(),
            'performed_at' => $data['performed_at'],
            'notes' => $data['notes'] ?? null,
        ]);

        foreach ($data['sets'] as $setData) {
            $workoutActivity->sets()->create($setData);
        }

        return redirect()->route('activity-logs.index')
            ->with('success', 'Activity logged successfully.');
    }

    /**
     * Display the specified activity log.
     */
    public function show(WorkoutActivity $activityLog): Response
    {
        $this->authorizeWorkoutActivity($activityLog);

        return Inertia::render('ActivityLogs/Show', [
            'log' => $activityLog->load(['activity', 'user', 'loggedBy', 'sets']),
        ]);
    }

    /**
     * Show the form for editing an activity log.
     */
    public function edit(WorkoutActivity $activityLog): Response
    {
        $this->authorizeWorkoutActivity($activityLog);

        $family = auth()->user()->family;

        return Inertia::render('ActivityLogs/Edit', [
            'log' => $activityLog->load(['activity', 'user', 'sets']),
            'activities' => Activity::where('family_id', $family->id)
                ->orderBy('name')
                ->get(),
            'members' => $family->members()->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified activity log.
     */
    public function update(UpdateActivityLogRequest $request, WorkoutActivity $activityLog): RedirectResponse
    {
        $this->authorizeWorkoutActivity($activityLog);

        $data = $request->validated();

        $activityLog->update([
            'activity_id' => $data['activity_id'],
            'user_id' => $data['user_id'],
            'performed_at' => $data['performed_at'],
            'notes' => $data['notes'] ?? null,
        ]);

        // Delete existing sets and recreate
        $activityLog->sets()->delete();
        foreach ($data['sets'] as $setData) {
            $activityLog->sets()->create($setData);
        }

        return redirect()->route('activity-logs.index')
            ->with('success', 'Activity log updated successfully.');
    }

    /**
     * Remove the specified activity log.
     */
    public function destroy(WorkoutActivity $activityLog): RedirectResponse
    {
        $this->authorizeWorkoutActivity($activityLog);

        $activityLog->delete();

        return redirect()->route('activity-logs.index')
            ->with('success', 'Activity log deleted successfully.');
    }

    /**
     * Authorize that the workout activity belongs to the current user's family.
     */
    private function authorizeWorkoutActivity(WorkoutActivity $workoutActivity): void
    {
        if ($workoutActivity->family_id !== auth()->user()->family_id) {
            abort(403, 'This activity log does not belong to your family.');
        }
    }
}
