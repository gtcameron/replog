<?php

use App\Models\Family;
use App\Models\User;

beforeEach(function () {
    $this->family = Family::factory()->create();
    $this->user = User::factory()->forFamily($this->family)->create();
});

it('can list family members', function () {
    $members = User::factory()->count(3)->forFamily($this->family)->create();

    $response = $this->actingAs($this->user)->get('/family/members');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Family/Members/Index')
        ->has('members', 4) // 3 + the authenticated user
    );
});

it('can create a family member without login credentials', function () {
    $response = $this->actingAs($this->user)->post('/family/members', [
        'name' => 'New Member',
    ]);

    $response->assertRedirect('/family/members');

    $this->assertDatabaseHas('users', [
        'name' => 'New Member',
        'family_id' => $this->family->id,
        'email' => null,
        'can_login' => false,
    ]);
});

it('can create a family member with login credentials', function () {
    $response = $this->actingAs($this->user)->post('/family/members', [
        'name' => 'New Member',
        'email' => 'newmember@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect('/family/members');

    $this->assertDatabaseHas('users', [
        'name' => 'New Member',
        'family_id' => $this->family->id,
        'email' => 'newmember@example.com',
        'can_login' => true,
    ]);
});

it('can update a family member', function () {
    $member = User::factory()->forFamily($this->family)->withoutLogin()->create();

    $response = $this->actingAs($this->user)->put("/family/members/{$member->id}", [
        'name' => 'Updated Name',
    ]);

    $response->assertRedirect('/family/members');

    $this->assertDatabaseHas('users', [
        'id' => $member->id,
        'name' => 'Updated Name',
    ]);
});

it('can delete a family member', function () {
    $member = User::factory()->forFamily($this->family)->withoutLogin()->create();

    $response = $this->actingAs($this->user)->delete("/family/members/{$member->id}");

    $response->assertRedirect('/family/members');

    $this->assertDatabaseMissing('users', [
        'id' => $member->id,
    ]);
});

it('cannot delete yourself', function () {
    $response = $this->actingAs($this->user)->delete("/family/members/{$this->user->id}");

    $response->assertRedirect('/family/members');

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
    ]);
});

it('can upgrade a non-login member to login user', function () {
    $member = User::factory()->forFamily($this->family)->withoutLogin()->create();

    $response = $this->actingAs($this->user)->post("/family/members/{$member->id}/upgrade", [
        'email' => 'upgraded@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect('/family/members');

    $member->refresh();
    expect($member->can_login)->toBeTrue();
    expect($member->email)->toBe('upgraded@example.com');
});

it('cannot access another family member', function () {
    $otherFamily = Family::factory()->create();
    $otherMember = User::factory()->forFamily($otherFamily)->create();

    $response = $this->actingAs($this->user)->put("/family/members/{$otherMember->id}", [
        'name' => 'Hacked Name',
    ]);

    $response->assertForbidden();
});
