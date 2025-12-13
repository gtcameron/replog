<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateFamilyRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FamilyController extends Controller
{
    /**
     * Show the form for editing the family settings.
     */
    public function edit(): Response
    {
        $family = auth()->user()->family;

        return Inertia::render('Family/Edit', [
            'family' => $family,
        ]);
    }

    /**
     * Update the family settings.
     */
    public function update(UpdateFamilyRequest $request): RedirectResponse
    {
        $family = auth()->user()->family;
        $family->update($request->validated());

        return redirect()->route('family.edit')
            ->with('success', 'Family settings updated successfully.');
    }
}
