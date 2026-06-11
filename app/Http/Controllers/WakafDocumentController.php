<?php

namespace App\Http\Controllers;

use App\Models\WakafAsset;
use App\Models\WakafDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class WakafDocumentController extends Controller
{
    public function index()
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId) {
            return redirect()->route('wakaf.index')->with('error', 'Pilih masjid aktif terlebih dahulu.');
        }

        $documents = WakafDocument::with('wakafAsset')
            ->where('mosque_id', $mosqueId)
            ->latest()
            ->paginate(10);

        return view('admin.wakaf.documents.index', compact('documents'));
    }

    public function create()
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId) {
            return redirect()->route('wakaf.index')->with('error', 'Pilih masjid aktif terlebih dahulu.');
        }

        $assets = $this->assetOptions($mosqueId);

        return view('admin.wakaf.documents.create', compact('assets'));
    }

    public function store(Request $request)
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId) {
            return redirect()->route('wakaf.index')->with('error', 'Pilih masjid aktif terlebih dahulu.');
        }

        $data = $request->validate($this->rules($mosqueId));
        $data['mosque_id'] = $mosqueId;

        if ($request->hasFile('file_dokumen')) {
            $data['file_dokumen'] = $request->file('file_dokumen')->store('wakaf/documents', 'public');
        }

        WakafDocument::create($data);

        return redirect()->route('wakaf.documents.index')->with('success', 'Dokumen Wakaf berhasil disimpan.');
    }

    public function show(WakafDocument $document)
    {
        $this->authorizeDocument($document);
        $document->load('wakafAsset');

        return view('admin.wakaf.documents.show', compact('document'));
    }

    public function edit(WakafDocument $document)
    {
        $this->authorizeDocument($document);

        $mosqueId = $this->activeMosqueId();
        $assets = $this->assetOptions($mosqueId);

        return view('admin.wakaf.documents.edit', compact('document', 'assets'));
    }

    public function update(Request $request, WakafDocument $document)
    {
        $this->authorizeDocument($document);

        $mosqueId = $this->activeMosqueId();
        $data = $request->validate($this->rules($mosqueId));

        if ($request->hasFile('file_dokumen')) {
            if ($document->file_dokumen && Storage::disk('public')->exists($document->file_dokumen)) {
                Storage::disk('public')->delete($document->file_dokumen);
            }

            $data['file_dokumen'] = $request->file('file_dokumen')->store('wakaf/documents', 'public');
        }

        $document->update($data);

        return redirect()->route('wakaf.documents.index')->with('success', 'Dokumen Wakaf berhasil diperbarui.');
    }

    public function destroy(WakafDocument $document)
    {
        $this->authorizeDocument($document);

        if ($document->file_dokumen && Storage::disk('public')->exists($document->file_dokumen)) {
            Storage::disk('public')->delete($document->file_dokumen);
        }

        $document->delete();

        return redirect()->route('wakaf.documents.index')->with('success', 'Dokumen Wakaf berhasil dihapus.');
    }

    private function rules(int $mosqueId): array
    {
        return [
            'waqf_asset_id' => [
                'required',
                Rule::exists('wakaf_assets', 'id')->where('mosque_id', $mosqueId),
            ],
            'jenis_dokumen' => 'nullable|string',
            'nomor_dokumen' => 'nullable|string',
            'file_dokumen' => 'nullable|file|max:4096',
            'tanggal_terbit' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_terbit',
            'keterangan' => 'nullable|string',
        ];
    }

    private function assetOptions(int $mosqueId)
    {
        return WakafAsset::where('mosque_id', $mosqueId)
            ->orderBy('nama_aset')
            ->get();
    }

    private function activeMosqueId(): ?int
    {
        return session('active_mosque_id') ?: auth()->user()?->active_mosque_id;
    }

    private function authorizeDocument(WakafDocument $document): void
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId || (int) $document->mosque_id !== (int) $mosqueId) {
            abort(404);
        }
    }
}
