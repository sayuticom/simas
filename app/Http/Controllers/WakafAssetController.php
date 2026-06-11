<?php

namespace App\Http\Controllers;

use App\Models\Nazhir;
use App\Models\WakafAsset;
use App\Models\WakafCash;
use App\Models\WakafNonCash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WakafAssetController extends Controller
{
    public function index()
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId) {
            return redirect()->route('wakaf.index')->with('error', 'Pilih masjid aktif terlebih dahulu.');
        }

        $assets = WakafAsset::with(['nazhir', 'wakafCash', 'wakafNonCash'])
            ->where('mosque_id', $mosqueId)
            ->latest()
            ->paginate(10);

        return view('admin.wakaf.assets.index', compact('assets'));
    }

    public function create()
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId) {
            return redirect()->route('wakaf.index')->with('error', 'Pilih masjid aktif terlebih dahulu.');
        }

        [$nazhirs, $wakafCashes, $wakafNonCashes] = $this->formOptions($mosqueId);

        return view('admin.wakaf.assets.create', compact('nazhirs', 'wakafCashes', 'wakafNonCashes'));
    }

    public function store(Request $request)
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId) {
            return redirect()->route('wakaf.index')->with('error', 'Pilih masjid aktif terlebih dahulu.');
        }

        $data = $this->validatedData($request, $mosqueId);
        $data['mosque_id'] = $mosqueId;

        WakafAsset::create($data);

        return redirect()->route('wakaf.assets.index')->with('success', 'Aset Wakaf berhasil disimpan.');
    }

    public function show(WakafAsset $asset)
    {
        $this->authorizeAsset($asset);
        $asset->load(['nazhir', 'wakafCash.wakif', 'wakafNonCash.wakif']);

        return view('admin.wakaf.assets.show', ['asset' => $asset]);
    }

    public function edit(WakafAsset $asset)
    {
        $this->authorizeAsset($asset);

        $mosqueId = $this->activeMosqueId();
        [$nazhirs, $wakafCashes, $wakafNonCashes] = $this->formOptions($mosqueId);

        return view('admin.wakaf.assets.edit', compact('asset', 'nazhirs', 'wakafCashes', 'wakafNonCashes'));
    }

    public function update(Request $request, WakafAsset $asset)
    {
        $this->authorizeAsset($asset);

        $mosqueId = $this->activeMosqueId();
        $data = $this->validatedData($request, $mosqueId);

        $asset->update($data);

        return redirect()->route('wakaf.assets.index')->with('success', 'Aset Wakaf berhasil diperbarui.');
    }

    public function destroy(WakafAsset $asset)
    {
        $this->authorizeAsset($asset);

        if ($asset->productiveAssets()->exists() || $asset->documents()->exists() || $asset->maintenances()->exists()) {
            return redirect()
                ->route('wakaf.assets.index')
                ->with('error', 'Aset Wakaf tidak dapat dihapus karena sudah memiliki data produktif, dokumen, atau perawatan.');
        }

        $asset->delete();

        return redirect()->route('wakaf.assets.index')->with('success', 'Aset Wakaf berhasil dihapus.');
    }

    private function validatedData(Request $request, int $mosqueId): array
    {
        $data = $request->validate([
            'sumber_wakaf' => ['required', Rule::in(['wakaf_tunai', 'wakaf_non_tunai', 'lainnya'])],
            'wakaf_tunai_id' => [
                'nullable',
                Rule::exists('wakaf_cashes', 'id')->where('mosque_id', $mosqueId),
            ],
            'wakaf_non_tunai_id' => [
                'nullable',
                Rule::exists('wakaf_non_cashes', 'id')->where('mosque_id', $mosqueId),
            ],
            'nazhir_id' => [
                'required',
                Rule::exists('nazhirs', 'id')->where('mosque_id', $mosqueId),
            ],
            'jenis_aset' => 'nullable|string',
            'nama_aset' => 'required|string',
            'lokasi' => 'nullable|string',
            'nilai_estimasi' => 'nullable|numeric|min:0',
            'kondisi' => 'nullable|string',
            'status_hukum' => 'nullable|string',
            'status_pemanfaatan' => 'nullable|string',
            'produktif' => 'nullable|boolean',
            'keterangan' => 'nullable|string',
        ]);

        if ($data['sumber_wakaf'] !== 'wakaf_tunai') {
            $data['wakaf_tunai_id'] = null;
        }

        if ($data['sumber_wakaf'] !== 'wakaf_non_tunai') {
            $data['wakaf_non_tunai_id'] = null;
        }

        $data['produktif'] = $request->boolean('produktif');

        return $data;
    }

    private function formOptions(int $mosqueId): array
    {
        return [
            Nazhir::where('mosque_id', $mosqueId)->orderBy('nama')->get(),
            WakafCash::with('wakif')
                ->where('mosque_id', $mosqueId)
                ->orderByDesc('tanggal_terima')
                ->get(),
            WakafNonCash::with('wakif')
                ->where('mosque_id', $mosqueId)
                ->orderByDesc('tanggal_terima')
                ->get(),
        ];
    }

    private function activeMosqueId(): ?int
    {
        return session('active_mosque_id') ?: auth()->user()?->active_mosque_id;
    }

    private function authorizeAsset(WakafAsset $asset): void
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId || (int) $asset->mosque_id !== (int) $mosqueId) {
            abort(404);
        }
    }
}
