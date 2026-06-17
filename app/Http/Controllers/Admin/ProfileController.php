<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show admin profile page.
     */
    public function index()
    {
        $admin = Auth::user();
        return view('admin.profile.index', compact('admin'));
    }

    /**
     * Update admin name and email.
     */
    public function updateInfo(Request $request)
    {
        $admin = Auth::user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $admin->id],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $admin->update($validated);

        return redirect()
            ->route('admin.profile.index')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update admin password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()
                ->withErrors([
                    'current_password' => 'The current password is incorrect.'
                ])
                ->with('password_error', true);
        }

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('admin.profile.index')
            ->with('success', 'Password changed successfully.');
    }
}
