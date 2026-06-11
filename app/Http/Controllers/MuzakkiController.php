<?php

namespace App\Http\Controllers;

use App\Models\Muzakki;
use Illuminate\Http\Request;

class MuzakkiController extends Controller
{
    public function index()
    {
        $muzakkis = Muzakki::latest()->paginate(10);

        return view('admin.zis.muzakkis.index', compact('muzakkis'));
    }

    public function create()
    {
        return view('admin.zis.muzakkis.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'nama_kepala_keluarga' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'jumlah_anggota_keluarga' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        Muzakki::create($data);

        return redirect()->route('zis.muzakkis.index')->with('success', 'Data Muzakki berhasil disimpan.');
    }

    public function show(Muzakki $muzakki)
    {
        return view('admin.zis.muzakkis.show', compact('muzakki'));
    }

    public function edit(Muzakki $muzakki)
    {
        return view('admin.zis.muzakkis.edit', compact('muzakki'));
    }

    public function update(Request $request, Muzakki $muzakki)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'nama_kepala_keluarga' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'jumlah_anggota_keluarga' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $muzakki->update($data);

        return redirect()->route('zis.muzakkis.index')->with('success', 'Data Muzakki berhasil diperbarui.');
    }

    public function destroy(Muzakki $muzakki)
    {
        $muzakki->delete();

        return redirect()->route('zis.muzakkis.index')->with('success', 'Data Muzakki berhasil dihapus.');
    }
}
