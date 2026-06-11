<?php

namespace App\Http\Controllers;

use App\Models\Mustahik;
use App\Models\CashAccount;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\ZisCategory;
use App\Models\ZisDistribution;
use App\Models\ZisProgram;
use App\Models\ZisReceipt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ZisDistributionController extends Controller
{
    public const RECIPIENT_TYPES = [
        'fakir' => 'Fakir',
        'miskin' => 'Miskin',
        'amil' => 'Amil',
        'mualaf' => 'Mualaf',
        'riqab' => 'Riqab',
        'gharim' => 'Gharimin',
        'fisabilillah' => 'Fi Sabilillah',
        'ibnu_sabil' => 'Ibnu Sabil',
        'lainnya' => 'Lainnya',
    ];

    public const DISTRIBUTION_TARGETS = [
        'penerima_manfaat' => 'Penerima Manfaat / Mustahik',
        'kas_operasional' => 'Kas Operasional Masjid',
    ];

    public const ASNAF_TYPES = [
        'fakir',
        'miskin',
        'amil',
        'mualaf',
        'riqab',
        'gharim',
        'fisabilillah',
        'ibnu_sabil',
    ];

    private const OPERATIONAL_TRANSFER_CATEGORY = 'Transfer dari ZIS';
//sayuti
    public function index(): View
    {
        $distributions = ZisDistribution::with(['cashAccount', 'category', 'receipt'])
            ->latest('distribution_date')
            ->paginate(10);

        return view('admin.zis.distributions.index', compact('distributions'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        $sourceReceipt = $this->sourceReceiptFromRequest($request);

        if (! $sourceReceipt) {
            return redirect()
                ->route('zis.receipts.index')
                ->with('error', 'Pilih penerimaan ZIS yang masih punya sisa dana sebelum membuat penyaluran.');
        }

        $categories = $this->activeCategories($sourceReceipt?->zis_category_id);
        $cashAccounts = $this->activeCashAccounts($sourceReceipt?->zis_category_id, $sourceReceipt?->cash_account_id);
        $recipientTypes = self::RECIPIENT_TYPES;
        $distributionTargets = self::DISTRIBUTION_TARGETS;

        return view('admin.zis.distributions.create', compact('cashAccounts', 'categories', 'distributionTargets', 'recipientTypes', 'sourceReceipt'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $sourceReceipt = $this->sourceReceiptFromData($data);
        $category = $sourceReceipt?->category ?? $this->category($data['zis_category_id'] ?? null);
        abort_if($category && ! $category->is_active, 422, 'Kategori ZIS nonaktif tidak bisa dipilih.');
        $this->ensureActiveCashAccount((int) $data['cash_account_id']);
        $this->validateSourceReceipt($data, $sourceReceipt);
        $this->validateFundUsage($data, $category);
        $this->validateRemainingAmount($data, $sourceReceipt);
        $this->validateCategoryBalance($data, $category, $sourceReceipt);
        $this->validateCashAccountCategoryBalance($data, $category);

        if ($request->hasFile('proof_file')) {
            $data['proof_file'] = $request->file('proof_file')->store('zis/distributions', 'public');
        }

        DB::transaction(function () use ($data, $category) {
            $distribution = ZisDistribution::create($this->payload($data, $category));
            $this->syncOperationalTransaction($distribution);
        });

        return redirect()->route('zis.distributions.index')->with('success', 'Penyaluran ZIS berhasil disimpan.');
    }

    public function show(ZisDistribution $distribution): View
    {
        $this->ensureOwnDistribution($distribution);
        $distribution->load(['cashAccount', 'category', 'receipt.category', 'operationalTransaction.cashAccount', 'operationalTransaction.category']);

        return view('admin.zis.distributions.show', compact('distribution'));
    }

    public function edit(ZisDistribution $distribution): View
    {
        $this->ensureOwnDistribution($distribution);
        $distribution->load('receipt');
        $categories = $this->activeCategories($distribution->zis_category_id);
        $cashAccounts = $this->activeCashAccounts($distribution->zis_category_id, $distribution->cash_account_id);
        $recipientTypes = self::RECIPIENT_TYPES;
        $distributionTargets = self::DISTRIBUTION_TARGETS;
        $sourceReceipt = $distribution->receipt;

        return view('admin.zis.distributions.edit', compact('cashAccounts', 'distribution', 'categories', 'distributionTargets', 'recipientTypes', 'sourceReceipt'));
    }

    public function update(Request $request, ZisDistribution $distribution)
    {
        $this->ensureOwnDistribution($distribution);
        $data = $this->validatedData($request);
        $sourceReceipt = $this->sourceReceiptFromData($data);
        $category = $sourceReceipt?->category ?? $this->category($data['zis_category_id'] ?? null);
        abort_if($category && ! $category->is_active && (int) $category->id !== (int) $distribution->zis_category_id, 422, 'Kategori ZIS nonaktif tidak bisa dipilih.');
        $this->ensureActiveCashAccount((int) $data['cash_account_id'], $distribution->cash_account_id);
        $this->validateSourceReceipt($data, $sourceReceipt);
        $this->validateFundUsage($data, $category);
        $this->validateRemainingAmount($data, $sourceReceipt, $distribution);
        $this->validateCategoryBalance($data, $category, $sourceReceipt, $distribution);
        $this->validateCashAccountCategoryBalance($data, $category, $distribution);
        $this->validateOperationalTransferChange($distribution, $data);

        if ($request->hasFile('proof_file')) {
            if ($distribution->proof_file) {
                Storage::disk('public')->delete($distribution->proof_file);
            }

            $data['proof_file'] = $request->file('proof_file')->store('zis/distributions', 'public');
        }

        DB::transaction(function () use ($data, $category, $distribution) {
            $distribution->update($this->payload($data, $category, $distribution));
            $this->syncOperationalTransaction($distribution->refresh());
        });

        return redirect()->route('zis.distributions.index')->with('success', 'Penyaluran ZIS berhasil diperbarui.');
    }

    public function destroy(ZisDistribution $distribution)
    {
        $this->ensureOwnDistribution($distribution);

        try {
            $this->validateOperationalTransferDeletion($distribution);
        } catch (ValidationException $exception) {
            return redirect()
                ->route('zis.distributions.index')
                ->with('error', collect($exception->errors())->flatten()->first());
        }

        DB::transaction(function () use ($distribution) {
            $this->deleteOperationalTransaction($distribution);

            if ($distribution->proof_file) {
                Storage::disk('public')->delete($distribution->proof_file);
            }

            $distribution->delete();
        });

        return redirect()->route('zis.distributions.index')->with('success', 'Penyaluran ZIS berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'zis_category_id' => [
                'required',
                Rule::exists('zis_categories', 'id')
                    ->where('mosque_id', $this->activeMosqueId()),
            ],
            'zis_receipt_id' => [
                'nullable',
                Rule::exists('zis_receipts', 'id')
                    ->where('mosque_id', $this->activeMosqueId()),
            ],
            'cash_account_id' => [
                'required',
                Rule::exists('cash_accounts', 'id')
                    ->where('mosque_id', $this->activeMosqueId()),
            ],
            'distribution_target' => ['required', Rule::in(array_keys(self::DISTRIBUTION_TARGETS))],
            'distribution_date' => 'required|date',
            'recipient_name' => 'nullable|string|max:255',
            'recipient_phone' => 'nullable|string|max:50',
            'recipient_address' => 'nullable|string',
            'recipient_type' => ['nullable', Rule::in(array_keys(self::RECIPIENT_TYPES))],
            'amount' => 'required|numeric|gt:0',
            'description' => 'nullable|string',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);
    }

    private function payload(array $data, ?ZisCategory $category, ?ZisDistribution $distribution = null): array
    {
        $distributionTarget = $data['distribution_target'];
        $recipientType = $distributionTarget === 'kas_operasional' ? 'kas_operasional' : ($data['recipient_type'] ?? 'lainnya');
        $recipientName = $distributionTarget === 'kas_operasional'
            ? 'Kas Operasional Masjid'
            : $data['recipient_name'];

        return [
            'mosque_id' => $this->activeMosqueId(),
            'zis_receipt_id' => $data['zis_receipt_id'] ?? null,
            'zis_category_id' => $category?->id,
            'cash_account_id' => $data['cash_account_id'],
            'distribution_date' => $data['distribution_date'],
            'recipient_name' => $recipientName,
            'recipient_phone' => $data['recipient_phone'] ?? null,
            'recipient_address' => $data['recipient_address'] ?? null,
            'recipient_type' => $recipientType,
            'distribution_target' => $distributionTarget,
            'amount' => $data['amount'],
            'description' => $data['description'] ?? null,
            'proof_file' => $data['proof_file'] ?? $distribution?->proof_file,
            'created_by' => $distribution?->created_by ?? auth()->user()?->name,
            'mustahik_id' => $this->ensureMustahik($data, $recipientType, $recipientName),
            'zis_program_id' => $this->ensureProgram($category?->name ?? 'Penyaluran ZIS'),
            'tanggal' => $data['distribution_date'],
            'sumber_dana' => $category?->name ?? 'ZIS',
            'kategori_asnaf' => $this->legacyAsnaf($recipientType),
            'jenis_bantuan' => $category?->type ? ucfirst($category->type) : 'ZIS',
            'nominal' => $data['amount'],
            'disalurkan_oleh' => $distribution?->disalurkan_oleh ?? auth()->user()?->name,
            'bukti_serah_terima' => $data['proof_file'] ?? $distribution?->bukti_serah_terima,
            'keterangan' => $data['description'] ?? null,
        ];
    }

    private function activeCategories(?int $includeId = null)
    {
        ZisCategory::ensureDefaultsForMosque($this->activeMosqueId());

        return ZisCategory::withSum([
            'receipts as received_amount' => fn ($query) => $query->where('mosque_id', $this->activeMosqueId()),
        ], 'amount')
            ->withSum([
                'distributions as distributed_amount' => fn ($query) => $query->where('mosque_id', $this->activeMosqueId()),
            ], 'amount')
            ->where('mosque_id', $this->activeMosqueId())
            ->where(function ($query) use ($includeId) {
                $query->where('is_active', true)
                    ->when($includeId, fn ($q) => $q->orWhere('id', $includeId));
            })
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->map(function (ZisCategory $category) {
                $category->available_balance = max(
                    (float) ($category->received_amount ?? 0) - (float) ($category->distributed_amount ?? 0),
                    0
                );

                return $category;
            })
            ->filter(fn (ZisCategory $category) => $category->available_balance > 0 || (int) $category->id === (int) $includeId)
            ->values();
    }

    private function activeCashAccounts(?int $categoryId = null, ?int $includeId = null)
    {
        CashAccount::ensureDefaultsForMosque($this->activeMosqueId());

        return CashAccount::where('mosque_id', $this->activeMosqueId())
            ->where(function ($query) use ($includeId) {
                $query->where('is_active', true)
                    ->when($includeId, fn ($q) => $q->orWhere('id', $includeId));
            })
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->map(function (CashAccount $account) use ($categoryId) {
                if (! $categoryId) {
                    $account->available_balance = null;

                    return $account;
                }

                $received = (float) ZisReceipt::where('mosque_id', $this->activeMosqueId())
                    ->where('cash_account_id', $account->id)
                    ->where('zis_category_id', $categoryId)
                    ->sum('amount');
                $distributed = (float) ZisDistribution::where('mosque_id', $this->activeMosqueId())
                    ->where('cash_account_id', $account->id)
                    ->where('zis_category_id', $categoryId)
                    ->sum('amount');

                $account->available_balance = max($received - $distributed, 0);

                return $account;
            });
    }

    private function category(?int $id): ?ZisCategory
    {
        if (! $id) {
            return null;
        }

        return ZisCategory::where('mosque_id', $this->activeMosqueId())->findOrFail($id);
    }

    private function ensureActiveCashAccount(int $id, ?int $includeId = null): void
    {
        $account = CashAccount::where('mosque_id', $this->activeMosqueId())->findOrFail($id);

        if (! $account->is_active && (int) $account->id !== (int) $includeId) {
            throw ValidationException::withMessages([
                'cash_account_id' => 'Akun kas nonaktif tidak bisa dipilih.',
            ]);
        }

        if (! $account->can_distribute_zis && (int) $account->id !== (int) $includeId) {
            throw ValidationException::withMessages([
                'cash_account_id' => 'Akun kas ini tidak diizinkan untuk Penyaluran ZIS.',
            ]);
        }
    }

    private function sourceReceiptFromRequest(Request $request): ?ZisReceipt
    {
        $receiptId = $request->integer('receipt_id');

        if (! $receiptId) {
            return null;
        }

        return $this->sourceReceipt($receiptId);
    }

    private function sourceReceiptFromData(array $data): ?ZisReceipt
    {
        if (empty($data['zis_receipt_id'])) {
            return null;
        }

        return $this->sourceReceipt((int) $data['zis_receipt_id']);
    }

    private function sourceReceipt(int $receiptId): ZisReceipt
    {
        return ZisReceipt::with('category')
            ->where('mosque_id', $this->activeMosqueId())
            ->findOrFail($receiptId);
    }

    private function ensureMustahik(array $data, string $recipientType, string $recipientName): int
    {
        return Mustahik::firstOrCreate(
            [
                'mosque_id' => $this->activeMosqueId(),
                'nama' => $recipientName,
                'no_hp' => $data['recipient_phone'] ?? null,
            ],
            [
                'alamat' => $data['recipient_address'] ?? null,
                'kategori_asnaf' => $this->legacyAsnaf($recipientType),
                'status_verifikasi' => true,
            ]
        )->id;
    }

    private function ensureProgram(string $name): int
    {
        return ZisProgram::firstOrCreate(
            [
                'mosque_id' => $this->activeMosqueId(),
                'nama' => $name,
            ],
            [
                'target_dana' => 0,
                'status' => 'Berjalan',
            ]
        )->id;
    }

    private function legacyAsnaf(string $recipientType): string
    {
        $label = self::RECIPIENT_TYPES[$recipientType] ?? 'Fakir';
        $allowed = ['Fakir', 'Miskin', 'Amil', 'Mualaf', 'Riqab', 'Gharimin', 'Fi Sabilillah', 'Ibnu Sabil'];

        return in_array($label, $allowed, true) ? $label : 'Fakir';
    }

    private function ensureOwnDistribution(ZisDistribution $distribution): void
    {
        abort_unless((int) $distribution->mosque_id === $this->activeMosqueId(), 404);
    }

    private function activeMosqueId(): int
    {
        return (int) session('active_mosque_id');
    }

    private function syncOperationalTransaction(ZisDistribution $distribution): void
    {
        if ($distribution->distribution_target !== 'kas_operasional') {
            $this->deleteOperationalTransaction($distribution);

            return;
        }

        $category = $this->operationalTransferCategory((int) $distribution->mosque_id);

        Transaction::withoutGlobalScopes()->updateOrCreate(
            [
                'source_type' => Transaction::SOURCE_ZIS_DISTRIBUTION,
                'source_id' => $distribution->id,
            ],
            [
                'mosque_id' => $distribution->mosque_id,
                'transaction_category_id' => $category->id,
                'cash_account_id' => $distribution->cash_account_id,
                'transaction_date' => $distribution->distribution_date,
                'type' => TransactionCategory::TYPE_MASUK,
                'amount' => $distribution->amount,
                'description' => 'Transfer dari ZIS ke Kas Operasional Masjid',
                'payment_method' => $distribution->cashAccount?->paymentMethodValue() ?? 'transfer',
                'proof_file' => $distribution->proof_file,
                'created_by' => auth()->user()?->name ?? $distribution->created_by,
            ]
        );
    }

    private function deleteOperationalTransaction(ZisDistribution $distribution): void
    {
        Transaction::withoutGlobalScopes()
            ->where('source_type', Transaction::SOURCE_ZIS_DISTRIBUTION)
            ->where('source_id', $distribution->id)
            ->delete();
    }

    private function operationalTransaction(ZisDistribution $distribution): ?Transaction
    {
        return Transaction::withoutGlobalScopes()
            ->where('source_type', Transaction::SOURCE_ZIS_DISTRIBUTION)
            ->where('source_id', $distribution->id)
            ->where('mosque_id', $distribution->mosque_id)
            ->first();
    }

    private function operationalBalance(int $mosqueId): float
    {
        $totalMasuk = (float) Transaction::withoutGlobalScopes()
            ->where('mosque_id', $mosqueId)
            ->where('type', TransactionCategory::TYPE_MASUK)
            ->sum('amount');

        $totalKeluar = (float) Transaction::withoutGlobalScopes()
            ->where('mosque_id', $mosqueId)
            ->where('type', TransactionCategory::TYPE_KELUAR)
            ->sum('amount');

        return $totalMasuk - $totalKeluar;
    }

    private function validateOperationalTransferChange(ZisDistribution $distribution, array $data): void
    {
        if ($distribution->distribution_target !== 'kas_operasional') {
            return;
        }

        $transaction = $this->operationalTransaction($distribution);

        if (! $transaction) {
            return;
        }

        $oldAmount = (float) $transaction->amount;
        $newAmount = $data['distribution_target'] === 'kas_operasional' ? (float) $data['amount'] : 0;
        $reduction = $oldAmount - $newAmount;

        if ($reduction <= 0) {
            return;
        }

        $operationalBalance = $this->operationalBalance((int) $distribution->mosque_id);

        if ($operationalBalance < $reduction) {
            throw ValidationException::withMessages([
                'amount' => 'Nominal tidak bisa dikurangi karena sebagian dana sudah digunakan untuk pengeluaran operasional.',
            ]);
        }
    }

    private function validateOperationalTransferDeletion(ZisDistribution $distribution): void
    {
        if ($distribution->distribution_target !== 'kas_operasional') {
            return;
        }

        $transaction = $this->operationalTransaction($distribution);

        if (! $transaction) {
            return;
        }

        if ($this->operationalBalance((int) $distribution->mosque_id) < (float) $transaction->amount) {
            throw ValidationException::withMessages([
                'distribution' => 'Penyaluran ZIS ini tidak bisa dihapus karena dana operasionalnya sudah digunakan untuk pengeluaran.',
            ]);
        }
    }

    private function operationalTransferCategory(int $mosqueId): TransactionCategory
    {
        return TransactionCategory::withoutGlobalScopes()->updateOrCreate(
            [
                'mosque_id' => $mosqueId,
                'name' => self::OPERATIONAL_TRANSFER_CATEGORY,
            ],
            [
                'type' => TransactionCategory::TYPE_MASUK,
                'is_active' => true,
                'description' => 'Kategori internal untuk transfer dana ZIS ke kas operasional masjid.',
            ]
        );
    }

    private function validateFundUsage(array $data, ?ZisCategory $category): void
    {
        if (! $category) {
            throw ValidationException::withMessages([
                'zis_category_id' => 'Kategori dana wajib dipilih.',
            ]);
        }

        if ($data['distribution_target'] === 'kas_operasional' && ! $category->allow_operational_transfer) {
            throw ValidationException::withMessages([
                'distribution_target' => 'Dana ini tidak boleh dipindahkan ke Kas Operasional karena termasuk dana terikat/khusus.',
            ]);
        }

        if ($data['distribution_target'] === 'kas_operasional' && empty($data['zis_receipt_id'])) {
            throw ValidationException::withMessages([
                'zis_receipt_id' => 'Transfer ke Kas Operasional wajib terhubung ke penerimaan ZIS asal.',
            ]);
        }

        if ($category->isZakat()) {
            if ($data['distribution_target'] === 'kas_operasional') {
                throw ValidationException::withMessages([
                    'distribution_target' => 'Zakat tidak boleh dipindahkan ke Kas Operasional Masjid.',
                ]);
            }

            if (! in_array($data['recipient_type'] ?? null, self::ASNAF_TYPES, true)) {
                throw ValidationException::withMessages([
                    'recipient_type' => 'Untuk zakat, jenis penerima/asnaf wajib dipilih.',
                ]);
            }
        }

        if ($data['distribution_target'] !== 'kas_operasional' && blank($data['recipient_name'] ?? null)) {
            throw ValidationException::withMessages([
                'recipient_name' => 'Nama penerima wajib diisi.',
            ]);
        }
    }

    private function validateSourceReceipt(array $data, ?ZisReceipt $sourceReceipt): void
    {
        if (! $sourceReceipt) {
            return;
        }

        if ((int) $data['zis_category_id'] !== (int) $sourceReceipt->zis_category_id) {
            throw ValidationException::withMessages([
                'zis_category_id' => 'Kategori penyaluran harus sama dengan kategori sumber penerimaan.',
            ]);
        }

        if ((int) $data['cash_account_id'] !== (int) $sourceReceipt->cash_account_id) {
            throw ValidationException::withMessages([
                'cash_account_id' => 'Akun kas penyaluran harus sama dengan akun kas sumber penerimaan.',
            ]);
        }
    }

    private function validateRemainingAmount(array $data, ?ZisReceipt $sourceReceipt, ?ZisDistribution $distribution = null): void
    {
        if (! $sourceReceipt) {
            return;
        }

        $distributedQuery = $sourceReceipt->distributions();

        if ($distribution) {
            $distributedQuery->where('id', '!=', $distribution->id);
        }

        $distributedAmount = (float) $distributedQuery->sum('amount');
        $receiptAmount = (float) ($sourceReceipt->amount ?? $sourceReceipt->nominal_uang ?? 0);
        $remainingAmount = max($receiptAmount - $distributedAmount, 0);

        if ((float) $data['amount'] > $remainingAmount) {
            throw ValidationException::withMessages([
                'amount' => 'Nominal penyaluran tidak boleh melebihi sisa dana penerimaan. Sisa tersedia: Rp '.number_format($remainingAmount, 0, ',', '.').'.',
            ]);
        }
    }

    private function validateCategoryBalance(array $data, ?ZisCategory $category, ?ZisReceipt $sourceReceipt, ?ZisDistribution $distribution = null): void
    {
        if (! $category || $sourceReceipt) {
            return;
        }

        $receivedAmount = (float) ZisReceipt::where('mosque_id', $this->activeMosqueId())
            ->where('zis_category_id', $category->id)
            ->sum('amount');

        $distributedQuery = ZisDistribution::where('mosque_id', $this->activeMosqueId())
            ->where('zis_category_id', $category->id);

        if ($distribution) {
            $distributedQuery->where('id', '!=', $distribution->id);
        }

        $distributedAmount = (float) $distributedQuery->sum('amount');
        $availableBalance = max($receivedAmount - $distributedAmount, 0);

        if ((float) $data['amount'] > $availableBalance) {
            throw ValidationException::withMessages([
                'amount' => 'Nominal penyaluran tidak boleh melebihi saldo kategori. Saldo tersedia: Rp '.number_format($availableBalance, 0, ',', '.').'.',
            ]);
        }
    }

    private function validateCashAccountCategoryBalance(array $data, ?ZisCategory $category, ?ZisDistribution $distribution = null): void
    {
        if (! $category) {
            return;
        }

        $receivedAmount = (float) ZisReceipt::where('mosque_id', $this->activeMosqueId())
            ->where('cash_account_id', $data['cash_account_id'])
            ->where('zis_category_id', $category->id)
            ->sum('amount');

        $distributedQuery = ZisDistribution::where('mosque_id', $this->activeMosqueId())
            ->where('cash_account_id', $data['cash_account_id'])
            ->where('zis_category_id', $category->id);

        if ($distribution) {
            $distributedQuery->where('id', '!=', $distribution->id);
        }

        $availableBalance = max($receivedAmount - (float) $distributedQuery->sum('amount'), 0);

        if ((float) $data['amount'] > $availableBalance) {
            throw ValidationException::withMessages([
                'cash_account_id' => 'Saldo kategori pada akun kas ini tidak mencukupi. Saldo tersedia: Rp '.number_format($availableBalance, 0, ',', '.').'.',
            ]);
        }
    }
}
