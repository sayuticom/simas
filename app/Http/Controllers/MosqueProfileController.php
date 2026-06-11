<?php

namespace App\Http\Controllers;

use App\Models\MosqueProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MosqueProfileController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $activeMosque = $user?->getActiveMosque();
        $availableMosques = $user?->selectableMosques() ?? collect();

        if ($activeMosque) {
            $profile = MosqueProfile::firstOrCreate(
                ['mosque_id' => $activeMosque->id],
                [
                    'nama_masjid' => $activeMosque->name ?? '',
                    'alamat' => $activeMosque->address ?? '',
                    'kelurahan' => '',
                    'kecamatan' => '',
                    'kota' => '',
                    'provinsi' => '',
                    'kode_pos' => '',
                    'no_telepon' => $activeMosque->phone ?? '',
                    'email' => '',
                    'website' => '',
                    'nama_ketua_dkm' => '',
                    'nama_bendahara' => '',
                    'nama_sekretaris' => '',
                    'deskripsi_singkat' => $activeMosque->notes ?? '',
                ]
            );
        } else {
            $profile = new MosqueProfile;
        }

        return view('admin.profile', compact('profile', 'activeMosque', 'availableMosques'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $activeMosque = $user?->getActiveMosque();

        if (! $activeMosque) {
            return redirect()->route('profile')->withErrors(['active_mosque' => 'Masjid aktif tidak ditemukan.']);
        }

        $profile = MosqueProfile::firstOrCreate(
            ['mosque_id' => $activeMosque->id],
            [
                'nama_masjid' => $activeMosque->name ?? '',
                'alamat' => $activeMosque->address ?? '',
                'kelurahan' => '',
                'kecamatan' => '',
                'kota' => '',
                'provinsi' => '',
                'kode_pos' => '',
                'no_telepon' => $activeMosque->phone ?? '',
                'email' => '',
                'website' => '',
                'nama_ketua_dkm' => '',
                'nama_bendahara' => '',
                'nama_sekretaris' => '',
                'deskripsi_singkat' => $activeMosque->notes ?? '',
            ]
        );

        $validated = $request->validate([
            'nama_masjid' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kelurahan' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kota' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'kode_pos' => 'nullable|string|max:20',
            'no_telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'nama_ketua_dkm' => 'nullable|string|max:255',
            'nama_bendahara' => 'nullable|string|max:255',
            'nama_sekretaris' => 'nullable|string|max:255',
            'deskripsi_singkat' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg,gif|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($profile->logo && Storage::disk('public')->exists($profile->logo)) {
                Storage::disk('public')->delete($profile->logo);
            }

            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $profile->update($validated);

        return redirect()->route('profile')->with('success', 'Profil masjid berhasil disimpan.');
    }
}
