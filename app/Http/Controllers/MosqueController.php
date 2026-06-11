<?php

namespace App\Http\Controllers;

use App\Models\Mosque;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MosqueController extends Controller
{
    public function create()
    {
        return view('auth.mosque-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
        ]);

        $mosque = Mosque::create([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
        ]);

        $user = Auth::user();
        // Assign admin role for this mosque to the creator
        $user->assignRole(Role::ADMIN_MASJID, $mosque->id);
        $user->setActiveMosque($mosque->id);
        $request->session()->put('active_mosque_id', $mosque->id);

        return redirect()->route('dashboard')->with('success', 'Masjid baru berhasil dibuat dan dipilih sebagai aktif.');
    }
}
