<?php

namespace App\Http\Controllers;

use App\Enums\EquipmentType;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $family = auth()->user()->family;

        return Inertia::render('Activities/Index', [
            'activities' => Activity::query()
                ->where('family_id', $family->id)
                ->orderBy('name')
                ->get(),
            'equipmentTypes' => collect(EquipmentType::cases())->map(fn (EquipmentType $type) => [
                'value' => $type->value,
                'label' => $type->label(),
            ]),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Activities/Create', [
            'equipmentTypes' => collect(EquipmentType::cases())->map(fn (EquipmentType $type) => [
                'value' => $type->value,
                'label' => $type->label(),
            ]),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActivityRequest $request): RedirectResponse
    {
        $family = auth()->user()->family;

        Activity::create([
            ...$request->validated(),
            'family_id' => $family->id,
        ]);

        return redirect()->route('activities.index')
            ->with('success', 'Activity created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity): Response
    {
        $this->authorizeActivity($activity);

        return Inertia::render('Activities/Show', [
            'activity' => $activity,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity): Response
    {
        $this->authorizeActivity($activity);

        return Inertia::render('Activities/Edit', [
            'activity' => $activity,
            'equipmentTypes' => collect(EquipmentType::cases())->map(fn (EquipmentType $type) => [
                'value' => $type->value,
                'label' => $type->label(),
            ]),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateActivityRequest $request, Activity $activity): RedirectResponse
    {
        $this->authorizeActivity($activity);

        $activity->update($request->validated());

        return redirect()->route('activities.index')
            ->with('success', 'Activity updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity): RedirectResponse
    {
        $this->authorizeActivity($activity);

        $activity->delete();

        return redirect()->route('activities.index')
            ->with('success', 'Activity deleted successfully.');
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
