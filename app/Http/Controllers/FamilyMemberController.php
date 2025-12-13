<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFamilyMemberRequest;
use App\Http\Requests\UpdateFamilyMemberRequest;
use App\Http\Requests\UpgradeFamilyMemberRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class FamilyMemberController extends Controller
{
    /**
     * Display a listing of family members.
     */
    public function index(): Response
    {
        $family = auth()->user()->family;

        return Inertia::render('Family/Members/Index', [
            'members' => $family->members()->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new family member.
     */
    public function create(): Response
    {
        return Inertia::render('Family/Members/Create');
    }

    /**
     * Store a newly created family member.
     */
    public function store(StoreFamilyMemberRequest $request): RedirectResponse
    {
        $family = auth()->user()->family;
        $data = $request->validated();

        $userData = [
            'name' => $data['name'],
            'family_id' => $family->id,
            'can_login' => false,
        ];

        if (! empty($data['email']) && ! empty($data['password'])) {
            $userData['email'] = $data['email'];
            $userData['password'] = Hash::make($data['password']);
            $userData['can_login'] = true;
        }

        User::create($userData);

        return redirect()->route('family.members.index')
            ->with('success', 'Family member added successfully.');
    }

    /**
     * Show the form for editing a family member.
     */
    public function edit(User $member): Response
    {
        $this->authorizeFamilyMember($member);

        return Inertia::render('Family/Members/Edit', [
            'member' => $member,
        ]);
    }

    /**
     * Update the specified family member.
     */
    public function update(UpdateFamilyMemberRequest $request, User $member): RedirectResponse
    {
        $this->authorizeFamilyMember($member);

        $member->update($request->validated());

        return redirect()->route('family.members.index')
            ->with('success', 'Family member updated successfully.');
    }

    /**
     * Remove the specified family member.
     */
    public function destroy(User $member): RedirectResponse
    {
        $this->authorizeFamilyMember($member);

        if ($member->id === auth()->id()) {
            return redirect()->route('family.members.index')
                ->with('error', 'You cannot remove yourself from the family.');
        }

        $member->delete();

        return redirect()->route('family.members.index')
            ->with('success', 'Family member removed successfully.');
    }

    /**
     * Upgrade a non-login member to a login user.
     */
    public function upgradeToLoginUser(UpgradeFamilyMemberRequest $request, User $member): RedirectResponse
    {
        $this->authorizeFamilyMember($member);

        if ($member->can_login) {
            return redirect()->route('family.members.index')
                ->with('error', 'This member already has login credentials.');
        }

        $data = $request->validated();

        $member->update([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'can_login' => true,
        ]);

        return redirect()->route('family.members.index')
            ->with('success', 'Login credentials added successfully.');
    }

    /**
     * Authorize that the member belongs to the current user's family.
     */
    private function authorizeFamilyMember(User $member): void
    {
        if ($member->family_id !== auth()->user()->family_id) {
            abort(403, 'This member does not belong to your family.');
        }
    }
}
