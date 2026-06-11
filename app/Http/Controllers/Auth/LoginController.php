<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // 1. Superuser: Langsung ke dashboard
            if ($user->isSuperuser()) {
                $user->setActiveMosque(null);
                $request->session()->forget('active_mosque_id');

                return redirect()->route('dashboard');
            }

            // 2. Cek akses masjid untuk user biasa
            $mosques = $user->mosques;
            if ($mosques->isEmpty()) {
                Auth::logout();

                return back()->withErrors(['email' => 'Akun Anda belum terhubung ke masjid manapun.']);
            }

            // Jika user punya lebih dari 1 masjid, jangan arahkan langsung —
            // biarkan pengguna memilih dari dashboard. Jika hanya 1, tetap set otomatis.
            $count = $user->mosques()->count();
            if ($count === 1) {
                $mosqueId = $user->mosques()->first()->id;
                $user->setActiveMosque($mosqueId);
                $request->session()->put('active_mosque_id', $mosqueId);
            }

            return redirect()->route('dashboard');
        }

        return back()->withErrors(['email' => 'Email atau password salah'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->forget('active_mosque_id');

        return redirect()->route('login');
    }
}
