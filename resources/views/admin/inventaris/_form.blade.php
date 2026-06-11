@php
    $conditionValue = old('kondisi', $inventaris?->kondisi ?? 'baik');
    $statusValue = old('status', $inventaris?->status ?? 'aktif');
@endphp

<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    <div>
        <label class="block text-sm font-semibold text-gray-700">Kode Barang</label>
        <input type="text" name="kode_barang" value="{{ old('kode_barang', $inventaris?->kode_barang) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('kode_barang')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Nama Barang <span class="text-red-600">*</span></label>
        <input type="text" name="nama_barang" value="{{ old('nama_barang', $inventaris?->nama_barang) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
        @error('nama_barang')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Kategori</label>
        <input type="text" name="kategori" value="{{ old('kategori', $inventaris?->kategori) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('kategori')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Merk</label>
        <input type="text" name="merk" value="{{ old('merk', $inventaris?->merk) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('merk')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Tipe/Model</label>
        <input type="text" name="tipe_model" value="{{ old('tipe_model', $inventaris?->tipe_model) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('tipe_model')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700">Jumlah <span class="text-red-600">*</span></label>
            <input type="number" name="jumlah" value="{{ old('jumlah', $inventaris?->jumlah ?? 1) }}" min="1" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
            @error('jumlah')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700">Satuan</label>
            <input type="text" name="satuan" value="{{ old('satuan', $inventaris?->satuan) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            @error('satuan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Kondisi</label>
        <select name="kondisi" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="baik" {{ $conditionValue === 'baik' ? 'selected' : '' }}>Baik</option>
            <option value="rusak_ringan" {{ $conditionValue === 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
            <option value="rusak_berat" {{ $conditionValue === 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
            <option value="hilang" {{ $conditionValue === 'hilang' ? 'selected' : '' }}>Hilang</option>
        </select>
        @error('kondisi')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Status</label>
        <select name="status" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="aktif" {{ $statusValue === 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ $statusValue === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            <option value="dipinjam" {{ $statusValue === 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
            <option value="hilang" {{ $statusValue === 'hilang' ? 'selected' : '' }}>Hilang</option>
            <option value="dihapus" {{ $statusValue === 'dihapus' ? 'selected' : '' }}>Dihapus</option>
        </select>
        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Lokasi</label>
        <input type="text" name="lokasi" value="{{ old('lokasi', $inventaris?->lokasi) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('lokasi')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Tanggal Perolehan</label>
        <input type="date" name="tanggal_perolehan" value="{{ old('tanggal_perolehan', $inventaris?->tanggal_perolehan?->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('tanggal_perolehan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Sumber Perolehan</label>
        <input type="text" name="sumber_perolehan" value="{{ old('sumber_perolehan', $inventaris?->sumber_perolehan) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('sumber_perolehan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Nilai Perolehan</label>
        <input type="number" name="nilai_perolehan" value="{{ old('nilai_perolehan', $inventaris?->nilai_perolehan ?? 0) }}" min="0" step="0.01" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('nilai_perolehan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Penanggung Jawab</label>
        <input type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab', $inventaris?->penanggung_jawab) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('penanggung_jawab')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Foto</label>
        <input type="file" name="foto" accept="image/*" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @if($inventaris?->foto)
            <a href="{{ asset('storage/'.$inventaris->foto) }}" target="_blank" class="mt-2 inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-900">Lihat foto tersimpan</a>
        @endif
        @error('foto')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Keterangan</label>
    <textarea name="keterangan" rows="4" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('keterangan', $inventaris?->keterangan) }}</textarea>
    @error('keterangan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
