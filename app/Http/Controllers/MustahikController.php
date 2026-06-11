<?php

namespace App\Http\Controllers;

use App\Models\Mustahik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MustahikController extends Controller
{
    public function index()
    {
        $mustahiks = Mustahik::latest()->paginate(10);

        return view('admin.zis.mustahiks.index', compact('mustahiks'));
    }

    public function create()
    {
        return view('admin.zis.mustahiks.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'kategori_asnaf' => 'required|string',
            'kondisi_ekonomi' => 'nullable|string',
            'jumlah_tanggungan' => 'nullable|integer|min:0',
            'status_verifikasi' => 'boolean',
            'catatan_survei' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('zis/mustahik', 'public');
        }

        Mustahik::create($data);

        return redirect()->route('zis.mustahiks.index')->with('success', 'Data Mustahik berhasil disimpan.');
    }

    public function show(Mustahik $mustahik)
    {
        return view('admin.zis.mustahiks.show', compact('mustahik'));
    }

    public function edit(Mustahik $mustahik)
    {
        return view('admin.zis.mustahiks.edit', compact('mustahik'));
    }

    public function update(Request $request, Mustahik $mustahik)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'kategori_asnaf' => 'required|string',
            'kondisi_ekonomi' => 'nullable|string',
            'jumlah_tanggungan' => 'nullable|integer|min:0',
            'status_verifikasi' => 'boolean',
            'catatan_survei' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($mustahik->foto) {
                Storage::disk('public')->delete($mustahik->foto);
            }
            $data['foto'] = $request->file('foto')->store('zis/mustahik', 'public');
        }

        $mustahik->update($data);

        return redirect()->route('zis.mustahiks.index')->with('success', 'Data Mustahik berhasil diperbarui.');
    }

    public function destroy(Mustahik $mustahik)
    {
        if ($mustahik->foto) {
            Storage::disk('public')->delete($mustahik->foto);
        }
        $mustahik->delete();

        return redirect()->route('zis.mustahiks.index')->with('success', 'Data Mustahik berhasil dihapus.');
    }
}
