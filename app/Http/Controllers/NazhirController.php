<?php

namespace App\Http\Controllers;

use App\Models\Nazhir;
use Illuminate\Http\Request;

class NazhirController extends Controller
{
    public function index()
    {
        $nazhirs = Nazhir::latest()->paginate(10);

        return view('admin.wakaf.nazhirs.index', compact('nazhirs'));
    }

    public function create()
    {
        return view('admin.wakaf.nazhirs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'nomor_identitas' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        Nazhir::create($data);

        return redirect()->route('wakaf.nazhirs.index')->with('success', 'Data Nazhir berhasil disimpan.');
    }

    public function show(Nazhir $nazhir)
    {
        return view('admin.wakaf.nazhirs.show', compact('nazhir'));
    }

    public function edit(Nazhir $nazhir)
    {
        return view('admin.wakaf.nazhirs.edit', compact('nazhir'));
    }

    public function update(Request $request, Nazhir $nazhir)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'nomor_identitas' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $nazhir->update($data);

        return redirect()->route('wakaf.nazhirs.index')->with('success', 'Data Nazhir berhasil diperbarui.');
    }

    public function destroy(Nazhir $nazhir)
    {
        if (
            $nazhir->wakafCashes()->exists()
            || $nazhir->wakafNonCashes()->exists()
            || $nazhir->wakafAssets()->exists()
        ) {
            return redirect()
                ->route('wakaf.nazhirs.index')
                ->with('error', 'Data Nazhir tidak dapat dihapus karena sudah digunakan pada transaksi atau data aset wakaf.');
        }

        $nazhir->delete();

        return redirect()->route('wakaf.nazhirs.index')->with('success', 'Data Nazhir berhasil dihapus.');
    }
}
