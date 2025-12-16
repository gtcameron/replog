<?php

use App\Models\Activity;
use App\Models\Family;
use App\Models\User;
use App\Models\WorkoutActivity;

beforeEach(function () {
    $this->family = Family::factory()->create();
    $this->user = User::factory()->forFamily($this->family)->create();
    $this->activity = Activity::factory()->forFamily($this->family)->create();
});

it('can list activity logs', function () {
    WorkoutActivity::factory()
        ->forFamily($this->family)
        ->forUser($this->user)
        ->count(3)
        ->create(['activity_id' => $this->activity->id]);

    $response = $this->actingAs($this->user)->get('/activity-logs');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('ActivityLogs/Index')
        ->has('logs.data', 3)
    );
});

it('can create an activity log for self', function () {
    $response = $this->actingAs($this->user)->post('/activity-logs', [
        'activity_id' => $this->activity->id,
        'user_id' => $this->user->id,
        'performed_at' => now()->toDateString(),
        'sets' => [
            ['set_number' => 1, 'reps' => 10, 'weight' => 135.5],
            ['set_number' => 2, 'reps' => 8, 'weight' => 145.5],
            ['set_number' => 3, 'reps' => 6, 'weight' => 155.5],
        ],
    ]);

    $response->assertRedirect('/activity-logs');

    $this->assertDatabaseHas('workout_activities', [
        'family_id' => $this->family->id,
        'activity_id' => $this->activity->id,
        'user_id' => $this->user->id,
        'logged_by_id' => $this->user->id,
    ]);

    $workoutActivity = WorkoutActivity::where('activity_id', $this->activity->id)->first();
    expect($workoutActivity->sets)->toHaveCount(3);
    expect($workoutActivity->sets->first()->reps)->toBe(10);
    expect($workoutActivity->sets->first()->weight)->toBe('135.50');
});

it('can create an activity log for another family member', function () {
    $familyMember = User::factory()->forFamily($this->family)->withoutLogin()->create();

    $response = $this->actingAs($this->user)->post('/activity-logs', [
        'activity_id' => $this->activity->id,
        'user_id' => $familyMember->id,
        'performed_at' => now()->toDateString(),
        'sets' => [
            ['set_number' => 1, 'reps' => 10, 'weight' => 100],
        ],
    ]);

    $response->assertRedirect('/activity-logs');

    $this->assertDatabaseHas('workout_activities', [
        'activity_id' => $this->activity->id,
        'user_id' => $familyMember->id,
        'logged_by_id' => $this->user->id,
    ]);
});

it('can update an activity log', function () {
    $log = WorkoutActivity::factory()
        ->forFamily($this->family)
        ->forUser($this->user)
        ->create(['activity_id' => $this->activity->id]);

    $log->sets()->create(['set_number' => 1, 'reps' => 10, 'weight' => 100]);

    $response = $this->actingAs($this->user)->put("/activity-logs/{$log->id}", [
        'activity_id' => $this->activity->id,
        'user_id' => $this->user->id,
        'performed_at' => now()->toDateString(),
        'sets' => [
            ['set_number' => 1, 'reps' => 12, 'weight' => 120],
            ['set_number' => 2, 'reps' => 10, 'weight' => 130],
        ],
    ]);

    $response->assertRedirect('/activity-logs');

    $log->refresh();
    expect($log->sets)->toHaveCount(2);
    expect($log->sets->first()->reps)->toBe(12);
});

it('can delete an activity log', function () {
    $log = WorkoutActivity::factory()
        ->forFamily($this->family)
        ->forUser($this->user)
        ->create(['activity_id' => $this->activity->id]);

    $response = $this->actingAs($this->user)->delete("/activity-logs/{$log->id}");

    $response->assertRedirect('/activity-logs');

    $this->assertDatabaseMissing('workout_activities', [
        'id' => $log->id,
    ]);
});

it('cannot access another family activity log', function () {
    $otherFamily = Family::factory()->create();
    $otherUser = User::factory()->forFamily($otherFamily)->create();
    $otherActivity = Activity::factory()->forFamily($otherFamily)->create();
    $otherLog = WorkoutActivity::factory()
        ->forFamily($otherFamily)
        ->forUser($otherUser)
        ->create(['activity_id' => $otherActivity->id]);

    $response = $this->actingAs($this->user)->get("/activity-logs/{$otherLog->id}/edit");

    $response->assertForbidden();
});
