@extends('layouts.admin')

@section('title', 'Profil Masjid - SIMAS')
@section('page_title', 'Profil Masjid')

@section('content')
@php
    use Illuminate\Support\Str;
@endphp
<div class="grid grid-cols-1 xl:grid-cols-[320px_1fr] gap-6">
    <aside class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col items-center text-center">
            <div class="w-32 h-32 rounded-full overflow-hidden border border-gray-200 mb-4 bg-gray-100">
                @if ($profile->logo)
                    <img src="{{ asset('storage/' . $profile->logo) }}" alt="Logo Masjid" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        <i class="fas fa-mosque text-4xl"></i>
                    </div>
                @endif
            </div>

            <h2 class="text-xl font-bold text-gray-800 mb-2">{{ $profile->nama_masjid ?: ($activeMosque?->name ?? 'Belum Ada Nama Masjid') }}</h2>
            <p class="text-gray-600 text-sm mb-4">{{ $profile->kota ?: ($activeMosque?->address ? Str::limit($activeMosque->address, 60) : 'Belum ada lokasi') }}</p>

            <div class="w-full text-left space-y-3">
                <div>
                    <p class="text-xs uppercase text-gray-500 tracking-[.16em]">Ketua DKM</p>
                    <p class="text-gray-800 font-semibold">{{ $profile->nama_ketua_dkm ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-500 tracking-[.16em]">Bendahara</p>
                    <p class="text-gray-800 font-semibold">{{ $profile->nama_bendahara ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-500 tracking-[.16em]">Sekretaris</p>
                    <p class="text-gray-800 font-semibold">{{ $profile->nama_sekretaris ?: '-' }}</p>
                </div>
            </div>
            
            <!-- Gallery dipindahkan ke bawah form agar tampilan sidebar tetap bersih -->        </div>
    </aside>

    <section class="bg-white rounded-lg shadow p-6">
        @if (session('success'))
            <div class="mb-6 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3">
                {{ session('error') }}
            </div>
        @endif

        @if(isset($availableMosques) && $availableMosques->count() > 0)
            <div class="mb-6 rounded-2xl border border-emerald-100 bg-emerald-50 p-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-amber-700">Masjid Aktif</p>
                        <h3 class="mt-1 text-xl font-black text-slate-950">{{ $activeMosque?->name ?? 'Belum ada masjid yang dipilih' }}</h3>
                        <p class="mt-1 text-sm text-slate-600">Pilih masjid aktif di sini untuk menentukan konteks data SIMAS.</p>
                    </div>
                    @if($availableMosques->count() > 1 || ! $activeMosque)
                        <form action="{{ route('mosque.switch') }}" method="POST" class="flex w-full flex-col gap-2 sm:flex-row lg:w-auto">
                            @csrf
                            <input type="hidden" name="redirect_to" value="profile">
                            <select name="mosque_id" class="w-full rounded-xl border border-emerald-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 focus:border-emerald-600 focus:ring-emerald-600 sm:w-72" required>
                                <option value="">Pilih masjid</option>
                                @foreach($availableMosques as $mosque)
                                    <option value="{{ $mosque->id }}" @selected((int) $activeMosque?->id === (int) $mosque->id)>
                                        {{ $mosque->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="rounded-xl bg-emerald-950 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-900">
                                Terapkan
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Nama Masjid</label>
                    <input type="text" name="nama_masjid" value="{{ old('nama_masjid', $profile->nama_masjid ?: ($activeMosque?->name ?? '')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
                    @error('nama_masjid')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email', $profile->email ?: ($activeMosque?->email ?? '')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Alamat</label>
                <textarea name="alamat" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('alamat', $profile->alamat ?: ($activeMosque?->address ?? '')) }}</textarea>
                @error('alamat')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Kelurahan</label>
                    <input type="text" name="kelurahan" value="{{ old('kelurahan', $profile->kelurahan ?: ($activeMosque?->kelurahan ?? '')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('kelurahan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Kecamatan</label>
                    <input type="text" name="kecamatan" value="{{ old('kecamatan', $profile->kecamatan ?: ($activeMosque?->kecamatan ?? '')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('kecamatan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Kota</label>
                    <input type="text" name="kota" value="{{ old('kota', $profile->kota ?: ($activeMosque?->kota ?? '')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('kota')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Provinsi</label>
                    <input type="text" name="provinsi" value="{{ old('provinsi', $profile->provinsi ?: ($activeMosque?->provinsi ?? '')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('provinsi')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Kode Pos</label>
                    <input type="text" name="kode_pos" value="{{ old('kode_pos', $profile->kode_pos ?: ($activeMosque?->kode_pos ?? '')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('kode_pos')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">No. Telepon</label>
                    <input type="text" name="no_telepon" value="{{ old('no_telepon', $profile->no_telepon ?: ($activeMosque?->phone ?? '')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('no_telepon')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Website</label>
                    <input type="url" name="website" value="{{ old('website', $profile->website ?: ($activeMosque?->website ?? '')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('website')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Logo Masjid</label>
                    <input type="file" name="logo" accept="image/*" class="mt-2 w-full text-sm text-gray-700" />
                    @error('logo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Nama Ketua DKM</label>
                    <input type="text" name="nama_ketua_dkm" value="{{ old('nama_ketua_dkm', $profile->nama_ketua_dkm ?: ($activeMosque?->nama_ketua_dkm ?? '')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('nama_ketua_dkm')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Nama Bendahara</label>
                    <input type="text" name="nama_bendahara" value="{{ old('nama_bendahara', $profile->nama_bendahara ?: ($activeMosque?->nama_bendahara ?? '')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('nama_bendahara')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Nama Sekretaris</label>
                    <input type="text" name="nama_sekretaris" value="{{ old('nama_sekretaris', $profile->nama_sekretaris ?: ($activeMosque?->nama_sekretaris ?? '')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('nama_sekretaris')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Deskripsi Singkat</label>
                <textarea name="deskripsi_singkat" rows="4" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('deskripsi_singkat', $profile->deskripsi_singkat ?: ($activeMosque?->notes ?? '')) }}</textarea>
                @error('deskripsi_singkat')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition">Simpan Profil</button>
            </div>
        </form>

        <!-- Galeri Foto Masjid -->
        <div class="mt-8 border-t border-gray-200 pt-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-5">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Galeri Foto Masjid</h3>
                    <p class="text-sm text-gray-500 mt-1">Dokumentasi foto kegiatan, bangunan, dan suasana masjid.</p>
                </div>

                <form action="{{ route('profile.photo.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-2 lg:items-center">
                    @csrf
                    <input 
                        type="file" 
                        name="photos[]" 
                        multiple 
                        accept="image/*" 
                        class="w-full sm:w-72 text-sm text-gray-700 border border-gray-300 rounded-lg p-2 bg-white"
                    />

                    <button 
                        type="submit" 
                        class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition whitespace-nowrap"
                    >
                        Unggah Foto
                    </button>
                </form>
            </div>

            @if(isset($profile) && $profile->mosque && $profile->mosque->photos()->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 2xl:grid-cols-4 gap-4">
                    @foreach($profile->mosque->photos()->latest()->limit(12)->get() as $photo)
                        <div class="group relative aspect-video bg-gray-100 rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                            <img 
                                src="{{ asset('storage/' . $photo->path) }}" 
                                alt="Foto masjid" 
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                            >

                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition"></div>

                            @if($photo->is_featured)
                                <span class="absolute top-3 left-3 bg-green-600 text-white text-xs px-2 py-1 rounded">Tampak Depan</span>
                            @endif

                            <div class="absolute top-3 right-3 flex gap-2">
                                <form action="{{ route('profile.photo.feature', $photo) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-indigo-600 text-white rounded px-3 py-1 text-xs">{{ $photo->is_featured ? 'Unset' : 'Set' }}</button>
                                </form>

                                <form action="{{ route('profile.photo.destroy', $photo) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white rounded px-3 py-1 text-xs">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-white border border-gray-200 text-gray-400">
                        <i class="fas fa-images text-xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-700">Belum ada foto</h4>
                    <p class="text-sm text-gray-500 mt-1">Unggah foto pertama untuk menampilkan galeri masjid.</p>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
