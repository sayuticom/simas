@php
    $statusValue = old('status', $pengumuman?->status ?? 'draft');
    $featuredImageUrl = $pengumuman?->featured_image ? asset('storage/' . $pengumuman->featured_image) : null;
@endphp

<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    <div>
        <label class="block text-sm font-semibold text-gray-700">Kegiatan</label>
        <select name="kegiatan_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Tidak terkait kegiatan</option>
            @foreach($kegiatans as $kegiatan)
                <option value="{{ $kegiatan->id }}" {{ (int) old('kegiatan_id', $pengumuman?->kegiatan_id) === $kegiatan->id ? 'selected' : '' }}>
                    {{ $kegiatan->nama_kegiatan }}
                </option>
            @endforeach
        </select>
        @error('kegiatan_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Status</label>
        <select name="status" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="draft" {{ $statusValue === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="terbit" {{ $statusValue === 'terbit' ? 'selected' : '' }}>Terbit</option>
            <option value="arsip" {{ $statusValue === 'arsip' ? 'selected' : '' }}>Arsip</option>
        </select>
        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="lg:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">Judul <span class="text-red-600">*</span></label>
        <input type="text" name="judul" value="{{ old('judul', $pengumuman?->judul) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
        @error('judul')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Slug Publik</label>
        <input type="text" name="slug" value="{{ old('slug', $pengumuman?->slug) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" placeholder="contoh-pengumuman">
        <p class="mt-1 text-xs font-semibold text-slate-600">Kosongkan untuk dibuat otomatis dari judul. Gunakan huruf kecil, angka, dan strip.</p>
        @error('slug')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Tanggal Publikasi</label>
        <input type="datetime-local" name="published_at" value="{{ old('published_at', $pengumuman?->published_at?->format('Y-m-d\TH:i')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        <p class="mt-1 text-xs font-semibold text-slate-600">Jika status Terbit dan dikosongkan, akan diisi otomatis saat disimpan.</p>
        @error('published_at')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Tanggal Mulai</label>
        <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $pengumuman?->tanggal_mulai?->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('tanggal_mulai')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Tanggal Selesai</label>
        <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $pengumuman?->tanggal_selesai?->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('tanggal_selesai')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Target Audiens</label>
        <input type="text" name="target_audiens" value="{{ old('target_audiens', $pengumuman?->target_audiens) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Jamaah umum, remaja masjid, pengurus">
        @error('target_audiens')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="flex items-end">
        <label class="inline-flex items-center gap-3 rounded-lg border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700">
            <input type="checkbox" name="tampil_di_dashboard" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ old('tampil_di_dashboard', $pengumuman?->tampil_di_dashboard) ? 'checked' : '' }}>
            Tampil di Dashboard
        </label>
        @error('tampil_di_dashboard')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Ringkasan Publik</label>
    <textarea name="excerpt" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ringkasan singkat yang tampil di daftar pengumuman publik.">{{ old('excerpt', $pengumuman?->excerpt) }}</textarea>
    @error('excerpt')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Gambar Utama Publik</label>
    <input type="file" name="featured_image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500">
    <p class="mt-1 text-xs font-semibold text-slate-600">Format JPG, JPEG, PNG, atau WEBP. Maksimal 4MB.</p>
    @error('featured_image')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror

    @if($featuredImageUrl)
        <div class="mt-4 max-w-md rounded-xl border border-slate-200 bg-white p-3">
            <img src="{{ $featuredImageUrl }}" alt="Preview gambar pengumuman" class="h-48 w-full rounded-lg object-cover">
            <p class="mt-2 truncate text-xs font-semibold text-slate-700">{{ $pengumuman->featured_image }}</p>
        </div>
    @endif
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Isi Pengumuman <span class="text-red-600">*</span></label>
    <textarea name="isi" rows="8" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('isi', $pengumuman?->isi) }}</textarea>
    @error('isi')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
