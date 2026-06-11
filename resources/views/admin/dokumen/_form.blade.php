@php
    $statusValue = old('status', $dokumen?->status ?? 'aktif');
@endphp

<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    <div class="lg:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">Judul <span class="text-red-600">*</span></label>
        <input type="text" name="judul" value="{{ old('judul', $dokumen?->judul) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
        @error('judul')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Jenis Dokumen</label>
        <input type="text" name="jenis_dokumen" value="{{ old('jenis_dokumen', $dokumen?->jenis_dokumen) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('jenis_dokumen')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Nomor Dokumen</label>
        <input type="text" name="nomor_dokumen" value="{{ old('nomor_dokumen', $dokumen?->nomor_dokumen) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('nomor_dokumen')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Tanggal Dokumen</label>
        <input type="date" name="tanggal_dokumen" value="{{ old('tanggal_dokumen', $dokumen?->tanggal_dokumen?->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('tanggal_dokumen')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Tanggal Berakhir</label>
        <input type="date" name="tanggal_berakhir" value="{{ old('tanggal_berakhir', $dokumen?->tanggal_berakhir?->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('tanggal_berakhir')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Sumber</label>
        <input type="text" name="sumber" value="{{ old('sumber', $dokumen?->sumber) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('sumber')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Status</label>
        <select name="status" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="aktif" {{ $statusValue === 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="arsip" {{ $statusValue === 'arsip' ? 'selected' : '' }}>Arsip</option>
            <option value="kedaluwarsa" {{ $statusValue === 'kedaluwarsa' ? 'selected' : '' }}>Kedaluwarsa</option>
        </select>
        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="lg:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">File Dokumen</label>
        <input type="file" name="file_dokumen" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @if($dokumen?->file_dokumen)
            <a href="{{ asset('storage/'.$dokumen->file_dokumen) }}" target="_blank" class="mt-2 inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-900">Lihat file tersimpan</a>
        @endif
        @error('file_dokumen')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Keterangan</label>
    <textarea name="keterangan" rows="4" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('keterangan', $dokumen?->keterangan) }}</textarea>
    @error('keterangan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
