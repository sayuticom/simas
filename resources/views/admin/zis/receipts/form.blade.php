<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700">Tanggal Penerimaan</label>
        <input type="date" name="receipt_date" value="{{ old('receipt_date', $receipt?->receipt_date?->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border px-4 py-3" required>
        @error('receipt_date')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700">Kategori ZIS</label>
        <select id="zis-receipt-category" name="zis_category_id" class="mt-2 w-full rounded-lg border px-4 py-3" required>
            <option value="">Pilih kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" data-name="{{ $category->name }}" {{ old('zis_category_id', $receipt?->zis_category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }} - {{ ucfirst($category->type) }}</option>
            @endforeach
        </select>
        @error('zis_category_id')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700">Tempat Dana / Akun Kas</label>
        <select id="zis-receipt-cash-account" name="cash_account_id" class="mt-2 w-full rounded-lg border px-4 py-3" required>
            <option value="">Pilih akun kas</option>
            @foreach($cashAccounts as $account)
                <option
                    value="{{ $account->id }}"
                    {{ old('cash_account_id', $receipt?->cash_account_id) == $account->id ? 'selected' : '' }}
                >
                    {{ $account->name }} - {{ $account->accountTypeLabel() }}
                </option>
            @endforeach
        </select>
        @error('cash_account_id')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        <p class="mt-1 text-xs text-gray-500">Hanya akun aktif yang diizinkan untuk Penerimaan ZIS yang tampil di sini.</p>
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700">Nama Donatur/Muzakki</label>
        <input type="text" name="donor_name" value="{{ old('donor_name', $receipt?->donor_name) }}" class="mt-2 w-full rounded-lg border px-4 py-3" placeholder="Kosongkan jika tidak diketahui">
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700">No. HP</label>
        <input type="text" name="donor_phone" value="{{ old('donor_phone', $receipt?->donor_phone) }}" class="mt-2 w-full rounded-lg border px-4 py-3">
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700">Nominal</label>
        <input type="number" step="0.01" name="amount" value="{{ old('amount', $receipt?->amount) }}" class="mt-2 w-full rounded-lg border px-4 py-3" required>
        @error('amount')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700">Bukti Transfer / Lampiran / Photo Penyerahan Dana</label>
        <input type="file" name="proof_file" accept="image/*,.pdf" class="mt-2 w-full text-sm">
        <p class="mt-1 text-xs text-gray-500">Wajib diisi. Upload bukti transfer, QRIS, lampiran pembayaran, atau foto penyerahan dana.</p>
        @error('proof_file')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
<div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
    Kwitansi penerimaan resmi dapat dicetak setelah data disimpan.
</div>
<div>
    <label class="block text-sm font-semibold text-gray-700">Keterangan</label>
    <textarea name="description" rows="3" class="mt-2 w-full rounded-lg border px-4 py-3">{{ old('description', $receipt?->description) }}</textarea>
</div>
