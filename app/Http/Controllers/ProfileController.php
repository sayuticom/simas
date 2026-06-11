<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display mosque profile page
     */
    public function index(): View
    {
        return view('admin.profile');
    }

    public function editPassword(): View
    {
        return view('profile.password');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Password lama tidak sesuai.',
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        return redirect()
            ->route('account.password.edit')
            ->with('success', 'Password akun berhasil diperbarui.');
    }
}
