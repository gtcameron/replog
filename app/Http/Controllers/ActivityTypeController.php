<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityTypeRequest;
use App\Http\Requests\UpdateActivityTypeRequest;
use App\Models\ActivityType;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ActivityTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('ActivityTypes/Index', [
            'activityTypes' => ActivityType::query()
                ->withCount('activities')
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('ActivityTypes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActivityTypeRequest $request): RedirectResponse
    {
        ActivityType::create($request->validated());

        return redirect()->route('activity-types.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ActivityType $activityType): Response
    {
        return Inertia::render('ActivityTypes/Edit', [
            'activityType' => $activityType,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateActivityTypeRequest $request, ActivityType $activityType): RedirectResponse
    {
        $activityType->update($request->validated());

        return redirect()->route('activity-types.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ActivityType $activityType): RedirectResponse
    {
        $activityType->delete();

        return redirect()->route('activity-types.index')
            ->with('success', 'Category deleted successfully.');
    }
}
