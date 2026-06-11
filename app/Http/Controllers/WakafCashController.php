<?php

namespace App\Http\Controllers;

use App\Models\CashAccount;
use App\Models\Nazhir;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\WakafCash;
use App\Models\WakafProgram;
use App\Models\Wakif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class WakafCashController extends Controller
{
    private const SOURCE_WAKAF_CASH = 'wakaf_cash';

    public function index()
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId) {
            return redirect()->route('wakaf.index')->with('error', 'Pilih masjid aktif terlebih dahulu.');
        }

        $cashRecords = WakafCash::with(['wakif', 'nazhir', 'program', 'cashAccount'])
            ->where('mosque_id', $mosqueId)
            ->latest()
            ->paginate(10);

        return view('admin.wakaf.cash.index', compact('cashRecords'));
    }

    public function create()
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId) {
            return redirect()->route('wakaf.index')->with('error', 'Pilih masjid aktif terlebih dahulu.');
        }

        [$wakifs, $nazhirs, $programs, $cashAccounts] = $this->formOptions($mosqueId);

        return view('admin.wakaf.cash.create', compact('wakifs', 'nazhirs', 'programs', 'cashAccounts'));
    }

    public function store(Request $request)
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId) {
            return redirect()->route('wakaf.index')->with('error', 'Pilih masjid aktif terlebih dahulu.');
        }

        $data = $request->validate($this->rules($mosqueId));

        $data['status'] = $data['status'] ?? 'tercatat';
        $data['metode_pembayaran'] = $data['metode_pembayaran'] ?? 'tunai';

        if ($request->hasFile('bukti_file')) {
            $data['bukti_file'] = $request->file('bukti_file')->store('wakaf/cashes', 'public');
        }

        if ($request->hasFile('dokumen_ikrar')) {
            $data['dokumen_ikrar'] = $request->file('dokumen_ikrar')->store('wakaf/cashes', 'public');
        }

        DB::transaction(function () use ($data): void {
            $wakafCash = WakafCash::create($data);
            $this->syncCashTransaction($wakafCash);
        });

        return redirect()->route('wakaf.cash.index')->with('success', 'Wakaf Tunai berhasil disimpan.');
    }

    public function show(WakafCash $wakafCash)
    {
        $this->authorizeCash($wakafCash);
        $wakafCash->load(['wakif', 'nazhir', 'program', 'cashAccount']);

        return view('admin.wakaf.cash.show', compact('wakafCash'));
    }

    public function receipt(WakafCash $wakafCash)
    {
        $this->authorizeCash($wakafCash);

        $wakafCash->load(['wakif', 'nazhir', 'program', 'cashAccount']);

        $activeMosque = auth()->user()?->activeMosque;
        $receiptNumber = sprintf('WKT-%s-%06d', $wakafCash->tanggal_terima?->format('Y') ?? now()->format('Y'), $wakafCash->id);
        $terbilang = $this->rupiahTerbilang((int) $wakafCash->nominal);

        return view('admin.wakaf.cash.receipt', compact('wakafCash', 'activeMosque', 'receiptNumber', 'terbilang'));
    }

    public function edit(WakafCash $wakafCash)
    {
        $this->authorizeCash($wakafCash);

        $mosqueId = $this->activeMosqueId();
        [$wakifs, $nazhirs, $programs, $cashAccounts] = $this->formOptions($mosqueId);

        return view('admin.wakaf.cash.edit', compact('wakafCash', 'wakifs', 'nazhirs', 'programs', 'cashAccounts'));
    }

    public function update(Request $request, WakafCash $wakafCash)
    {
        $this->authorizeCash($wakafCash);

        $mosqueId = $this->activeMosqueId();
        $data = $request->validate($this->rules($mosqueId));

        $data['status'] = $data['status'] ?? 'tercatat';
        $data['metode_pembayaran'] = $data['metode_pembayaran'] ?? 'tunai';

        if ($request->hasFile('bukti_file')) {
            if ($wakafCash->bukti_file && Storage::disk('public')->exists($wakafCash->bukti_file)) {
                Storage::disk('public')->delete($wakafCash->bukti_file);
            }
            $data['bukti_file'] = $request->file('bukti_file')->store('wakaf/cashes', 'public');
        }

        if ($request->hasFile('dokumen_ikrar')) {
            if ($wakafCash->dokumen_ikrar && Storage::disk('public')->exists($wakafCash->dokumen_ikrar)) {
                Storage::disk('public')->delete($wakafCash->dokumen_ikrar);
            }
            $data['dokumen_ikrar'] = $request->file('dokumen_ikrar')->store('wakaf/cashes', 'public');
        }

        DB::transaction(function () use ($data, $wakafCash): void {
            $wakafCash->update($data);
            $this->syncCashTransaction($wakafCash->refresh());
        });

        return redirect()->route('wakaf.cash.index')->with('success', 'Wakaf Tunai berhasil diperbarui.');
    }

    public function destroy(WakafCash $wakafCash)
    {
        $this->authorizeCash($wakafCash);

        DB::transaction(function () use ($wakafCash): void {
            $this->deleteCashTransaction($wakafCash);

            if ($wakafCash->bukti_file && Storage::disk('public')->exists($wakafCash->bukti_file)) {
                Storage::disk('public')->delete($wakafCash->bukti_file);
            }

            if ($wakafCash->dokumen_ikrar && Storage::disk('public')->exists($wakafCash->dokumen_ikrar)) {
                Storage::disk('public')->delete($wakafCash->dokumen_ikrar);
            }

            $wakafCash->delete();
        });

        return redirect()->route('wakaf.cash.index')->with('success', 'Wakaf Tunai berhasil dihapus.');
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
            'waqf_program_id' => [
                'required',
                Rule::exists('wakaf_programs', 'id')->where('mosque_id', $mosqueId),
            ],
            'tanggal_terima' => 'required|date',
            'nominal' => 'required|numeric|min:0',
            'tujuan_investasi' => 'nullable|string',
            'metode_pembayaran' => ['nullable', Rule::in(['tunai', 'transfer', 'qris', 'ewallet', 'lainnya'])],
            'cash_account_id' => [
                'nullable',
                Rule::exists('cash_accounts', 'id')
                    ->where('mosque_id', $mosqueId)
                    ->where('is_active', true),
            ],
            'bukti_file' => 'nullable|file|max:2048',
            'dokumen_ikrar' => 'nullable|file|max:2048',
            'status' => ['nullable', Rule::in(['tercatat', 'diverifikasi', 'batal'])],
            'keterangan' => 'nullable|string',
        ];
    }

    private function formOptions(int $mosqueId): array
    {
        return [
            Wakif::where('mosque_id', $mosqueId)->orderBy('nama')->get(),
            Nazhir::where('mosque_id', $mosqueId)->orderBy('nama')->get(),
            WakafProgram::where('mosque_id', $mosqueId)->orderBy('nama')->get(),
            CashAccount::where('mosque_id', $mosqueId)
                ->where('is_active', true)
                ->orderBy('type')
                ->orderBy('name')
                ->get(),
        ];
    }

    private function activeMosqueId(): ?int
    {
        return session('active_mosque_id') ?: auth()->user()?->active_mosque_id;
    }

    private function authorizeCash(WakafCash $wakafCash): void
    {
        $mosqueId = $this->activeMosqueId();
        if (! $mosqueId || (int) $wakafCash->mosque_id !== (int) $mosqueId) {
            abort(404);
        }
    }

    private function syncCashTransaction(WakafCash $wakafCash): void
    {
        if (! $wakafCash->cash_account_id || $wakafCash->status === 'batal') {
            $this->deleteCashTransaction($wakafCash);

            return;
        }

        $wakafCash->loadMissing('wakif');
        $category = $this->wakafTunaiCategory((int) $wakafCash->mosque_id);
        $wakifName = $wakafCash->wakif?->nama ?: 'wakif';

        Transaction::withoutGlobalScopes()->updateOrCreate(
            [
                'source_type' => self::SOURCE_WAKAF_CASH,
                'source_id' => $wakafCash->id,
            ],
            [
                'mosque_id' => $wakafCash->mosque_id,
                'transaction_date' => $wakafCash->tanggal_terima,
                'type' => TransactionCategory::TYPE_MASUK,
                'transaction_category_id' => $category->id,
                'cash_account_id' => $wakafCash->cash_account_id,
                'amount' => $wakafCash->nominal,
                'description' => 'Penerimaan Wakaf Tunai dari '.$wakifName,
                'payment_method' => $wakafCash->metode_pembayaran,
                'proof_file' => $wakafCash->bukti_file,
                'created_by' => auth()->user()?->name ?? 'Admin',
            ]
        );
    }

    private function deleteCashTransaction(WakafCash $wakafCash): void
    {
        Transaction::withoutGlobalScopes()
            ->where('source_type', self::SOURCE_WAKAF_CASH)
            ->where('source_id', $wakafCash->id)
            ->delete();
    }

    private function wakafTunaiCategory(int $mosqueId): TransactionCategory
    {
        return TransactionCategory::withoutGlobalScopes()->updateOrCreate(
            [
                'mosque_id' => $mosqueId,
                'name' => 'Wakaf Tunai',
            ],
            [
                'type' => TransactionCategory::TYPE_MASUK,
                'is_active' => true,
                'description' => 'Penerimaan wakaf tunai',
            ]
        );
    }

    private function rupiahTerbilang(int $amount): string
    {
        if ($amount === 0) {
            return 'Nol rupiah';
        }

        return ucfirst($this->numberToWords($amount)).' rupiah';
    }

    private function numberToWords(int $number): string
    {
        $words = [
            '',
            'satu',
            'dua',
            'tiga',
            'empat',
            'lima',
            'enam',
            'tujuh',
            'delapan',
            'sembilan',
            'sepuluh',
            'sebelas',
        ];

        if ($number < 12) {
            return $words[$number];
        }

        if ($number < 20) {
            return $this->numberToWords($number - 10).' belas';
        }

        if ($number < 100) {
            return trim($this->numberToWords(intdiv($number, 10)).' puluh '.$this->numberToWords($number % 10));
        }

        if ($number < 200) {
            return trim('seratus '.$this->numberToWords($number - 100));
        }

        if ($number < 1000) {
            return trim($this->numberToWords(intdiv($number, 100)).' ratus '.$this->numberToWords($number % 100));
        }

        if ($number < 2000) {
            return trim('seribu '.$this->numberToWords($number - 1000));
        }

        if ($number < 1000000) {
            return trim($this->numberToWords(intdiv($number, 1000)).' ribu '.$this->numberToWords($number % 1000));
        }

        if ($number < 1000000000) {
            return trim($this->numberToWords(intdiv($number, 1000000)).' juta '.$this->numberToWords($number % 1000000));
        }

        if ($number < 1000000000000) {
            return trim($this->numberToWords(intdiv($number, 1000000000)).' miliar '.$this->numberToWords($number % 1000000000));
        }

        return trim($this->numberToWords(intdiv($number, 1000000000000)).' triliun '.$this->numberToWords($number % 1000000000000));
    }
}
