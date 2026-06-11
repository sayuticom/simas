<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="lg:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">Aset Produktif <span class="text-red-600">*</span></label>
        <select name="productive_waqf_asset_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Pilih Aset Produktif</option>
            @foreach($productiveAssets as $productiveAsset)
                <option value="{{ $productiveAsset->id }}" {{ (int) old('productive_waqf_asset_id', $result?->productive_waqf_asset_id) === $productiveAsset->id ? 'selected' : '' }}>
                    {{ $productiveAsset->wakafAsset?->nama_aset ?? 'Aset Produktif #'.$productiveAsset->id }}
                    @if($productiveAsset->jenis_pengelolaan)
                        - {{ $productiveAsset->jenis_pengelolaan }}
                    @endif
                </option>
            @endforeach
        </select>
        @if($productiveAssets->isEmpty())
            <p class="mt-1 text-sm font-semibold text-amber-700">Belum ada Aset Produktif Wakaf. Silakan buat Aset Produktif terlebih dahulu.</p>
        @endif
        @error('productive_waqf_asset_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Tanggal Penerimaan <span class="text-red-600">*</span></label>
        <input type="date" name="tanggal_penerimaan" value="{{ old('tanggal_penerimaan', $result?->tanggal_penerimaan?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
        @error('tanggal_penerimaan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Nominal <span class="text-red-600">*</span></label>
        <input type="number" name="nominal" value="{{ old('nominal', $result?->nominal) }}" min="0" step="0.01" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
        @error('nominal')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Jenis Hasil</label>
        <input type="text" name="jenis_hasil" value="{{ old('jenis_hasil', $result?->jenis_hasil) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('jenis_hasil')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Periode</label>
        <input type="text" name="periode" value="{{ old('periode', $result?->periode) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('periode')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Nama Pembayar</label>
        <input type="text" name="nama_pembayar" value="{{ old('nama_pembayar', $result?->nama_pembayar) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('nama_pembayar')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Masuk ke Kas Masjid <span class="text-red-600">*</span></label>
        <select id="masuk-ke-kas" name="masuk_ke_kas_masjid" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="Tidak" {{ old('masuk_ke_kas_masjid', $result?->masuk_ke_kas_masjid ?? 'Tidak') === 'Tidak' ? 'selected' : '' }}>Tidak</option>
            <option value="Ya" {{ old('masuk_ke_kas_masjid', $result?->masuk_ke_kas_masjid) === 'Ya' ? 'selected' : '' }}>Ya</option>
        </select>
        @error('masuk_ke_kas_masjid')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div id="cash-account-wrapper">
        <label class="block text-sm font-semibold text-gray-700">Akun Penerimaan Dana</label>
        <select name="cash_account_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Pilih Akun Penerimaan Dana</option>
            @foreach($cashAccounts as $cashAccount)
                <option value="{{ $cashAccount->id }}" {{ (int) old('cash_account_id', $result?->cash_account_id) === $cashAccount->id ? 'selected' : '' }}>
                    {{ $cashAccount->name }} - {{ $cashAccount->accountTypeLabel() }}
                </option>
            @endforeach
        </select>
        <p id="cash-account-help" class="mt-1 text-xs text-gray-500">Dipilih jika hasil kelola wakaf dimasukkan ke kas masjid.</p>
        @error('cash_account_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Bukti File</label>
        <input type="file" name="bukti_file" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @if($result?->bukti_file)
            <a href="{{ asset('storage/'.$result->bukti_file) }}" target="_blank" class="mt-2 inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-900">Lihat file tersimpan</a>
        @endif
        @error('bukti_file')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const masukKas = document.getElementById('masuk-ke-kas');
        const cashWrapper = document.getElementById('cash-account-wrapper');
        const cashHelp = document.getElementById('cash-account-help');

        const syncCashAccount = () => {
            const isKas = masukKas.value === 'Ya';
            cashWrapper.classList.toggle('hidden', !isKas);
            cashHelp.textContent = isKas
                ? 'Dipilih jika hasil kelola wakaf dimasukkan ke kas masjid.'
                : 'Akun penerimaan dana tidak diperlukan jika hasil kelola tidak masuk kas masjid.';
        };

        masukKas.addEventListener('change', syncCashAccount);
        syncCashAccount();
    });
</script>

<div>
    <label class="block text-sm font-semibold text-gray-700">Keterangan</label>
    <textarea name="keterangan" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('keterangan', $result?->keterangan) }}</textarea>
    @error('keterangan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
