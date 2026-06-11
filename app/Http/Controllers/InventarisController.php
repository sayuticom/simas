<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class InventarisController extends Controller
{
    public function index()
    {
        $inventaris = Inventaris::latest()->paginate(10);

        return view('admin.inventaris.index', compact('inventaris'));
    }

    public function create()
    {
        return view('admin.inventaris.create', ['inventaris' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['mosque_id'] = $this->activeMosqueId();
        $data['kondisi'] = $data['kondisi'] ?? 'baik';
        $data['status'] = $data['status'] ?? 'aktif';
        $data['nilai_perolehan'] = $data['nilai_perolehan'] ?? 0;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('inventaris', 'public');
        }

        Inventaris::create($data);

        return redirect()->route('inventaris.index')->with('success', 'Inventaris berhasil disimpan.');
    }

    public function show(Inventaris $inventaris)
    {
        $this->authorizeInventaris($inventaris);

        return view('admin.inventaris.show', ['inventaris' => $inventaris]);
    }

    public function edit(Inventaris $inventaris)
    {
        $this->authorizeInventaris($inventaris);

        return view('admin.inventaris.edit', ['inventaris' => $inventaris]);
    }

    public function update(Request $request, Inventaris $inventaris)
    {
        $this->authorizeInventaris($inventaris);

        $data = $this->validatedData($request);
        $data['kondisi'] = $data['kondisi'] ?? 'baik';
        $data['status'] = $data['status'] ?? 'aktif';
        $data['nilai_perolehan'] = $data['nilai_perolehan'] ?? 0;

        if ($request->hasFile('foto')) {
            if ($inventaris->foto && Storage::disk('public')->exists($inventaris->foto)) {
                Storage::disk('public')->delete($inventaris->foto);
            }

            $data['foto'] = $request->file('foto')->store('inventaris', 'public');
        }

        $inventaris->update($data);

        return redirect()->route('inventaris.index')->with('success', 'Inventaris berhasil diperbarui.');
    }

    public function destroy(Inventaris $inventaris)
    {
        $this->authorizeInventaris($inventaris);

        if ($inventaris->foto && Storage::disk('public')->exists($inventaris->foto)) {
            Storage::disk('public')->delete($inventaris->foto);
        }

        $inventaris->delete();

        return redirect()->route('inventaris.index')->with('success', 'Inventaris berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'kode_barang' => 'nullable|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'merk' => 'nullable|string|max:255',
            'tipe_model' => 'nullable|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'nullable|string|max:255',
            'kondisi' => ['nullable', Rule::in(['baik', 'rusak_ringan', 'rusak_berat', 'hilang'])],
            'lokasi' => 'nullable|string|max:255',
            'tanggal_perolehan' => 'nullable|date',
            'sumber_perolehan' => 'nullable|string|max:255',
            'nilai_perolehan' => 'nullable|numeric|min:0',
            'penanggung_jawab' => 'nullable|string|max:255',
            'foto' => 'nullable|image|max:2048',
            'status' => ['nullable', Rule::in(['aktif', 'nonaktif', 'dipinjam', 'hilang', 'dihapus'])],
            'keterangan' => 'nullable|string',
        ]);
    }

    private function authorizeInventaris(Inventaris $inventaris): void
    {
        $mosqueId = $this->activeMosqueId();

        abort_unless($mosqueId && (int) $inventaris->mosque_id === (int) $mosqueId, 404);
    }

    private function activeMosqueId(): ?int
    {
        return session('active_mosque_id') ?: auth()->user()?->active_mosque_id;
    }
}
