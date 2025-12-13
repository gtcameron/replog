<?php

use App\Enums\EquipmentType;
use App\Http\Controllers\ExerciseController;
use App\Models\Exercise;
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
            'recentExercises' => Exercise::query()->latest()->take(5)->get(),
            'exerciseStats' => [
                'total' => Exercise::count(),
                'byEquipment' => collect(EquipmentType::cases())->map(fn (EquipmentType $type) => [
                    'type' => $type->label(),
                    'count' => Exercise::where('equipment_type', $type->value)->count(),
                ])->filter(fn ($item) => $item['count'] > 0)->values(),
            ],
        ]);
    })->name('dashboard');

    Route::resource('exercises', ExerciseController::class);
});
