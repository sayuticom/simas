<?php

namespace App\Http\Controllers;

use App\Models\CashAccount;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\WakafAsset;
use App\Models\WakafAssetMaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class WakafAssetMaintenanceController extends Controller
{
    public function index()
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId) {
            return redirect()->route('wakaf.index')->with('error', 'Pilih masjid aktif terlebih dahulu.');
        }

        $maintenances = WakafAssetMaintenance::with(['wakafAsset', 'cashAccount'])
            ->where('mosque_id', $mosqueId)
            ->latest()
            ->paginate(10);

        return view('admin.wakaf.asset-maintenances.index', compact('maintenances'));
    }

    public function create()
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId) {
            return redirect()->route('wakaf.index')->with('error', 'Pilih masjid aktif terlebih dahulu.');
        }

        $assets = $this->assetOptions($mosqueId);
        $cashAccounts = $this->cashAccountOptions($mosqueId);

        return view('admin.wakaf.asset-maintenances.create', compact('assets', 'cashAccounts'));
    }

    public function store(Request $request)
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId) {
            return redirect()->route('wakaf.index')->with('error', 'Pilih masjid aktif terlebih dahulu.');
        }

        $data = $request->validate($this->rules($mosqueId));
        $data['mosque_id'] = $mosqueId;

        if ($request->hasFile('bukti_file')) {
            $data['bukti_file'] = $request->file('bukti_file')->store('wakaf/asset-maintenances', 'public');
        }

        DB::transaction(function () use ($data): void {
            $maintenance = WakafAssetMaintenance::create($data);
            $this->syncCashTransaction($maintenance);
        });

        return redirect()->route('wakaf.asset-maintenances.index')->with('success', 'Perawatan Aset Wakaf berhasil disimpan.');
    }

    public function show(WakafAssetMaintenance $asset_maintenance)
    {
        $this->authorizeMaintenance($asset_maintenance);
        $asset_maintenance->load(['wakafAsset', 'cashAccount', 'cashTransaction']);

        return view('admin.wakaf.asset-maintenances.show', ['maintenance' => $asset_maintenance]);
    }

    public function edit(WakafAssetMaintenance $asset_maintenance)
    {
        $this->authorizeMaintenance($asset_maintenance);

        $mosqueId = $this->activeMosqueId();
        $assets = $this->assetOptions($mosqueId);
        $cashAccounts = $this->cashAccountOptions($mosqueId);

        return view('admin.wakaf.asset-maintenances.edit', [
            'maintenance' => $asset_maintenance,
            'assets' => $assets,
            'cashAccounts' => $cashAccounts,
        ]);
    }

    public function update(Request $request, WakafAssetMaintenance $asset_maintenance)
    {
        $this->authorizeMaintenance($asset_maintenance);

        $mosqueId = $this->activeMosqueId();
        $data = $request->validate($this->rules($mosqueId));

        if ($request->hasFile('bukti_file')) {
            if ($asset_maintenance->bukti_file && Storage::disk('public')->exists($asset_maintenance->bukti_file)) {
                Storage::disk('public')->delete($asset_maintenance->bukti_file);
            }

            $data['bukti_file'] = $request->file('bukti_file')->store('wakaf/asset-maintenances', 'public');
        }

        DB::transaction(function () use ($asset_maintenance, $data): void {
            $asset_maintenance->update($data);
            $this->syncCashTransaction($asset_maintenance->refresh());
        });

        return redirect()->route('wakaf.asset-maintenances.index')->with('success', 'Perawatan Aset Wakaf berhasil diperbarui.');
    }

    public function destroy(WakafAssetMaintenance $asset_maintenance)
    {
        $this->authorizeMaintenance($asset_maintenance);

        if ($asset_maintenance->bukti_file && Storage::disk('public')->exists($asset_maintenance->bukti_file)) {
            Storage::disk('public')->delete($asset_maintenance->bukti_file);
        }

        DB::transaction(function () use ($asset_maintenance): void {
            $this->deleteCashTransaction($asset_maintenance);
            $asset_maintenance->delete();
        });

        return redirect()->route('wakaf.asset-maintenances.index')->with('success', 'Perawatan Aset Wakaf berhasil dihapus.');
    }

    private function rules(int $mosqueId): array
    {
        return [
            'waqf_asset_id' => [
                'required',
                Rule::exists('wakaf_assets', 'id')->where('mosque_id', $mosqueId),
            ],
            'tanggal_pengeluaran' => 'required|date',
            'jenis_biaya' => 'nullable|string',
            'nominal' => 'required|numeric|min:0',
            'dibayar_dari' => 'nullable|string',
            'cash_account_id' => [
                'required',
                Rule::exists('cash_accounts', 'id')
                    ->where('mosque_id', $mosqueId)
                    ->where('is_active', true),
            ],
            'bukti_file' => 'nullable|file|max:2048',
            'penanggung_jawab' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ];
    }

    private function syncCashTransaction(WakafAssetMaintenance $maintenance): void
    {
        $maintenance->loadMissing(['wakafAsset', 'cashAccount']);
        $category = $this->maintenanceCategory((int) $maintenance->mosque_id);
        $assetName = $maintenance->wakafAsset?->nama_aset ?: 'aset wakaf';

        $transaction = Transaction::withoutGlobalScopes()->updateOrCreate(
            [
                'source_type' => Transaction::SOURCE_WAKAF_ASSET_MAINTENANCE,
                'source_id' => $maintenance->id,
            ],
            [
                'mosque_id' => $maintenance->mosque_id,
                'transaction_date' => $maintenance->tanggal_pengeluaran,
                'type' => TransactionCategory::TYPE_KELUAR,
                'transaction_category_id' => $category->id,
                'cash_account_id' => $maintenance->cash_account_id,
                'amount' => $maintenance->nominal,
                'description' => 'Perawatan Aset Wakaf: '.$assetName,
                'payment_method' => $maintenance->cashAccount?->paymentMethodValue() ?? 'lainnya',
                'proof_file' => $maintenance->bukti_file,
                'created_by' => auth()->user()?->name ?? 'Admin',
            ]
        );

        if ((int) $maintenance->mosque_cash_transaction_id !== (int) $transaction->id) {
            $maintenance->forceFill(['mosque_cash_transaction_id' => $transaction->id])->save();
        }
    }

    private function deleteCashTransaction(WakafAssetMaintenance $maintenance): void
    {
        Transaction::withoutGlobalScopes()
            ->where('source_type', Transaction::SOURCE_WAKAF_ASSET_MAINTENANCE)
            ->where('source_id', $maintenance->id)
            ->delete();
    }

    private function maintenanceCategory(int $mosqueId): TransactionCategory
    {
        return TransactionCategory::withoutGlobalScopes()->updateOrCreate(
            [
                'mosque_id' => $mosqueId,
                'name' => 'Perawatan Aset Wakaf',
            ],
            [
                'type' => TransactionCategory::TYPE_KELUAR,
                'is_active' => true,
                'description' => 'Pengeluaran perawatan aset wakaf',
            ]
        );
    }

    private function assetOptions(int $mosqueId)
    {
        return WakafAsset::where('mosque_id', $mosqueId)
            ->orderBy('nama_aset')
            ->get();
    }

    private function cashAccountOptions(int $mosqueId)
    {
        return CashAccount::where('mosque_id', $mosqueId)
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();
    }

    private function activeMosqueId(): ?int
    {
        return session('active_mosque_id') ?: auth()->user()?->active_mosque_id;
    }

    private function authorizeMaintenance(WakafAssetMaintenance $maintenance): void
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId || (int) $maintenance->mosque_id !== (int) $mosqueId) {
            abort(404);
        }
    }
}
