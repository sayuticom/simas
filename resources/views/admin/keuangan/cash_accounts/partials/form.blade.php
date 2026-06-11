<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700">Nama Akun</label>
        <input type="text" name="name" value="{{ old('name', $cashAccount?->name) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Jenis Akun</label>
        <select name="type" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Pilih jenis akun</option>
            @foreach($typeOptions as $value => $label)
                <option value="{{ $value }}" {{ old('type', $cashAccount?->type) === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Nama Bank / Provider</label>
        <input type="text" name="bank_name" value="{{ old('bank_name', $cashAccount?->bank_name) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: BSI, BCA, QRIS">
        @error('bank_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Nomor Rekening / ID</label>
        <input type="text" name="account_number" value="{{ old('account_number', $cashAccount?->account_number) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('account_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Atas Nama</label>
        <input type="text" name="account_holder" value="{{ old('account_holder', $cashAccount?->account_holder) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('account_holder')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="flex items-center rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
        <input type="hidden" name="is_active" value="0">
        <input id="is_active" type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ old('is_active', $cashAccount?->is_active ?? true) ? 'checked' : '' }}>
        <label for="is_active" class="ml-3 text-sm font-semibold text-gray-700">Akun aktif dan bisa dipilih di form transaksi baru</label>
    </div>

    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 lg:col-span-2">
        <p class="text-sm font-semibold text-gray-700">Penggunaan Akun</p>
        <p class="mt-1 text-xs text-gray-500">Matikan opsi yang tidak boleh memakai akun ini. Akun nonaktif tetap tidak muncul di semua transaksi baru.</p>
        <div class="mt-4 grid gap-3 md:grid-cols-3">
            <label class="flex items-center rounded-lg bg-white px-4 py-3">
                <input type="hidden" name="can_receive_zis" value="0">
                <input type="checkbox" name="can_receive_zis" value="1" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ old('can_receive_zis', $cashAccount?->can_receive_zis ?? true) ? 'checked' : '' }}>
                <span class="ml-3 text-sm font-semibold text-gray-700">Bisa dipakai untuk Penerimaan ZIS</span>
            </label>
            <label class="flex items-center rounded-lg bg-white px-4 py-3">
                <input type="hidden" name="can_distribute_zis" value="0">
                <input type="checkbox" name="can_distribute_zis" value="1" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ old('can_distribute_zis', $cashAccount?->can_distribute_zis ?? true) ? 'checked' : '' }}>
                <span class="ml-3 text-sm font-semibold text-gray-700">Bisa dipakai untuk Penyaluran ZIS</span>
            </label>
            <label class="flex items-center rounded-lg bg-white px-4 py-3">
                <input type="hidden" name="can_operational" value="0">
                <input type="checkbox" name="can_operational" value="1" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ old('can_operational', $cashAccount?->can_operational ?? true) ? 'checked' : '' }}>
                <span class="ml-3 text-sm font-semibold text-gray-700">Bisa dipakai untuk Operasional</span>
            </label>
        </div>
    </div>
</div>
