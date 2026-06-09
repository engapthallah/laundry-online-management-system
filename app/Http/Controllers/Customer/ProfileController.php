<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit(): View
    {
        $user = Auth::user();
        return view('customer.profile.edit', compact('user'));
    }

    /**
     * Update the customer's profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'current_password' => ['nullable', 'required_with:new_password', 'string'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Verify current password if user is trying to change password
        if (!empty($validated['new_password'])) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors([
                    'current_password' => 'The provided current password does not match our records.'
                ])->withInput();
            }
        }

        DB::transaction(function () use ($user, $validated) {
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->phone = $validated['phone'] ?? null;
            $user->address = $validated['address'] ?? null;

            if (!empty($validated['new_password'])) {
                $user->password = Hash::make($validated['new_password']);
            }

            $user->save();
        });

        return redirect()->route('customer.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}
