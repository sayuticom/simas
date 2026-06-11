<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DokumenController extends Controller
{
    public function index()
    {
        $dokumens = Dokumen::latest()->paginate(10);

        return view('admin.dokumen.index', compact('dokumens'));
    }

    public function create()
    {
        return view('admin.dokumen.create', ['dokumen' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['mosque_id'] = $this->activeMosqueId();
        $data['status'] = $data['status'] ?? 'aktif';

        if ($request->hasFile('file_dokumen')) {
            $data['file_dokumen'] = $request->file('file_dokumen')->store('dokumen', 'public');
        }

        Dokumen::create($data);

        return redirect()->route('dokumen.index')->with('success', 'Dokumen berhasil disimpan.');
    }

    public function show(Dokumen $dokumen)
    {
        $this->authorizeDokumen($dokumen);

        return view('admin.dokumen.show', ['dokumen' => $dokumen]);
    }

    public function edit(Dokumen $dokumen)
    {
        $this->authorizeDokumen($dokumen);

        return view('admin.dokumen.edit', ['dokumen' => $dokumen]);
    }

    public function update(Request $request, Dokumen $dokumen)
    {
        $this->authorizeDokumen($dokumen);

        $data = $this->validatedData($request);
        $data['status'] = $data['status'] ?? 'aktif';

        if ($request->hasFile('file_dokumen')) {
            if ($dokumen->file_dokumen && Storage::disk('public')->exists($dokumen->file_dokumen)) {
                Storage::disk('public')->delete($dokumen->file_dokumen);
            }

            $data['file_dokumen'] = $request->file('file_dokumen')->store('dokumen', 'public');
        }

        $dokumen->update($data);

        return redirect()->route('dokumen.index')->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(Dokumen $dokumen)
    {
        $this->authorizeDokumen($dokumen);

        if ($dokumen->file_dokumen && Storage::disk('public')->exists($dokumen->file_dokumen)) {
            Storage::disk('public')->delete($dokumen->file_dokumen);
        }

        $dokumen->delete();

        return redirect()->route('dokumen.index')->with('success', 'Dokumen berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'judul' => 'required|string|max:255',
            'jenis_dokumen' => 'nullable|string|max:255',
            'nomor_dokumen' => 'nullable|string|max:255',
            'tanggal_dokumen' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_dokumen',
            'file_dokumen' => 'nullable|file|max:4096',
            'sumber' => 'nullable|string|max:255',
            'status' => ['nullable', Rule::in(['aktif', 'arsip', 'kedaluwarsa'])],
            'keterangan' => 'nullable|string',
        ]);
    }

    private function authorizeDokumen(Dokumen $dokumen): void
    {
        $mosqueId = $this->activeMosqueId();

        abort_unless($mosqueId && (int) $dokumen->mosque_id === (int) $mosqueId, 404);
    }

    private function activeMosqueId(): ?int
    {
        return session('active_mosque_id') ?: auth()->user()?->active_mosque_id;
    }
}
