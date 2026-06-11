@php
    $statusValue = old('status', $kegiatan?->status ?? 'terencana');
    $statusPublikValue = old('status_publik', $kegiatan?->status_publik ?? 'draft');
    $tampilkanDiWebsite = old('tampilkan_di_website', $kegiatan?->tampilkan_di_website ?? false);
    $posterPublikUrl = $kegiatan?->poster_publik ? asset('storage/' . $kegiatan->poster_publik) : null;
    $jenisOptions = $jenisOptions ?? \App\Models\Kegiatan::jenisOptions();
    $jenisValue = old('jenis_kegiatan', $kegiatan?->jenis_kegiatan);
    if ($jenisValue && ! in_array($jenisValue, $jenisOptions, true)) {
        $jenisOptions[] = $jenisValue;
    }
@endphp

<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    <div>
        <label class="block text-sm font-semibold text-gray-700">Nama Kegiatan <span class="text-red-600">*</span></label>
        <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan', $kegiatan?->nama_kegiatan) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
        @error('nama_kegiatan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Jenis Kegiatan</label>
        <select name="jenis_kegiatan" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Pilih jenis kegiatan</option>
            @foreach($jenisOptions as $jenis)
                <option value="{{ $jenis }}" @selected($jenisValue === $jenis)>
                    {{ $jenis }}{{ $kegiatan?->jenis_kegiatan === $jenis && ! in_array($jenis, \App\Models\Kegiatan::jenisOptions(), true) ? ' (data lama)' : '' }}
                </option>
            @endforeach
        </select>
        @error('jenis_kegiatan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Tema Materi</label>
        <input type="text" name="tema_materi" value="{{ old('tema_materi', $kegiatan?->tema_materi) }}" placeholder="Contoh: Menjaga Keikhlasan dalam Beramal" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        <p class="mt-1 text-xs font-semibold text-slate-600">Isi tema materi kajian atau topik utama kegiatan. Kosongkan jika tidak ada.</p>
        @error('tema_materi')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Tanggal Mulai</label>
        <input type="datetime-local" name="tanggal_mulai" value="{{ old('tanggal_mulai', $kegiatan?->tanggal_mulai?->format('Y-m-d\TH:i')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('tanggal_mulai')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Tanggal Selesai</label>
        <input type="datetime-local" name="tanggal_selesai" value="{{ old('tanggal_selesai', $kegiatan?->tanggal_selesai?->format('Y-m-d\TH:i')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('tanggal_selesai')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Lokasi</label>
        <input type="text" name="lokasi" value="{{ old('lokasi', $kegiatan?->lokasi) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('lokasi')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Penanggung Jawab</label>
        <input type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab', $kegiatan?->penanggung_jawab) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('penanggung_jawab')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Narasumber</label>
        <input type="text" name="narasumber" value="{{ old('narasumber', $kegiatan?->narasumber) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('narasumber')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Target Peserta</label>
        <input type="text" name="target_peserta" value="{{ old('target_peserta', $kegiatan?->target_peserta) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('target_peserta')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Kontak Person</label>
        <input type="text" name="kontak_person" value="{{ old('kontak_person', $kegiatan?->kontak_person) }}" placeholder="Contoh: Ust. Ahmad" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('kontak_person')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Nomor WhatsApp/Kontak</label>
        <input type="text" name="nomor_kontak" value="{{ old('nomor_kontak', $kegiatan?->nomor_kontak) }}" placeholder="Contoh: 081234567890" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('nomor_kontak')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Label Kontak</label>
        <select name="label_kontak" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Pilih label kontak</option>
            @foreach(\App\Models\Kegiatan::labelKontakOptions() as $labelKontak)
                <option value="{{ $labelKontak }}" @selected(old('label_kontak', $kegiatan?->label_kontak) === $labelKontak)>{{ $labelKontak }}</option>
            @endforeach
        </select>
        @error('label_kontak')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Status</label>
        <select name="status" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="terencana" {{ $statusValue === 'terencana' ? 'selected' : '' }}>Terencana</option>
            <option value="berjalan" {{ $statusValue === 'berjalan' ? 'selected' : '' }}>Berjalan</option>
            <option value="selesai" {{ $statusValue === 'selesai' ? 'selected' : '' }}>Selesai</option>
            <option value="batal" {{ $statusValue === 'batal' ? 'selected' : '' }}>Batal</option>
        </select>
        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Deskripsi</label>
    <textarea name="deskripsi" rows="4" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('deskripsi', $kegiatan?->deskripsi) }}</textarea>
    @error('deskripsi')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<section class="rounded-2xl border border-emerald-100 bg-emerald-50/70 p-5">
    <div class="mb-5">
        <p class="text-xs font-bold uppercase tracking-[0.22em] text-amber-700">Website Publik</p>
        <h3 class="mt-1 text-lg font-black text-slate-950">Pengaturan Website Publik</h3>
        <p class="mt-1 text-sm font-semibold text-slate-700">Data publik bersifat opsional. Jika tidak dicentang, kegiatan tidak muncul di website publik walaupun status publik tayang.</p>
    </div>

    <div class="space-y-5">
        <label class="inline-flex items-center gap-3 rounded-xl border border-emerald-200 bg-white px-4 py-3 font-bold text-slate-800">
            <input type="checkbox" name="tampilkan_di_website" value="1" class="h-5 w-5 rounded border-slate-300 text-emerald-700 focus:ring-emerald-700" @checked((bool) $tampilkanDiWebsite)>
            Tampilkan di Website
        </label>
        @error('tampilkan_di_website')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div>
                <label class="block text-sm font-semibold text-gray-700">Status Publik</label>
                <select name="status_publik" class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="draft" {{ $statusPublikValue === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="tayang" {{ $statusPublikValue === 'tayang' ? 'selected' : '' }}>Tayang</option>
                    <option value="arsip" {{ $statusPublikValue === 'arsip' ? 'selected' : '' }}>Arsip</option>
                </select>
                @error('status_publik')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700">Deskripsi Publik</label>
            <textarea name="deskripsi_publik" rows="4" class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('deskripsi_publik', $kegiatan?->deskripsi_publik) }}</textarea>
            <p class="mt-1 text-xs font-semibold text-slate-600">Opsional. Isi jika deskripsi yang tampil di website ingin dibuat lebih singkat atau lebih rapi. Jika kosong, website memakai Deskripsi kegiatan.</p>
            @error('deskripsi_publik')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700">Poster Publik</label>
            <input type="file" name="poster_publik" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm">
            <p class="mt-1 text-xs font-semibold text-slate-700">Rekomendasi poster 1080x1080 px, rasio 1:1. Cocok untuk website, Instagram, Facebook, dan WhatsApp. Format JPG, JPEG, PNG, atau WEBP. Maksimal 4MB.</p>
            @error('poster_publik')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror

            @if($posterPublikUrl)
                <div class="mt-4 max-w-md rounded-xl border border-emerald-200 bg-white p-3">
                    <img src="{{ $posterPublikUrl }}" alt="Preview poster publik" class="h-48 w-full rounded-lg object-cover">
                    <p class="mt-2 truncate text-xs font-semibold text-slate-700">{{ $kegiatan->poster_publik }}</p>
                </div>
            @endif
        </div>
    </div>
</section>
