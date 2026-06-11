@php
    $statusValue = old('status', $post?->status ?? 'draft');
    $typeValue = old('type', $post?->type ?? 'berita');
    $featuredImageUrl = $post?->featured_image ? asset('storage/' . $post->featured_image) : null;
@endphp

<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    <div>
        <label class="block text-sm font-semibold text-gray-700">Jenis Konten <span class="text-red-600">*</span></label>
        <select name="type" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
            @foreach($typeOptions as $value => $label)
                <option value="{{ $value }}" @selected($typeValue === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Status <span class="text-red-600">*</span></label>
        <select name="status" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
            @foreach($statusOptions as $value => $label)
                <option value="{{ $value }}" @selected($statusValue === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="lg:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">Judul <span class="text-red-600">*</span></label>
        <input type="text" name="title" value="{{ old('title', $post?->title) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
        @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Slug Publik</label>
        <input type="text" name="slug" value="{{ old('slug', $post?->slug) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" placeholder="judul-konten">
        <p class="mt-1 text-xs font-semibold text-slate-600">Kosongkan untuk dibuat otomatis dari judul. Gunakan huruf kecil, angka, dan strip.</p>
        @error('slug')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Tanggal Publikasi</label>
        <input type="datetime-local" name="published_at" value="{{ old('published_at', $post?->published_at?->format('Y-m-d\TH:i')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        <p class="mt-1 text-xs font-semibold text-slate-600">Jika status Published dan dikosongkan, akan diisi otomatis saat disimpan.</p>
        @error('published_at')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="flex items-end">
        <label class="inline-flex items-center gap-3 rounded-lg border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700">
            <input type="checkbox" name="is_featured" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ old('is_featured', $post?->is_featured) ? 'checked' : '' }}>
            Konten Unggulan
        </label>
        @error('is_featured')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Ringkasan</label>
    <textarea name="excerpt" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" maxlength="500" placeholder="Ringkasan singkat maksimal 500 karakter.">{{ old('excerpt', $post?->excerpt) }}</textarea>
    @error('excerpt')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Gambar Utama</label>
    <input type="file" name="featured_image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500">
    <p class="mt-1 text-xs font-semibold text-slate-600">Format JPG, JPEG, PNG, atau WEBP. Maksimal 4MB.</p>
    @error('featured_image')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror

    @if($featuredImageUrl)
        <div class="mt-4 max-w-md rounded-xl border border-slate-200 bg-white p-3">
            <img src="{{ $featuredImageUrl }}" alt="Preview gambar konten" class="h-48 w-full rounded-lg object-cover">
            <p class="mt-2 truncate text-xs font-semibold text-slate-700">{{ $post->featured_image }}</p>
        </div>
    @endif
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Isi Konten <span class="text-red-600">*</span></label>
    <textarea name="content" rows="10" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('content', $post?->content) }}</textarea>
    <p class="mt-1 text-xs font-semibold text-slate-600">Konten publik akan ditampilkan sebagai teks aman. HTML mentah tidak dirender.</p>
    @error('content')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
