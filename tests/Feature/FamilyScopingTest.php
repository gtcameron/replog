<?php

use App\Models\Activity;
use App\Models\ActivityType;
use App\Models\Family;
use App\Models\User;

beforeEach(function () {
    $this->family = Family::factory()->create();
    $this->user = User::factory()->forFamily($this->family)->create();

    $this->otherFamily = Family::factory()->create();
    $this->otherUser = User::factory()->forFamily($this->otherFamily)->create();
});

it('can only see activities from own family', function () {
    $ownActivity = Activity::factory()->forFamily($this->family)->create();
    $otherActivity = Activity::factory()->forFamily($this->otherFamily)->create();

    $response = $this->actingAs($this->user)->get('/activities');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Activities/Index')
        ->has('activities', 1)
        ->where('activities.0.id', $ownActivity->id)
    );
});

it('cannot access activity from another family', function () {
    $otherActivity = Activity::factory()->forFamily($this->otherFamily)->create();

    $response = $this->actingAs($this->user)->get("/activities/{$otherActivity->id}");

    $response->assertForbidden();
});

it('creates activity with correct family_id', function () {
    $response = $this->actingAs($this->user)->post('/activities', [
        'name' => 'New Activity',
    ]);

    $response->assertRedirect('/activities');

    $this->assertDatabaseHas('activities', [
        'name' => 'New Activity',
        'family_id' => $this->family->id,
    ]);
});

it('cannot update activity from another family', function () {
    $otherActivity = Activity::factory()->forFamily($this->otherFamily)->create();

    $response = $this->actingAs($this->user)->put("/activities/{$otherActivity->id}", [
        'name' => 'Hacked Name',
    ]);

    $response->assertForbidden();
});

it('can only see activity types from own family', function () {
    $ownType = ActivityType::factory()->forFamily($this->family)->create();
    $otherType = ActivityType::factory()->forFamily($this->otherFamily)->create();

    $response = $this->actingAs($this->user)->get('/activity-types');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('ActivityTypes/Index')
        ->has('activityTypes', 1)
        ->where('activityTypes.0.id', $ownType->id)
    );
});

it('creates activity type with correct family_id', function () {
    $response = $this->actingAs($this->user)->post('/activity-types', [
        'name' => 'New Type',
        'color' => '#ff0000',
    ]);

    $response->assertRedirect('/activity-types');

    $this->assertDatabaseHas('activity_types', [
        'name' => 'New Type',
        'family_id' => $this->family->id,
    ]);
});

it('cannot update activity type from another family', function () {
    $otherType = ActivityType::factory()->forFamily($this->otherFamily)->create();

    $response = $this->actingAs($this->user)->put("/activity-types/{$otherType->id}", [
        'name' => 'Hacked Type',
    ]);

    $response->assertForbidden();
});
