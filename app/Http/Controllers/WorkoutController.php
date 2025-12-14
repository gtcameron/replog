<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkoutRequest;
use App\Http\Requests\UpdateWorkoutRequest;
use App\Models\Activity;
use App\Models\Workout;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class WorkoutController extends Controller
{
    /**
     * Display a listing of workouts.
     */
    public function index(): Response
    {
        $family = auth()->user()->family;

        return Inertia::render('Workouts/Index', [
            'workouts' => Workout::query()
                ->where('family_id', $family->id)
                ->with(['user'])
                ->withCount('activityLogs')
                ->orderByDesc('started_at')
                ->paginate(20),
        ]);
    }

    /**
     * Show the form for creating a new workout.
     */
    public function create(): Response
    {
        $family = auth()->user()->family;

        return Inertia::render('Workouts/Create', [
            'members' => $family->members()->orderBy('name')->get(),
            'defaultUserId' => auth()->id(),
        ]);
    }

    /**
     * Start a new workout.
     */
    public function store(StoreWorkoutRequest $request): RedirectResponse
    {
        $family = auth()->user()->family;
        $data = $request->validated();

        $workout = Workout::create([
            'family_id' => $family->id,
            'user_id' => $data['user_id'],
            'started_by_id' => auth()->id(),
            'name' => $data['name'] ?? null,
            'started_at' => now(),
        ]);

        return redirect()->route('workouts.show', $workout);
    }

    /**
     * Display the workout (active workout interface).
     */
    public function show(Workout $workout): Response
    {
        $this->authorizeWorkout($workout);

        $family = auth()->user()->family;

        return Inertia::render('Workouts/Show', [
            'workout' => $workout->load(['user', 'activityLogs.activity']),
            'activities' => Activity::where('family_id', $family->id)
                ->with('activityType')
                ->orderBy('name')
                ->get(),
            'members' => $family->members()->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified workout.
     */
    public function update(UpdateWorkoutRequest $request, Workout $workout): RedirectResponse
    {
        $this->authorizeWorkout($workout);

        $workout->update($request->validated());

        return redirect()->route('workouts.show', $workout)
            ->with('success', 'Workout updated successfully.');
    }

    /**
     * Remove the specified workout.
     */
    public function destroy(Workout $workout): RedirectResponse
    {
        $this->authorizeWorkout($workout);

        $workout->delete();

        return redirect()->route('workouts.index')
            ->with('success', 'Workout deleted successfully.');
    }

    /**
     * End the active workout.
     */
    public function end(Workout $workout): RedirectResponse
    {
        $this->authorizeWorkout($workout);

        if (! $workout->isActive()) {
            return redirect()->route('workouts.show', $workout)
                ->with('error', 'This workout has already ended.');
        }

        $workout->update([
            'ended_at' => now(),
        ]);

        return redirect()->route('workouts.show', $workout)
            ->with('success', 'Workout completed!');
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
}
