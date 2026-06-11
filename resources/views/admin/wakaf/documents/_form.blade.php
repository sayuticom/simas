<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="lg:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">Aset Wakaf <span class="text-red-600">*</span></label>
        <select name="waqf_asset_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Pilih Aset Wakaf</option>
            @foreach($assets as $asset)
                <option value="{{ $asset->id }}" {{ (int) old('waqf_asset_id', $document?->waqf_asset_id) === $asset->id ? 'selected' : '' }}>
                    {{ $asset->nama_aset }} - {{ $asset->jenis_aset ?: 'Aset Wakaf' }}
                </option>
            @endforeach
        </select>
        @error('waqf_asset_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Jenis Dokumen</label>
        <input type="text" name="jenis_dokumen" value="{{ old('jenis_dokumen', $document?->jenis_dokumen) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('jenis_dokumen')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Nomor Dokumen</label>
        <input type="text" name="nomor_dokumen" value="{{ old('nomor_dokumen', $document?->nomor_dokumen) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('nomor_dokumen')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Tanggal Terbit</label>
        <input type="date" name="tanggal_terbit" value="{{ old('tanggal_terbit', $document?->tanggal_terbit?->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('tanggal_terbit')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Tanggal Berakhir</label>
        <input type="date" name="tanggal_berakhir" value="{{ old('tanggal_berakhir', $document?->tanggal_berakhir?->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('tanggal_berakhir')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="lg:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">File Dokumen</label>
        <input type="file" name="file_dokumen" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @if($document?->file_dokumen)
            <a href="{{ asset('storage/'.$document->file_dokumen) }}" target="_blank" class="mt-2 inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-900">Lihat file tersimpan</a>
        @endif
        @error('file_dokumen')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Keterangan</label>
    <textarea name="keterangan" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('keterangan', $document?->keterangan) }}</textarea>
    @error('keterangan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
