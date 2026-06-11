<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700">Wakif <span class="text-red-600">*</span></label>
        <select name="wakif_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Pilih wakif</option>
            @foreach($wakifs as $wakif)
                <option value="{{ $wakif->id }}" {{ (int) old('wakif_id', $wakafNonCash?->wakif_id) === $wakif->id ? 'selected' : '' }}>{{ $wakif->nama }}</option>
            @endforeach
        </select>
        @error('wakif_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Nazhir <span class="text-red-600">*</span></label>
        <select name="nazhir_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Pilih nazhir</option>
            @foreach($nazhirs as $nazhir)
                <option value="{{ $nazhir->id }}" {{ (int) old('nazhir_id', $wakafNonCash?->nazhir_id) === $nazhir->id ? 'selected' : '' }}>{{ $nazhir->nama }}</option>
            @endforeach
        </select>
        @error('nazhir_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Tanggal Terima <span class="text-red-600">*</span></label>
        <input type="date" name="tanggal_terima" value="{{ old('tanggal_terima', $wakafNonCash?->tanggal_terima?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
        @error('tanggal_terima')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Nama Aset <span class="text-red-600">*</span></label>
        <input type="text" name="nama_aset" value="{{ old('nama_aset', $wakafNonCash?->nama_aset) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
        @error('nama_aset')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Jenis Aset</label>
        <input type="text" name="jenis_aset" value="{{ old('jenis_aset', $wakafNonCash?->jenis_aset) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('jenis_aset')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Nilai Estimasi</label>
        <input type="number" name="nilai_estimasi" value="{{ old('nilai_estimasi', $wakafNonCash?->nilai_estimasi ?? 0) }}" min="0" step="0.01" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('nilai_estimasi')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Jumlah</label>
        <input type="number" name="jumlah" value="{{ old('jumlah', $wakafNonCash?->jumlah) }}" min="0" step="1" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('jumlah')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Luas</label>
        <input type="text" name="luas" value="{{ old('luas', $wakafNonCash?->luas) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('luas')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Nomor Sertifikat</label>
        <input type="text" name="nomor_sertifikat" value="{{ old('nomor_sertifikat', $wakafNonCash?->nomor_sertifikat) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('nomor_sertifikat')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Status Dokumen</label>
        <input type="text" name="status_dokumen" value="{{ old('status_dokumen', $wakafNonCash?->status_dokumen) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('status_dokumen')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Lokasi</label>
    <textarea name="lokasi" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('lokasi', $wakafNonCash?->lokasi) }}</textarea>
    @error('lokasi')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700">Dokumen Ikrar</label>
        <input type="file" name="dokumen_ikrar" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @if($wakafNonCash?->dokumen_ikrar)
            <a href="{{ asset('storage/'.$wakafNonCash->dokumen_ikrar) }}" target="_blank" class="mt-2 inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-900">Lihat file tersimpan</a>
        @endif
        @error('dokumen_ikrar')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Dokumen Aset</label>
        <input type="file" name="dokumen_aset" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @if($wakafNonCash?->dokumen_aset)
            <a href="{{ asset('storage/'.$wakafNonCash->dokumen_aset) }}" target="_blank" class="mt-2 inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-900">Lihat file tersimpan</a>
        @endif
        @error('dokumen_aset')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Foto</label>
        <input type="file" name="foto" accept="image/*" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @if($wakafNonCash?->foto)
            <a href="{{ asset('storage/'.$wakafNonCash->foto) }}" target="_blank" class="mt-2 inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-900">Lihat foto tersimpan</a>
        @endif
        @error('foto')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Keterangan</label>
    <textarea name="keterangan" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('keterangan', $wakafNonCash?->keterangan) }}</textarea>
    @error('keterangan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
