<?php

namespace App\Http\Controllers;

use App\Models\CashAccount;
use App\Models\CashAccountTransfer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CashAccountTransferController extends Controller
{
    public function index(): View
    {
        $transfers = CashAccountTransfer::with(['creator', 'fromAccount', 'toAccount'])
            ->where('mosque_id', $this->activeMosqueId())
            ->latest('transfer_date')
            ->paginate(10);

        return view('admin.keuangan.cash_account_transfers.index', compact('transfers'));
    }

    public function create(): View
    {
        $cashAccounts = $this->activeCashAccounts();

        return view('admin.keuangan.cash_account_transfers.create', compact('cashAccounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'from_cash_account_id' => [
                'required',
                Rule::exists('cash_accounts', 'id')
                    ->where('mosque_id', $this->activeMosqueId())
                    ->where('is_active', true),
            ],
            'to_cash_account_id' => [
                'required',
                'different:from_cash_account_id',
                Rule::exists('cash_accounts', 'id')
                    ->where('mosque_id', $this->activeMosqueId())
                    ->where('is_active', true),
            ],
            'amount' => 'required|numeric|gt:0',
            'transfer_date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        $fromAccount = CashAccount::where('mosque_id', $this->activeMosqueId())
            ->where('is_active', true)
            ->findOrFail($data['from_cash_account_id']);
        $availableBalance = $fromAccount->availableBalance();

        if ((float) $data['amount'] > $availableBalance) {
            throw ValidationException::withMessages([
                'amount' => 'Nominal mutasi tidak boleh melebihi saldo akun asal. Saldo tersedia: Rp '.number_format($availableBalance, 0, ',', '.').'.',
            ]);
        }

        $transfer = CashAccountTransfer::create([
            'mosque_id' => $this->activeMosqueId(),
            'from_cash_account_id' => $data['from_cash_account_id'],
            'to_cash_account_id' => $data['to_cash_account_id'],
            'amount' => $data['amount'],
            'transfer_date' => $data['transfer_date'],
            'note' => $data['note'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('keuangan.mutasi-akun-kas.show', $transfer)->with('success', 'Mutasi akun kas berhasil disimpan.');
    }

    public function show(CashAccountTransfer $cashAccountTransfer): View
    {
        $this->ensureOwnTransfer($cashAccountTransfer);
        $cashAccountTransfer->load(['creator', 'fromAccount', 'toAccount']);

        return view('admin.keuangan.cash_account_transfers.show', compact('cashAccountTransfer'));
    }

    private function activeMosqueId(): int
    {
        return (int) session('active_mosque_id');
    }

    private function activeCashAccounts()
    {
        CashAccount::ensureDefaultsForMosque($this->activeMosqueId());

        return CashAccount::where('mosque_id', $this->activeMosqueId())
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->map(function (CashAccount $account) {
                $account->available_balance = $account->availableBalance();

                return $account;
            });
    }

    private function ensureOwnTransfer(CashAccountTransfer $cashAccountTransfer): void
    {
        abort_unless((int) $cashAccountTransfer->mosque_id === $this->activeMosqueId(), 404);
    }
}
