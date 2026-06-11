<?php

namespace App\Http\Controllers;

use App\Models\WakafAsset;
use App\Models\WakafProductiveAsset;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WakafProductiveAssetController extends Controller
{
    public function index()
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId) {
            return redirect()->route('wakaf.index')->with('error', 'Pilih masjid aktif terlebih dahulu.');
        }

        $productiveAssets = WakafProductiveAsset::with('wakafAsset')
            ->where('mosque_id', $mosqueId)
            ->latest()
            ->paginate(10);

        return view('admin.wakaf.productive-assets.index', compact('productiveAssets'));
    }

    public function create()
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId) {
            return redirect()->route('wakaf.index')->with('error', 'Pilih masjid aktif terlebih dahulu.');
        }

        $assets = $this->assetOptions($mosqueId);

        return view('admin.wakaf.productive-assets.create', compact('assets'));
    }

    public function store(Request $request)
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId) {
            return redirect()->route('wakaf.index')->with('error', 'Pilih masjid aktif terlebih dahulu.');
        }

        $data = $request->validate($this->rules($mosqueId));
        $data['mosque_id'] = $mosqueId;

        WakafProductiveAsset::create($data);

        return redirect()->route('wakaf.productive-assets.index')->with('success', 'Aset Produktif Wakaf berhasil disimpan.');
    }

    public function show(WakafProductiveAsset $productive_asset)
    {
        $this->authorizeProductiveAsset($productive_asset);
        $productive_asset->load('wakafAsset.nazhir');

        return view('admin.wakaf.productive-assets.show', ['productiveAsset' => $productive_asset]);
    }

    public function edit(WakafProductiveAsset $productive_asset)
    {
        $this->authorizeProductiveAsset($productive_asset);

        $mosqueId = $this->activeMosqueId();
        $assets = $this->assetOptions($mosqueId, $productive_asset->waqf_asset_id);

        return view('admin.wakaf.productive-assets.edit', [
            'productiveAsset' => $productive_asset,
            'assets' => $assets,
        ]);
    }

    public function update(Request $request, WakafProductiveAsset $productive_asset)
    {
        $this->authorizeProductiveAsset($productive_asset);

        $mosqueId = $this->activeMosqueId();
        $data = $request->validate($this->rules($mosqueId));

        $productive_asset->update($data);

        return redirect()->route('wakaf.productive-assets.index')->with('success', 'Aset Produktif Wakaf berhasil diperbarui.');
    }

    public function destroy(WakafProductiveAsset $productive_asset)
    {
        $this->authorizeProductiveAsset($productive_asset);

        if ($productive_asset->managementResults()->exists()) {
            return redirect()
                ->route('wakaf.productive-assets.index')
                ->with('error', 'Aset Produktif Wakaf tidak dapat dihapus karena sudah memiliki data hasil kelola.');
        }

        $productive_asset->delete();

        return redirect()->route('wakaf.productive-assets.index')->with('success', 'Aset Produktif Wakaf berhasil dihapus.');
    }

    private function rules(int $mosqueId): array
    {
        return [
            'waqf_asset_id' => [
                'required',
                Rule::exists('wakaf_assets', 'id')->where('mosque_id', $mosqueId),
            ],
            'jenis_pengelolaan' => 'nullable|string',
            'nama_penyewa_atau_mitra' => 'nullable|string',
            'tanggal_mulai_kontrak' => 'nullable|date',
            'tanggal_selesai_kontrak' => 'nullable|date|after_or_equal:tanggal_mulai_kontrak',
            'target_pendapatan' => 'nullable|numeric|min:0',
            'periode_pendapatan' => 'nullable|string',
            'status' => ['nullable', Rule::in(['aktif', 'nonaktif', 'selesai'])],
            'keterangan' => 'nullable|string',
        ];
    }

    private function assetOptions(int $mosqueId, ?int $includeId = null)
    {
        return WakafAsset::where('mosque_id', $mosqueId)
            ->where(function ($query) use ($includeId) {
                $query->where('produktif', true)
                    ->when($includeId, fn ($q) => $q->orWhere('id', $includeId));
            })
            ->orderBy('nama_aset')
            ->get();
    }

    private function activeMosqueId(): ?int
    {
        return session('active_mosque_id') ?: auth()->user()?->active_mosque_id;
    }

    private function authorizeProductiveAsset(WakafProductiveAsset $productiveAsset): void
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId || (int) $productiveAsset->mosque_id !== (int) $mosqueId) {
            abort(404);
        }
    }
}
