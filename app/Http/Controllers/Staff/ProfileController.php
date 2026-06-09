<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the profile edit page.
     */
    public function edit(): View
    {
        $user = Auth::user();
        return view('staff.profile.edit', compact('user'));
    }

    /**
     * Update the authenticated staff member's profile settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Standard validation
        $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone'            => ['nullable', 'string', 'max:20'],
            'address'          => ['nullable', 'string'],
            'current_password' => ['nullable', 'required_with:new_password', 'string'],
            'new_password'     => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Check if password update is requested
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()
                    ->withErrors(['current_password' => 'The provided current password does not match our records.'])
                    ->withInput();
            }

            $user->password = Hash::make($request->new_password);
        }

        // Update other fields
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->save();

        return redirect()->route('staff.profile.edit')->with('success', 'Profile settings updated successfully.');
    }
}
