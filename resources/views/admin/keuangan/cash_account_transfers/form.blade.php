<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700">Akun Asal</label>
        <select name="from_cash_account_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Pilih akun asal</option>
            @foreach($cashAccounts as $account)
                <option value="{{ $account->id }}" {{ old('from_cash_account_id') == $account->id ? 'selected' : '' }}>
                    {{ $account->name }} - {{ $account->accountTypeLabel() }} | Saldo: Rp {{ number_format($account->available_balance, 0, ',', '.') }}
                </option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-gray-500">Saldo akun asal adalah saldo fisik akun kas setelah ZIS, operasional, dan mutasi.</p>
        @error('from_cash_account_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Akun Tujuan</label>
        <select name="to_cash_account_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Pilih akun tujuan</option>
            @foreach($cashAccounts as $account)
                <option value="{{ $account->id }}" {{ old('to_cash_account_id') == $account->id ? 'selected' : '' }}>
                    {{ $account->name }} - {{ $account->accountTypeLabel() }}
                </option>
            @endforeach
        </select>
        @error('to_cash_account_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Tanggal Mutasi</label>
        <input type="date" name="transfer_date" value="{{ old('transfer_date', now()->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
        @error('transfer_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Nominal (Rp)</label>
        <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
        @error('amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="lg:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">Catatan</label>
        <textarea name="note" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: Setor kas tunai ke rekening bank">{{ old('note') }}</textarea>
        @error('note')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
