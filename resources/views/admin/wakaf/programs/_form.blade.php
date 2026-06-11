<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700">Nama Program <span class="text-red-600">*</span></label>
        <input type="text" name="nama" value="{{ old('nama', $program?->nama) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
        @error('nama')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Target Dana</label>
        <input type="number" name="target_dana" value="{{ old('target_dana', $program?->target_dana ?? 0) }}" min="0" step="0.01" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('target_dana')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Status</label>
        <select name="status" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Pilih status</option>
            <option value="aktif" {{ old('status', $program?->status ?? 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ old('status', $program?->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            <option value="selesai" {{ old('status', $program?->status) === 'selesai' ? 'selected' : '' }}>Selesai</option>
        </select>
        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Tujuan</label>
    <textarea name="tujuan" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('tujuan', $program?->tujuan) }}</textarea>
    @error('tujuan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Deskripsi</label>
    <textarea name="deskripsi" rows="4" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('deskripsi', $program?->deskripsi) }}</textarea>
    @error('deskripsi')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
