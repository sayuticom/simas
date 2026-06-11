<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="lg:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">Aset Wakaf <span class="text-red-600">*</span></label>
        <select name="waqf_asset_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Pilih Aset Wakaf</option>
            @foreach($assets as $asset)
                <option value="{{ $asset->id }}" {{ (int) old('waqf_asset_id', $maintenance?->waqf_asset_id) === $asset->id ? 'selected' : '' }}>
                    {{ $asset->nama_aset }} - {{ $asset->jenis_aset ?: 'Aset Wakaf' }}
                </option>
            @endforeach
        </select>
        @error('waqf_asset_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Tanggal Pengeluaran <span class="text-red-600">*</span></label>
        <input type="date" name="tanggal_pengeluaran" value="{{ old('tanggal_pengeluaran', $maintenance?->tanggal_pengeluaran?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
        @error('tanggal_pengeluaran')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Nominal <span class="text-red-600">*</span></label>
        <input type="number" name="nominal" value="{{ old('nominal', $maintenance?->nominal) }}" min="0" step="0.01" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
        @error('nominal')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Jenis Biaya</label>
        <input type="text" name="jenis_biaya" value="{{ old('jenis_biaya', $maintenance?->jenis_biaya) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('jenis_biaya')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Dibayar Dari</label>
        <input type="text" name="dibayar_dari" value="{{ old('dibayar_dari', $maintenance?->dibayar_dari) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('dibayar_dari')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Akun Pembayaran <span class="text-red-600">*</span></label>
        <select name="cash_account_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Pilih Akun Pembayaran</option>
            @foreach($cashAccounts as $cashAccount)
                <option value="{{ $cashAccount->id }}" {{ (int) old('cash_account_id', $maintenance?->cash_account_id) === $cashAccount->id ? 'selected' : '' }}>
                    {{ $cashAccount->name }} - {{ $cashAccount->accountTypeLabel() }}
                </option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-gray-500">Akun kas yang digunakan untuk membayar biaya perawatan aset wakaf.</p>
        @error('cash_account_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Penanggung Jawab</label>
        <input type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab', $maintenance?->penanggung_jawab) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('penanggung_jawab')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Bukti File</label>
        <input type="file" name="bukti_file" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @if($maintenance?->bukti_file)
            <a href="{{ asset('storage/'.$maintenance->bukti_file) }}" target="_blank" class="mt-2 inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-900">Lihat file tersimpan</a>
        @endif
        @error('bukti_file')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Keterangan</label>
    <textarea name="keterangan" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('keterangan', $maintenance?->keterangan) }}</textarea>
    @error('keterangan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
