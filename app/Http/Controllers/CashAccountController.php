<?php

namespace App\Http\Controllers;

use App\Models\CashAccount;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CashAccountController extends Controller
{
    public function index(): View
    {
        $mosqueId = $this->activeMosqueId();
        CashAccount::ensureDefaultsForMosque($mosqueId);

        $cashAccounts = CashAccount::where('mosque_id', $mosqueId)
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->map(function (CashAccount $account) {
                $account->zis_balance = $account->zisBalance();
                $account->operational_balance = $account->operationalBalance();
                $account->transfer_balance = $account->transferBalance();
                $account->available_balance = $account->availableBalance();
                $account->is_used = $this->isUsed($account);

                return $account;
            });

        return view('admin.keuangan.cash_accounts.index', [
            'cashAccounts' => $cashAccounts,
            'typeOptions' => CashAccount::TYPE_OPTIONS,
        ]);
    }

    public function create(): View
    {
        return view('admin.keuangan.cash_accounts.create', [
            'cashAccount' => null,
            'typeOptions' => CashAccount::TYPE_OPTIONS,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $accountType = CashAccount::accountTypeForType($data['type']);

        CashAccount::create([
            'mosque_id' => $this->activeMosqueId(),
            'name' => $data['name'],
            'type' => $data['type'],
            'account_type' => $accountType,
            'bank_name' => $data['bank_name'] ?? null,
            'account_number' => $data['account_number'] ?? null,
            'account_holder' => $data['account_holder'] ?? null,
            'is_active' => $request->boolean('is_active'),
            ...$this->usageFlags($request, $accountType),
        ]);

        return redirect()->route('keuangan.akun-kas.index')->with('success', 'Akun kas berhasil ditambahkan.');
    }

    public function edit(CashAccount $cashAccount): View
    {
        $this->ensureOwnCashAccount($cashAccount);

        return view('admin.keuangan.cash_accounts.edit', [
            'cashAccount' => $cashAccount,
            'typeOptions' => CashAccount::TYPE_OPTIONS,
        ]);
    }

    public function update(Request $request, CashAccount $cashAccount)
    {
        $this->ensureOwnCashAccount($cashAccount);
        $data = $this->validatedData($request, $cashAccount);
        $accountType = CashAccount::accountTypeForType($data['type']);

        $cashAccount->update([
            'name' => $data['name'],
            'type' => $data['type'],
            'account_type' => $accountType,
            'bank_name' => $data['bank_name'] ?? null,
            'account_number' => $data['account_number'] ?? null,
            'account_holder' => $data['account_holder'] ?? null,
            'is_active' => $request->boolean('is_active'),
            ...$this->usageFlags($request, $accountType),
        ]);

        return redirect()->route('keuangan.akun-kas.index')->with('success', 'Akun kas berhasil diperbarui.');
    }

    public function destroy(CashAccount $cashAccount)
    {
        $this->ensureOwnCashAccount($cashAccount);

        if ($this->isUsed($cashAccount)) {
            $cashAccount->update(['is_active' => false]);

            return redirect()
                ->route('keuangan.akun-kas.index')
                ->with('success', 'Akun kas sudah memiliki transaksi, sehingga hanya dinonaktifkan.');
        }

        $cashAccount->delete();

        return redirect()->route('keuangan.akun-kas.index')->with('success', 'Akun kas berhasil dihapus.');
    }

    private function validatedData(Request $request, ?CashAccount $cashAccount = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cash_accounts', 'name')
                    ->ignore($cashAccount?->id)
                    ->where(fn ($query) => $query->where('mosque_id', $this->activeMosqueId())),
            ],
            'type' => ['required', Rule::in(array_keys(CashAccount::TYPE_OPTIONS))],
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'account_holder' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'can_receive_zis' => 'nullable|boolean',
            'can_distribute_zis' => 'nullable|boolean',
            'can_operational' => 'nullable|boolean',
        ]);
    }

    private function usageFlags(Request $request, string $accountType): array
    {
        $defaults = CashAccount::defaultUsageFlagsForAccountType($accountType);

        return [
            'can_receive_zis' => $request->has('can_receive_zis') ? $request->boolean('can_receive_zis') : $defaults['can_receive_zis'],
            'can_distribute_zis' => $request->has('can_distribute_zis') ? $request->boolean('can_distribute_zis') : $defaults['can_distribute_zis'],
            'can_operational' => $request->has('can_operational') ? $request->boolean('can_operational') : $defaults['can_operational'],
        ];
    }

    private function activeMosqueId(): int
    {
        return (int) session('active_mosque_id');
    }

    private function ensureOwnCashAccount(CashAccount $cashAccount): void
    {
        abort_unless((int) $cashAccount->mosque_id === $this->activeMosqueId(), 404);
    }

    private function isUsed(CashAccount $cashAccount): bool
    {
        return $cashAccount->receipts()->exists()
            || $cashAccount->distributions()->exists()
            || $cashAccount->transactions()->exists()
            || $cashAccount->incomingTransfers()->exists()
            || $cashAccount->outgoingTransfers()->exists();
    }
}
