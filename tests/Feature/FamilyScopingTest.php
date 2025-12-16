<?php

use App\Models\Activity;
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
