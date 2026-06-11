<?php

namespace App\Http\Controllers;

use App\Models\CashAccount;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\WakafManagementResult;
use App\Models\WakafProductiveAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class WakafManagementResultController extends Controller
{
    public function index()
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId) {
            return redirect()->route('wakaf.index')->with('error', 'Pilih masjid aktif terlebih dahulu.');
        }

        $results = WakafManagementResult::with(['productiveAsset.wakafAsset', 'cashAccount'])
            ->where('mosque_id', $mosqueId)
            ->latest()
            ->paginate(10);

        return view('admin.wakaf.management-results.index', compact('results'));
    }

    public function create()
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId) {
            return redirect()->route('wakaf.index')->with('error', 'Pilih masjid aktif terlebih dahulu.');
        }

        $productiveAssets = $this->productiveAssetOptions($mosqueId);
        $cashAccounts = $this->cashAccountOptions($mosqueId);

        return view('admin.wakaf.management-results.create', compact('productiveAssets', 'cashAccounts'));
    }

    public function store(Request $request)
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId) {
            return redirect()->route('wakaf.index')->with('error', 'Pilih masjid aktif terlebih dahulu.');
        }

        $data = $request->validate($this->rules($mosqueId));
        $data['mosque_id'] = $mosqueId;
        $this->normalizeCashAccount($data);

        if ($request->hasFile('bukti_file')) {
            $data['bukti_file'] = $request->file('bukti_file')->store('wakaf/management-results', 'public');
        }

        DB::transaction(function () use ($data): void {
            $result = WakafManagementResult::create($data);
            $this->syncCashTransaction($result);
        });

        return redirect()->route('wakaf.management-results.index')->with('success', 'Hasil Kelola Wakaf berhasil disimpan.');
    }

    public function show(WakafManagementResult $management_result)
    {
        $this->authorizeResult($management_result);
        $management_result->load(['productiveAsset.wakafAsset', 'cashAccount', 'cashTransaction']);

        return view('admin.wakaf.management-results.show', ['result' => $management_result]);
    }

    public function edit(WakafManagementResult $management_result)
    {
        $this->authorizeResult($management_result);

        $mosqueId = $this->activeMosqueId();
        $productiveAssets = $this->productiveAssetOptions($mosqueId, $management_result->productive_waqf_asset_id);
        $cashAccounts = $this->cashAccountOptions($mosqueId);

        return view('admin.wakaf.management-results.edit', [
            'result' => $management_result,
            'productiveAssets' => $productiveAssets,
            'cashAccounts' => $cashAccounts,
        ]);
    }

    public function update(Request $request, WakafManagementResult $management_result)
    {
        $this->authorizeResult($management_result);

        $mosqueId = $this->activeMosqueId();
        $data = $request->validate($this->rules($mosqueId));
        $this->normalizeCashAccount($data);

        if ($request->hasFile('bukti_file')) {
            if ($management_result->bukti_file && Storage::disk('public')->exists($management_result->bukti_file)) {
                Storage::disk('public')->delete($management_result->bukti_file);
            }

            $data['bukti_file'] = $request->file('bukti_file')->store('wakaf/management-results', 'public');
        }

        DB::transaction(function () use ($management_result, $data): void {
            $management_result->update($data);
            $this->syncCashTransaction($management_result->refresh());
        });

        return redirect()->route('wakaf.management-results.index')->with('success', 'Hasil Kelola Wakaf berhasil diperbarui.');
    }

    public function destroy(WakafManagementResult $management_result)
    {
        $this->authorizeResult($management_result);

        if ($management_result->bukti_file && Storage::disk('public')->exists($management_result->bukti_file)) {
            Storage::disk('public')->delete($management_result->bukti_file);
        }

        DB::transaction(function () use ($management_result): void {
            $this->deleteCashTransaction($management_result);
            $management_result->delete();
        });

        return redirect()->route('wakaf.management-results.index')->with('success', 'Hasil Kelola Wakaf berhasil dihapus.');
    }

    private function rules(int $mosqueId): array
    {
        return [
            'productive_waqf_asset_id' => [
                'required',
                Rule::exists('wakaf_productive_assets', 'id')->where('mosque_id', $mosqueId),
            ],
            'tanggal_penerimaan' => 'required|date',
            'jenis_hasil' => 'nullable|string',
            'nominal' => 'required|numeric|min:0',
            'periode' => 'nullable|string',
            'nama_pembayar' => 'nullable|string',
            'bukti_file' => 'nullable|file|max:2048',
            'masuk_ke_kas_masjid' => ['required', Rule::in(['Ya', 'Tidak'])],
            'cash_account_id' => [
                'nullable',
                'required_if:masuk_ke_kas_masjid,Ya',
                Rule::exists('cash_accounts', 'id')
                    ->where('mosque_id', $mosqueId)
                    ->where('is_active', true),
            ],
            'keterangan' => 'nullable|string',
        ];
    }

    private function normalizeCashAccount(array &$data): void
    {
        if (($data['masuk_ke_kas_masjid'] ?? null) !== 'Ya') {
            $data['cash_account_id'] = null;
        }
    }

    private function syncCashTransaction(WakafManagementResult $result): void
    {
        if ($result->masuk_ke_kas_masjid !== 'Ya' || ! $result->cash_account_id) {
            $this->deleteCashTransaction($result);
            $result->forceFill(['mosque_cash_transaction_id' => null])->save();

            return;
        }

        $result->loadMissing(['productiveAsset.wakafAsset', 'cashAccount']);
        $category = $this->managementResultCategory((int) $result->mosque_id);
        $assetName = $result->productiveAsset?->wakafAsset?->nama_aset ?: 'aset produktif';

        $transaction = Transaction::withoutGlobalScopes()->updateOrCreate(
            [
                'source_type' => Transaction::SOURCE_WAKAF_MANAGEMENT_RESULT,
                'source_id' => $result->id,
            ],
            [
                'mosque_id' => $result->mosque_id,
                'transaction_date' => $result->tanggal_penerimaan,
                'type' => TransactionCategory::TYPE_MASUK,
                'transaction_category_id' => $category->id,
                'cash_account_id' => $result->cash_account_id,
                'amount' => $result->nominal,
                'description' => 'Hasil Kelola Wakaf dari '.$assetName,
                'payment_method' => $result->cashAccount?->paymentMethodValue() ?? 'lainnya',
                'proof_file' => $result->bukti_file,
                'created_by' => auth()->user()?->name ?? 'Admin',
            ]
        );

        if ((int) $result->mosque_cash_transaction_id !== (int) $transaction->id) {
            $result->forceFill(['mosque_cash_transaction_id' => $transaction->id])->save();
        }
    }

    private function deleteCashTransaction(WakafManagementResult $result): void
    {
        Transaction::withoutGlobalScopes()
            ->where('source_type', Transaction::SOURCE_WAKAF_MANAGEMENT_RESULT)
            ->where('source_id', $result->id)
            ->delete();
    }

    private function managementResultCategory(int $mosqueId): TransactionCategory
    {
        return TransactionCategory::withoutGlobalScopes()->updateOrCreate(
            [
                'mosque_id' => $mosqueId,
                'name' => 'Hasil Kelola Wakaf',
            ],
            [
                'type' => TransactionCategory::TYPE_MASUK,
                'is_active' => true,
                'description' => 'Penerimaan hasil kelola wakaf produktif',
            ]
        );
    }

    private function productiveAssetOptions(int $mosqueId, ?int $includeId = null)
    {
        return WakafProductiveAsset::with('wakafAsset')
            ->where('mosque_id', $mosqueId)
            ->orderByDesc('created_at')
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

    private function authorizeResult(WakafManagementResult $result): void
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId || (int) $result->mosque_id !== (int) $mosqueId) {
            abort(404);
        }
    }

}
