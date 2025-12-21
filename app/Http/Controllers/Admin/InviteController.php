<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InviteController extends Controller
{
    /**
     * Display a listing of invites.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Invites/Index', [
            'invites' => Invite::query()
                ->orderByDesc('created_at')
                ->get(),
        ]);
    }

    /**
     * Store a newly created invite.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:invites,email'],
        ]);

        Invite::create([
            'email' => strtolower($validated['email']),
        ]);

        return redirect()->route('admin.invites.index')
            ->with('success', 'Invite created successfully.');
    }

    /**
     * Remove the specified invite.
     */
    public function destroy(Invite $invite): RedirectResponse
    {
        $invite->delete();

        return redirect()->route('admin.invites.index')
            ->with('success', 'Invite deleted successfully.');
    }
}
