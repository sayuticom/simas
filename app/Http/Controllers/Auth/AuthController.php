<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // 1. Superuser: Bypass pemilihan masjid dan langsung ke dashboard
            if ($user->isSuperuser()) {
                $user->setActiveMosque(null);
                $request->session()->forget('active_mosque_id');

                return redirect()->route('dashboard');
            }

            // 2. Cek jumlah masjid untuk user biasa
            $mosques = $user->selectableMosques();
            if ($mosques->isEmpty()) {
                Auth::logout();

                return back()->withErrors(['email' => 'Akun Anda belum terhubung ke masjid manapun.']);
            }

            if ($mosques->count() === 1) {
                $mosqueId = $mosques->first()->id;
                $user->setActiveMosque($mosqueId);
                $request->session()->put('active_mosque_id', $mosqueId);
            } else {
                $user->clearActiveMosque();
                $request->session()->forget('active_mosque_id');
            }

            // Jika lebih dari 1 masjid, biarkan pengguna memilih dari dashboard.
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah',
        ])->onlyInput('email');
    }

    /**
     * Menampilkan halaman pilih masjid
     */
    public function showMosqueSelect()
    {
        return redirect()->route('dashboard', ['choose_mosque' => 1]);
    }

    /**
     * Mengganti masjid aktif saat sudah di dalam sistem
     */
    public function switchMosque(Request $request)
    {
        $request->validate(['mosque_id' => ['required', 'exists:mosques,id']]);

        if (auth()->user()->setActiveMosque($request->mosque_id)) {
            $request->session()->put('active_mosque_id', $request->mosque_id);

            if ($request->input('redirect_to') === 'profile') {
                return redirect()->route('profile')->with('success', 'Berhasil berganti masjid aktif.');
            }

            if ($request->input('redirect_to') === 'website-settings.edit') {
                return redirect()->route('website-settings.edit')->with('success', 'Berhasil berganti masjid aktif.');
            }

            return redirect()->route('dashboard')->with('success', 'Berhasil berganti masjid.');
        }

        return back()->with('error', 'Gagal berganti masjid.');
    }

    /**
     * Kembali ke daftar seluruh masjid untuk superuser.
     */
    public function showAllMosques(Request $request)
    {
        $user = auth()->user();

        if (! $user->isSuperuser()) {
            abort(403);
        }

        $user->clearActiveMosque();
        $request->session()->forget('active_mosque_id');

        return redirect()->route('dashboard');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
