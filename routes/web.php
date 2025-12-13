<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityTypeController;
use App\Models\Activity;
use App\Models\ActivityType;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'auth' => [
            'user' => auth()->user(),
        ],
    ]);
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard', [
            'user' => auth()->user(),
            'recentActivities' => Activity::query()
                ->with('activityType')
                ->latest()
                ->take(5)
                ->get(),
            'activityStats' => [
                'total' => Activity::count(),
                'byType' => ActivityType::query()
                    ->withCount('activities')
                    ->orderBy('name')
                    ->get()
                    ->filter(fn (ActivityType $type) => $type->activities_count > 0)
                    ->map(fn (ActivityType $type) => [
                        'name' => $type->name,
                        'color' => $type->color,
                        'count' => $type->activities_count,
                    ])
                    ->values(),
            ],
        ]);
    })->name('dashboard');

    Route::resource('activities', ActivityController::class);
    Route::resource('activity-types', ActivityTypeController::class)->except(['show']);
});
