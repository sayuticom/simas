<?php

namespace App\Http\Controllers;

use App\Models\Wakif;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WakifController extends Controller
{
    public function index()
    {
        $wakifs = Wakif::latest()->paginate(10);

        return view('admin.wakaf.wakifs.index', compact('wakifs'));
    }

    public function create()
    {
        return view('admin.wakaf.wakifs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_wakif' => ['nullable', Rule::in(['perorangan', 'lembaga'])],
            'no_hp' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'nomor_identitas' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        Wakif::create($data);

        return redirect()->route('wakaf.wakifs.index')->with('success', 'Data Wakif berhasil disimpan.');
    }

    public function show(Wakif $wakif)
    {
        return view('admin.wakaf.wakifs.show', compact('wakif'));
    }

    public function edit(Wakif $wakif)
    {
        return view('admin.wakaf.wakifs.edit', compact('wakif'));
    }

    public function update(Request $request, Wakif $wakif)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_wakif' => ['nullable', Rule::in(['perorangan', 'lembaga'])],
            'no_hp' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'nomor_identitas' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $wakif->update($data);

        return redirect()->route('wakaf.wakifs.index')->with('success', 'Data Wakif berhasil diperbarui.');
    }

    public function destroy(Wakif $wakif)
    {
        if ($wakif->wakafCashes()->exists() || $wakif->wakafNonCashes()->exists()) {
            return redirect()
                ->route('wakaf.wakifs.index')
                ->with('error', 'Data Wakif tidak dapat dihapus karena sudah dipakai pada transaksi wakaf.');
        }

        $wakif->delete();

        return redirect()->route('wakaf.wakifs.index')->with('success', 'Data Wakif berhasil dihapus.');
    }
}
