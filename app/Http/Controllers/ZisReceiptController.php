<?php

namespace App\Http\Controllers;

use App\Models\Muzakki;
use App\Models\CashAccount;
use App\Models\ZisCategory;
use App\Models\ZisProgram;
use App\Models\ZisReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ZisReceiptController extends Controller
{
    public function index(): View
    {
        $receipts = ZisReceipt::with(['cashAccount', 'category', 'recappedBy'])
            ->withSum('distributions as distributed_amount', 'amount')
            ->latest('receipt_date')
            ->paginate(10);

        return view('admin.zis.receipts.index', compact('receipts'));
    }

    public function create(): View
    {
        $categories = $this->activeCategories();
        $cashAccounts = $this->activeCashAccounts();

        // Generate a one-time form token to prevent double submit via browser Back
        $formToken = bin2hex(random_bytes(16));
        session(['zis_receipt_form_token' => $formToken]);

        return view('admin.zis.receipts.create', compact('cashAccounts', 'categories', 'formToken'));
    }

    public function store(Request $request)
    {
        // Check one-time form token before validating
        $sessionToken = session('zis_receipt_form_token');
        $formToken = $request->input('form_token');

        if (! $formToken || ! $sessionToken || ! hash_equals($sessionToken, $formToken)) {
            return redirect()->route('zis.receipts.index')
                ->with('error', 'Form ini sudah diproses. Silakan buat penerimaan baru jika ingin input transaksi lain.');
        }

        // Run validation first. If validation fails, token remains in session so user can correct and resubmit.

        $data = $this->validatedData($request, null);

        $category = $this->category((int) $data['zis_category_id']);
        abort_unless($category->is_active, 422, 'Kategori ZIS nonaktif tidak bisa dipilih.');
        $this->ensureActiveCashAccount((int) $data['cash_account_id']);

        // Invalidate token now to prevent reuse (before file upload / DB operations)
        session()->forget('zis_receipt_form_token');

        // Wrap storage + DB create in a transaction; delete stored file if rollback
        $filePath = null;

        DB::beginTransaction();
        try {
            // Token publik akan otomatis dibuat oleh model.
            // Setelah store berhasil, receipt dikunci sebagai sudah diterbitkan.

            if ($request->hasFile('proof_file')) {
                $filePath = $request->file('proof_file')->store('zis/receipts', 'public');
                $data['proof_file'] = $filePath;
            }

            $receipt = ZisReceipt::create($this->payload($data, $category));

            // Ensure public token exists
            $receipt->ensurePublicReceiptToken();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            // remove uploaded file to avoid orphan
            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }

            // regenerate a new token so user can retry safely
            $newToken = bin2hex(random_bytes(16));
            session(['zis_receipt_form_token' => $newToken]);

            return redirect()->route('zis.receipts.create')->withInput()->with('error', 'Terjadi kesalahan saat menyimpan. Silakan coba lagi.');
        }

        // Lock: mark receipt as issued (digital receipt public URL is ready)
        $receipt->update([
            'receipt_status' => 'sudah_diterbitkan',
            'receipt_issued_at' => now(),
            'receipt_issued_by' => auth()->id(),
        ]);

        $publicReceiptUrl = route('zis.penerimaan.receipt.public', $receipt->public_receipt_token);

        return redirect($publicReceiptUrl)->with('success', 'Penerimaan ZIS berhasil disimpan. Bukti tanda terima digital sudah tersedia.');
    }

    public function show(ZisReceipt $receipt): View
    {
        $this->ensureOwnReceipt($receipt);
        $receipt->load(['cashAccount', 'category', 'distributions.cashAccount', 'distributions.category', 'recappedBy']);
        $receipt->loadSum('distributions as distributed_amount', 'amount');
        $receipt->ensurePublicReceiptToken();
        $publicReceiptUrl = route('zis.penerimaan.receipt.public', $receipt->public_receipt_token);

        return view('admin.zis.receipts.show', compact('publicReceiptUrl', 'receipt'));
    }

    public function kwitansi(ZisReceipt $receipt): View
    {
        $this->ensureOwnReceipt($receipt);
        $receipt->load(['cashAccount', 'category']);
        $receipt->ensurePublicReceiptToken();

        $activeMosque = auth()->user()?->activeMosque;
        $receiptNumber = $receipt->receiptNumber();

        return view('admin.zis.receipts.kwitansi', compact('activeMosque', 'receipt', 'receiptNumber'));
    }

    public function publicReceipt(string $token): View
    {
        $receipt = ZisReceipt::withoutGlobalScope('mosque')
            ->with(['cashAccount', 'category', 'mosque.profile'])
            ->where('public_receipt_token', $token)
            ->firstOrFail();

        $activeMosque = $receipt->mosque;
        $receiptNumber = $receipt->receiptNumber();

        return view('public.zis.receipt', compact('activeMosque', 'receipt', 'receiptNumber'));
    }

    public function edit(ZisReceipt $receipt): View
    {
        $this->ensureOwnReceipt($receipt);

        if ($receipt->isLocked()) {
            abort(403, $this->lockedReceiptMessage());
        }

        $categories = $this->activeCategories($receipt->zis_category_id);
        $cashAccounts = $this->activeCashAccounts($receipt->cash_account_id);

        return view('admin.zis.receipts.edit', compact('cashAccounts', 'receipt', 'categories'));
    }

    public function update(Request $request, ZisReceipt $receipt)
    {
        $this->ensureOwnReceipt($receipt);

        if ($receipt->isLocked()) {
            abort(403, $this->lockedReceiptMessage());
        }

        $data = $this->validatedData($request, $receipt);
        $category = $this->category((int) $data['zis_category_id']);
        abort_if(! $category->is_active && (int) $category->id !== (int) $receipt->zis_category_id, 422, 'Kategori ZIS nonaktif tidak bisa dipilih.');
        $this->ensureActiveCashAccount((int) $data['cash_account_id'], $receipt->cash_account_id);
        $this->validateAmountNotBelowDistributed($data, $receipt);
        $this->validateSourceFieldsNotChangedAfterDistribution($data, $receipt);

        if ($request->hasFile('proof_file')) {
            if ($receipt->proof_file) {
                Storage::disk('public')->delete($receipt->proof_file);
            }

            $data['proof_file'] = $request->file('proof_file')->store('zis/receipts', 'public');
        }

        $receipt->update($this->payload($data, $category, $receipt));

        $receipt->ensurePublicReceiptToken();

        return redirect()->route('zis.receipts.show', $receipt)->with('success', 'Penerimaan ZIS berhasil diperbarui. Bukti tanda terima digital sudah tersedia.');
    }

    public function destroy(ZisReceipt $receipt)
    {
        $this->ensureOwnReceipt($receipt);

        if ($receipt->isLocked()) {
            abort(403, $this->lockedReceiptMessage());
        }

        if ($receipt->distributions()->exists()) {
            return redirect()
                ->route('zis.receipts.index')
                ->with('error', 'Penerimaan ini sudah memiliki riwayat penyaluran dan tidak bisa dihapus langsung.');
        }

        if ($receipt->proof_file) {
            Storage::disk('public')->delete($receipt->proof_file);
        }

        $receipt->delete();

        return redirect()->route('zis.receipts.index')->with('success', 'Penerimaan ZIS berhasil dihapus.');
    }

    public function markRecapped(Request $request, ZisReceipt $receipt)
    {
        $this->ensureOwnReceipt($receipt);

        $data = $request->validate([
            'recap_note' => 'nullable|string|max:1000',
        ]);

        if ($receipt->isRecapped()) {
            return back()->with('success', 'Penerimaan ZIS ini sudah direkap/disetorkan ke Bendahara.');
        }

        $receipt->update([
            'recap_status' => ZisReceipt::RECAP_STATUS_SUDAH_DIREKAP,
            'recapped_at' => now(),
            'recapped_by' => auth()->id(),
            'recap_note' => $data['recap_note'] ?? null,
        ]);

        return back()->with('success', 'Penerimaan ZIS berhasil ditandai sudah direkap/disetorkan ke Bendahara.');
    }

    private function validatedData(Request $request, ?ZisReceipt $receipt = null): array
    {
        $proofRule = $receipt && $receipt->proof_file ? 'nullable' : 'required';

        $messages = [
            'proof_file.required' => 'Bukti transfer / lampiran / foto penyerahan dana wajib diisi.',
        ];

        return $request->validate([
            'zis_category_id' => [
                'required',
                Rule::exists('zis_categories', 'id')
                    ->where('mosque_id', $this->activeMosqueId()),
            ],
            'cash_account_id' => [
                'required',
                Rule::exists('cash_accounts', 'id')
                    ->where('mosque_id', $this->activeMosqueId()),
            ],
            'receipt_date' => 'required|date',
            'donor_name' => 'nullable|string|max:255',
            'donor_phone' => 'nullable|string|max:50',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'proof_file' => $proofRule . '|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ], $messages);
    }

    private function payload(array $data, ZisCategory $category, ?ZisReceipt $receipt = null): array
    {
        $donorName = filled($data['donor_name'] ?? null) ? $data['donor_name'] : null;
        $donorPhone = filled($data['donor_phone'] ?? null) ? $data['donor_phone'] : null;
        $paymentMethod = CashAccount::where('mosque_id', $this->activeMosqueId())
            ->findOrFail($data['cash_account_id'])
            ->paymentMethodValue();

        return [
            'mosque_id' => $this->activeMosqueId(),
            'zis_category_id' => $category->id,
            'cash_account_id' => $data['cash_account_id'],
            'receipt_date' => $data['receipt_date'],
            'donor_name' => $donorName,
            'donor_phone' => $donorPhone,
            'amount' => $data['amount'],
            'payment_method' => $paymentMethod,
            'description' => $data['description'] ?? null,
            'proof_file' => $data['proof_file'] ?? $receipt?->proof_file,
            'created_by' => $receipt?->created_by ?? auth()->user()?->name,
            'jenis_penerimaan' => $category->name,
            'muzakki_id' => $this->ensureMuzakki($donorName, $donorPhone),
            'zis_program_id' => $this->ensureProgram($category->name),
            'tanggal' => $data['receipt_date'],
            'metode_pembayaran' => $paymentMethod,
            'jenis_zakat' => $category->type === ZisCategory::TYPE_ZAKAT ? $category->name : null,
            'nominal_uang' => $data['amount'],
            'bukti_file' => $data['proof_file'] ?? $receipt?->bukti_file,
            'diterima_oleh' => $receipt?->diterima_oleh ?? auth()->user()?->name,
            'keterangan' => $data['description'] ?? null,
        ];
    }

    private function activeCategories(?int $includeId = null)
    {
        ZisCategory::ensureDefaultsForMosque($this->activeMosqueId());

        return ZisCategory::where('mosque_id', $this->activeMosqueId())
            ->where(function ($query) use ($includeId) {
                $query->where('is_active', true)
                    ->when($includeId, fn ($q) => $q->orWhere('id', $includeId));
            })
            ->orderBy('type')
            ->orderBy('name')
            ->get();
    }

    private function activeCashAccounts(?int $includeId = null)
    {
        CashAccount::ensureDefaultsForMosque($this->activeMosqueId());

        return CashAccount::where('mosque_id', $this->activeMosqueId())
            ->where(function ($query) use ($includeId) {
                $query->where('is_active', true)
                    ->when($includeId, fn ($q) => $q->orWhere('id', $includeId));
            })
            ->orderBy('type')
            ->orderBy('name')
            ->get();
    }

    private function category(int $id): ZisCategory
    {
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

        if (! $account->can_receive_zis && (int) $account->id !== (int) $includeId) {
            throw ValidationException::withMessages([
                'cash_account_id' => 'Akun kas ini tidak diizinkan untuk Penerimaan ZIS.',
            ]);
        }
    }

    private function ensureMuzakki(?string $name, ?string $phone): int
    {
        return Muzakki::firstOrCreate(
            [
                'mosque_id' => $this->activeMosqueId(),
                'nama' => $name ?: 'Tidak dicantumkan',
                'no_hp' => $phone,
            ],
            ['keterangan' => 'Otomatis dari penerimaan ZIS']
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

    private function ensureOwnReceipt(ZisReceipt $receipt): void
    {
        abort_unless((int) $receipt->mosque_id === $this->activeMosqueId(), 404);
    }

    private function activeMosqueId(): int
    {
        return (int) session('active_mosque_id');
    }

    private function validateAmountNotBelowDistributed(array $data, ZisReceipt $receipt): void
    {
        $distributedAmount = (float) $receipt->distributions()->sum('amount');

        if ((float) $data['amount'] < $distributedAmount) {
            throw ValidationException::withMessages([
                'amount' => 'Nominal penerimaan tidak boleh lebih kecil dari total yang sudah disalurkan.',
            ]);
        }
    }

    private function validateSourceFieldsNotChangedAfterDistribution(array $data, ZisReceipt $receipt): void
    {
        if (! $receipt->distributions()->exists()) {
            return;
        }

        if ((int) $data['zis_category_id'] !== (int) $receipt->zis_category_id) {
            throw ValidationException::withMessages([
                'zis_category_id' => 'Kategori tidak bisa diubah karena penerimaan ini sudah memiliki riwayat penyaluran.',
            ]);
        }

        if ((int) $data['cash_account_id'] !== (int) $receipt->cash_account_id) {
            throw ValidationException::withMessages([
                'cash_account_id' => 'Akun kas tidak bisa diubah karena penerimaan ini sudah memiliki riwayat penyaluran.',
            ]);
        }
    }

    private function lockedReceiptMessage(): string
    {
        return 'Penerimaan ZIS ini sudah terkunci karena tanda terima digital sudah diterbitkan atau sudah direkap/disetorkan ke Bendahara.';
    }
}
