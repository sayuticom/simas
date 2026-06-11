<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="lg:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">Aset Wakaf <span class="text-red-600">*</span></label>
        <select name="waqf_asset_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Pilih Aset Wakaf Produktif</option>
            @foreach($assets as $asset)
                <option value="{{ $asset->id }}" {{ (int) old('waqf_asset_id', $productiveAsset?->waqf_asset_id) === $asset->id ? 'selected' : '' }}>
                    {{ $asset->nama_aset }} - {{ $asset->jenis_aset ?: 'Aset Wakaf' }} - Rp {{ number_format((float) $asset->nilai_estimasi, 0, ',', '.') }}
                </option>
            @endforeach
        </select>
        @if($assets->isEmpty())
            <p class="mt-1 text-sm font-semibold text-amber-700">Belum ada Aset Wakaf Produktif. Silakan tandai aset sebagai produktif di menu Aset Wakaf terlebih dahulu.</p>
        @endif
        @error('waqf_asset_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Jenis Pengelolaan</label>
        <input type="text" name="jenis_pengelolaan" value="{{ old('jenis_pengelolaan', $productiveAsset?->jenis_pengelolaan) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('jenis_pengelolaan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Penyewa / Mitra</label>
        <input type="text" name="nama_penyewa_atau_mitra" value="{{ old('nama_penyewa_atau_mitra', $productiveAsset?->nama_penyewa_atau_mitra) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('nama_penyewa_atau_mitra')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Tanggal Mulai Kontrak</label>
        <input type="date" name="tanggal_mulai_kontrak" value="{{ old('tanggal_mulai_kontrak', $productiveAsset?->tanggal_mulai_kontrak?->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('tanggal_mulai_kontrak')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Tanggal Selesai Kontrak</label>
        <input type="date" name="tanggal_selesai_kontrak" value="{{ old('tanggal_selesai_kontrak', $productiveAsset?->tanggal_selesai_kontrak?->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('tanggal_selesai_kontrak')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Target Pendapatan</label>
        <input type="number" name="target_pendapatan" value="{{ old('target_pendapatan', $productiveAsset?->target_pendapatan) }}" min="0" step="0.01" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('target_pendapatan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Periode Pendapatan</label>
        <input type="text" name="periode_pendapatan" value="{{ old('periode_pendapatan', $productiveAsset?->periode_pendapatan) }}" placeholder="Bulanan, tahunan, panen, atau lainnya" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('periode_pendapatan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Status</label>
        <select name="status" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Pilih status</option>
            <option value="aktif" {{ old('status', $productiveAsset?->status ?? 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ old('status', $productiveAsset?->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            <option value="selesai" {{ old('status', $productiveAsset?->status) === 'selesai' ? 'selected' : '' }}>Selesai</option>
        </select>
        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Keterangan</label>
    <textarea name="keterangan" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('keterangan', $productiveAsset?->keterangan) }}</textarea>
    @error('keterangan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
