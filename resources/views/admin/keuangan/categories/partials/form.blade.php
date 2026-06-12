<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700">Nama Kategori</label>
        <input type="text" name="name" value="{{ old('name', $category?->name) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700">Tipe Transaksi</label>
        <input type="hidden" name="type" value="{{ old('type', $category?->type ?? 'keluar') }}">
        <p class="mt-2 text-sm text-gray-600">Keluar (kategori pengeluaran operasional)</p>
        @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Keterangan</label>
    <textarea name="description" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $category?->description) }}</textarea>
    @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<label class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category?->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
    Aktif
</label>
