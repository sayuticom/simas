<?php

namespace App\Http\Controllers;

use App\Models\Nazhir;
use App\Models\WakafNonCash;
use App\Models\Wakif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class WakafNonCashController extends Controller
{
    public function index()
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId) {
            return redirect()->route('wakaf.index')->with('error', 'Pilih masjid aktif terlebih dahulu.');
        }

        $nonCash = WakafNonCash::with(['wakif', 'nazhir'])
            ->where('mosque_id', $mosqueId)
            ->latest()
            ->paginate(10);

        return view('admin.wakaf.non-cash.index', compact('nonCash'));
    }

    public function create()
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId) {
            return redirect()->route('wakaf.index')->with('error', 'Pilih masjid aktif terlebih dahulu.');
        }

        [$wakifs, $nazhirs] = $this->formOptions($mosqueId);

        return view('admin.wakaf.non-cash.create', compact('wakifs', 'nazhirs'));
    }

    public function store(Request $request)
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId) {
            return redirect()->route('wakaf.index')->with('error', 'Pilih masjid aktif terlebih dahulu.');
        }

        $data = $request->validate($this->rules($mosqueId));

        $data['mosque_id'] = $mosqueId;
        $data['nilai_estimasi'] = $data['nilai_estimasi'] ?? 0;

        $this->storeUploads($request, $data);

        WakafNonCash::create($data);

        return redirect()->route('wakaf.non-cash.index')->with('success', 'Wakaf Non-Tunai berhasil disimpan.');
    }

    public function show(WakafNonCash $wakafNonCash)
    {
        $this->authorizeNonCash($wakafNonCash);
        $wakafNonCash->load(['wakif', 'nazhir']);

        return view('admin.wakaf.non-cash.show', compact('wakafNonCash'));
    }

    public function edit(WakafNonCash $wakafNonCash)
    {
        $this->authorizeNonCash($wakafNonCash);

        $mosqueId = $this->activeMosqueId();
        [$wakifs, $nazhirs] = $this->formOptions($mosqueId);

        return view('admin.wakaf.non-cash.edit', compact('wakafNonCash', 'wakifs', 'nazhirs'));
    }

    public function update(Request $request, WakafNonCash $wakafNonCash)
    {
        $this->authorizeNonCash($wakafNonCash);

        $mosqueId = $this->activeMosqueId();
        $data = $request->validate($this->rules($mosqueId));

        $data['nilai_estimasi'] = $data['nilai_estimasi'] ?? 0;

        $this->storeUploads($request, $data, $wakafNonCash);

        $wakafNonCash->update($data);

        return redirect()->route('wakaf.non-cash.index')->with('success', 'Wakaf Non-Tunai berhasil diperbarui.');
    }

    public function destroy(WakafNonCash $wakafNonCash)
    {
        $this->authorizeNonCash($wakafNonCash);

        if ($wakafNonCash->wakafAssets()->exists()) {
            return redirect()
                ->route('wakaf.non-cash.index')
                ->with('error', 'Wakaf Non-Tunai tidak dapat dihapus karena sudah digunakan pada data aset wakaf.');
        }

        $this->deleteStoredFiles($wakafNonCash);

        $wakafNonCash->delete();

        return redirect()->route('wakaf.non-cash.index')->with('success', 'Wakaf Non-Tunai berhasil dihapus.');
    }

    private function rules(int $mosqueId): array
    {
        return [
            'wakif_id' => [
                'required',
                Rule::exists('wakifs', 'id')->where('mosque_id', $mosqueId),
            ],
            'nazhir_id' => [
                'required',
                Rule::exists('nazhirs', 'id')->where('mosque_id', $mosqueId),
            ],
            'tanggal_terima' => 'required|date',
            'jenis_aset' => 'nullable|string',
            'nama_aset' => 'required|string',
            'nilai_estimasi' => 'nullable|numeric|min:0',
            'lokasi' => 'nullable|string',
            'jumlah' => 'nullable|integer|min:0',
            'luas' => 'nullable|string',
            'nomor_sertifikat' => 'nullable|string',
            'status_dokumen' => 'nullable|string',
            'dokumen_ikrar' => 'nullable|file|max:2048',
            'dokumen_aset' => 'nullable|file|max:2048',
            'foto' => 'nullable|image|max:2048',
            'keterangan' => 'nullable|string',
        ];
    }

    private function formOptions(int $mosqueId): array
    {
        return [
            Wakif::where('mosque_id', $mosqueId)->orderBy('nama')->get(),
            Nazhir::where('mosque_id', $mosqueId)->orderBy('nama')->get(),
        ];
    }

    private function activeMosqueId(): ?int
    {
        return session('active_mosque_id') ?: auth()->user()?->active_mosque_id;
    }

    private function authorizeNonCash(WakafNonCash $wakafNonCash): void
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId || (int) $wakafNonCash->mosque_id !== (int) $mosqueId) {
            abort(404);
        }
    }

    private function storeUploads(Request $request, array &$data, ?WakafNonCash $wakafNonCash = null): void
    {
        foreach (['dokumen_ikrar', 'dokumen_aset', 'foto'] as $field) {
            if (! $request->hasFile($field)) {
                continue;
            }

            if ($wakafNonCash?->{$field} && Storage::disk('public')->exists($wakafNonCash->{$field})) {
                Storage::disk('public')->delete($wakafNonCash->{$field});
            }

            $data[$field] = $request->file($field)->store('wakaf/non-cashes', 'public');
        }
    }

    private function deleteStoredFiles(WakafNonCash $wakafNonCash): void
    {
        foreach (['dokumen_ikrar', 'dokumen_aset', 'foto'] as $field) {
            if ($wakafNonCash->{$field} && Storage::disk('public')->exists($wakafNonCash->{$field})) {
                Storage::disk('public')->delete($wakafNonCash->{$field});
            }
        }
    }
}
