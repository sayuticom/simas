@php
    $sourceValue = old('sumber_wakaf', $asset?->sumber_wakaf ?? 'lainnya');
@endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700">Sumber Wakaf <span class="text-red-600">*</span></label>
        <select id="sumber-wakaf" name="sumber_wakaf" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="wakaf_tunai" {{ $sourceValue === 'wakaf_tunai' ? 'selected' : '' }}>Wakaf Tunai</option>
            <option value="wakaf_non_tunai" {{ $sourceValue === 'wakaf_non_tunai' ? 'selected' : '' }}>Wakaf Non-Tunai</option>
            <option value="lainnya" {{ $sourceValue === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
        </select>
        @error('sumber_wakaf')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Nazhir <span class="text-red-600">*</span></label>
        <select name="nazhir_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Pilih nazhir</option>
            @foreach($nazhirs as $nazhir)
                <option value="{{ $nazhir->id }}" {{ (int) old('nazhir_id', $asset?->nazhir_id) === $nazhir->id ? 'selected' : '' }}>{{ $nazhir->nama }}</option>
            @endforeach
        </select>
        @error('nazhir_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div id="wakaf-tunai-field">
        <label class="block text-sm font-semibold text-gray-700">Data Wakaf Tunai</label>
        <select name="wakaf_tunai_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Pilih Wakaf Tunai</option>
            @foreach($wakafCashes as $wakafCash)
                <option value="{{ $wakafCash->id }}" {{ (int) old('wakaf_tunai_id', $asset?->wakaf_tunai_id) === $wakafCash->id ? 'selected' : '' }}>
                    {{ $wakafCash->tanggal_terima?->format('d-m-Y') }} - {{ $wakafCash->wakif?->nama ?? 'Wakif' }} - Rp {{ number_format((float) $wakafCash->nominal, 0, ',', '.') }}
                </option>
            @endforeach
        </select>
        @error('wakaf_tunai_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div id="wakaf-non-tunai-field">
        <label class="block text-sm font-semibold text-gray-700">Data Wakaf Non-Tunai</label>
        <select name="wakaf_non_tunai_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Pilih Wakaf Non-Tunai</option>
            @foreach($wakafNonCashes as $wakafNonCash)
                <option value="{{ $wakafNonCash->id }}" {{ (int) old('wakaf_non_tunai_id', $asset?->wakaf_non_tunai_id) === $wakafNonCash->id ? 'selected' : '' }}>
                    {{ $wakafNonCash->tanggal_terima?->format('d-m-Y') }} - {{ $wakafNonCash->nama_aset }} - {{ $wakafNonCash->wakif?->nama ?? 'Wakif' }}
                </option>
            @endforeach
        </select>
        @error('wakaf_non_tunai_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Nama Aset <span class="text-red-600">*</span></label>
        <input type="text" name="nama_aset" value="{{ old('nama_aset', $asset?->nama_aset) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
        @error('nama_aset')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Jenis Aset</label>
        <input type="text" name="jenis_aset" value="{{ old('jenis_aset', $asset?->jenis_aset) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('jenis_aset')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Nilai Estimasi</label>
        <input type="number" name="nilai_estimasi" value="{{ old('nilai_estimasi', $asset?->nilai_estimasi) }}" min="0" step="0.01" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('nilai_estimasi')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Kondisi</label>
        <input type="text" name="kondisi" value="{{ old('kondisi', $asset?->kondisi) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('kondisi')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Status Hukum</label>
        <input type="text" name="status_hukum" value="{{ old('status_hukum', $asset?->status_hukum) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('status_hukum')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Status Pemanfaatan</label>
        <input type="text" name="status_pemanfaatan" value="{{ old('status_pemanfaatan', $asset?->status_pemanfaatan) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('status_pemanfaatan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Lokasi</label>
    <textarea name="lokasi" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('lokasi', $asset?->lokasi) }}</textarea>
    @error('lokasi')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
    <label class="inline-flex items-center gap-3 text-sm font-semibold text-gray-700">
        <input type="checkbox" name="produktif" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ old('produktif', $asset?->produktif) ? 'checked' : '' }}>
        Aset produktif
    </label>
    <p class="mt-1 text-xs text-gray-500">Aktifkan jika aset ini digunakan sebagai aset wakaf produktif.</p>
    @error('produktif')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Keterangan</label>
    <textarea name="keterangan" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('keterangan', $asset?->keterangan) }}</textarea>
    @error('keterangan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const source = document.getElementById('sumber-wakaf');
        const cashField = document.getElementById('wakaf-tunai-field');
        const nonCashField = document.getElementById('wakaf-non-tunai-field');

        const syncSourceFields = () => {
            cashField.classList.toggle('hidden', source.value !== 'wakaf_tunai');
            nonCashField.classList.toggle('hidden', source.value !== 'wakaf_non_tunai');
        };

        source.addEventListener('change', syncSourceFields);
        syncSourceFields();
    });
</script>
