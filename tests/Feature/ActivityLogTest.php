<?php

use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\Family;
use App\Models\User;

beforeEach(function () {
    $this->family = Family::factory()->create();
    $this->user = User::factory()->forFamily($this->family)->create();
    $this->activity = Activity::factory()->forFamily($this->family)->create();
});

it('can list activity logs', function () {
    ActivityLog::factory()
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
        'sets' => 3,
        'reps' => 10,
        'weight' => 135.5,
    ]);

    $response->assertRedirect('/activity-logs');

    $this->assertDatabaseHas('activity_logs', [
        'family_id' => $this->family->id,
        'activity_id' => $this->activity->id,
        'user_id' => $this->user->id,
        'logged_by_id' => $this->user->id,
        'sets' => 3,
        'reps' => 10,
    ]);
});

it('can create an activity log for another family member', function () {
    $familyMember = User::factory()->forFamily($this->family)->withoutLogin()->create();

    $response = $this->actingAs($this->user)->post('/activity-logs', [
        'activity_id' => $this->activity->id,
        'user_id' => $familyMember->id,
        'performed_at' => now()->toDateString(),
        'sets' => 3,
        'reps' => 10,
    ]);

    $response->assertRedirect('/activity-logs');

    $this->assertDatabaseHas('activity_logs', [
        'activity_id' => $this->activity->id,
        'user_id' => $familyMember->id,
        'logged_by_id' => $this->user->id,
    ]);
});

it('can update an activity log', function () {
    $log = ActivityLog::factory()
        ->forFamily($this->family)
        ->forUser($this->user)
        ->create(['activity_id' => $this->activity->id]);

    $response = $this->actingAs($this->user)->put("/activity-logs/{$log->id}", [
        'activity_id' => $this->activity->id,
        'user_id' => $this->user->id,
        'performed_at' => now()->toDateString(),
        'sets' => 5,
        'reps' => 12,
    ]);

    $response->assertRedirect('/activity-logs');

    $log->refresh();
    expect($log->sets)->toBe(5);
    expect($log->reps)->toBe(12);
});

it('can delete an activity log', function () {
    $log = ActivityLog::factory()
        ->forFamily($this->family)
        ->forUser($this->user)
        ->create(['activity_id' => $this->activity->id]);

    $response = $this->actingAs($this->user)->delete("/activity-logs/{$log->id}");

    $response->assertRedirect('/activity-logs');

    $this->assertDatabaseMissing('activity_logs', [
        'id' => $log->id,
    ]);
});

it('cannot access another family activity log', function () {
    $otherFamily = Family::factory()->create();
    $otherUser = User::factory()->forFamily($otherFamily)->create();
    $otherActivity = Activity::factory()->forFamily($otherFamily)->create();
    $otherLog = ActivityLog::factory()
        ->forFamily($otherFamily)
        ->forUser($otherUser)
        ->create(['activity_id' => $otherActivity->id]);

    $response = $this->actingAs($this->user)->get("/activity-logs/{$otherLog->id}/edit");

    $response->assertForbidden();
});
