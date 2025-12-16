<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ActivityStatsController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\MemberStatsController;
use App\Http\Controllers\WorkoutActivityLogController;
use App\Http\Controllers\WorkoutController;
use App\Models\Activity;
use App\Models\WorkoutActivity;
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
        $family = auth()->user()->family;

        return Inertia::render('Dashboard', [
            'user' => auth()->user(),
            'recentActivities' => Activity::query()
                ->where('family_id', $family->id)
                ->latest()
                ->take(5)
                ->get(),
            'recentLogs' => WorkoutActivity::query()
                ->where('family_id', $family->id)
                ->with(['activity', 'user', 'sets'])
                ->orderByDesc('performed_at')
                ->orderByDesc('created_at')
                ->take(5)
                ->get(),
            'activityStats' => [
                'total' => Activity::where('family_id', $family->id)->count(),
                'logsThisWeek' => WorkoutActivity::where('family_id', $family->id)
                    ->where('performed_at', '>=', now()->startOfWeek())
                    ->count(),
            ],
        ]);
    })->name('dashboard');

    // Activity resources
    Route::resource('activities', ActivityController::class);

    // Activity logs
    Route::resource('activity-logs', ActivityLogController::class)
        ->parameters(['activity-logs' => 'activityLog:id']);

    // Workouts
    Route::resource('workouts', WorkoutController::class);
    Route::post('workouts/{workout}/end', [WorkoutController::class, 'end'])->name('workouts.end');
    Route::post('workouts/{workout}/logs', [WorkoutActivityLogController::class, 'store'])->name('workouts.logs.store');
    Route::get('workouts/{workout}/activities/{activity}/history', [WorkoutActivityLogController::class, 'activityHistory'])->name('workouts.activities.history');

    // Family management
    Route::get('/family', [FamilyController::class, 'edit'])->name('family.edit');
    Route::put('/family', [FamilyController::class, 'update'])->name('family.update');

    // Family members
    Route::resource('family/members', FamilyMemberController::class)
        ->names('family.members')
        ->parameters(['members' => 'member'])
        ->except(['show']);
    Route::post('family/members/{member}/upgrade', [FamilyMemberController::class, 'upgradeToLoginUser'])
        ->name('family.members.upgrade');

    // Stats routes
    Route::prefix('stats')->group(function () {
        Route::get('members', [MemberStatsController::class, 'index'])->name('stats.members.index');
        Route::get('members/{member}', [MemberStatsController::class, 'show'])->name('stats.members.show');
        Route::get('activities', [ActivityStatsController::class, 'index'])->name('stats.activities.index');
        Route::get('activities/{activity}', [ActivityStatsController::class, 'show'])->name('stats.activities.show');
    });
});
