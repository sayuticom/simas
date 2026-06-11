@extends('layouts.admin')

@section('title', 'Pengaturan Website Masjid - SIMAS')
@section('page_title', 'Pengaturan Website Masjid')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
            <p class="font-semibold">Periksa kembali input berikut:</p>
            <ul class="mt-2 list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="rounded-lg bg-white p-6 shadow">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.22em] text-amber-700">Website Masjid</p>
                <h2 class="mt-1 text-2xl font-black text-slate-950">{{ $activeMosque?->name ?? 'Belum ada masjid aktif' }}</h2>
                <p class="mt-1 text-sm font-medium text-slate-600">Pengaturan ini menentukan website publik berbasis subdomain.</p>
            </div>

            @if(isset($availableMosques) && ($availableMosques->count() > 1 || ! $activeMosque))
                <form action="{{ route('mosque.switch') }}" method="POST" class="flex w-full flex-col gap-2 sm:flex-row lg:w-auto">
                    @csrf
                    <input type="hidden" name="redirect_to" value="website-settings.edit">
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

        @if(! $activeMosque)
            <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-5 text-amber-900">
                Pilih masjid aktif terlebih dahulu untuk mengatur website publik.
            </div>
        @else
            @if(! $canUpdate)
                <div class="mt-6 rounded-xl border border-blue-200 bg-blue-50 p-5 text-blue-900">
                    Akun Anda hanya dapat melihat pengaturan website. Perubahan hanya dapat disimpan oleh superuser atau admin masjid.
                </div>
            @endif

            @php
                $subdomainValue = old('subdomain', $websiteSetting?->subdomain);
                $statusValue = old('status_website', $websiteSetting?->status_website ?? 'draft');
                $previewUrl = $subdomainValue ? 'http://' . $subdomainValue . '.' . $baseDomain : 'http://subdomain.' . $baseDomain;
                $websiteButtonLabel = $statusValue === 'aktif' ? 'Buka Website' : 'Preview Website';
                $logoUrl = $websiteSetting?->logo ? asset('storage/' . $websiteSetting->logo) : null;
                $bannerUrl = $websiteSetting?->banner ? asset('storage/' . $websiteSetting->banner) : null;
            @endphp

            <form action="{{ route('website-settings.update') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <div>
                        <label class="block text-sm font-bold text-slate-700">Nama Website</label>
                        <input type="text" name="nama_website" value="{{ old('nama_website', $websiteSetting?->nama_website) }}" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" @disabled(! $canUpdate)>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700">Subdomain</label>
                        <div class="mt-2 flex rounded-lg border border-slate-300 bg-white focus-within:border-emerald-700 focus-within:ring-4 focus-within:ring-emerald-700/10">
                            <input type="text" name="subdomain" value="{{ $subdomainValue }}" placeholder="hadyurasul" class="min-w-0 flex-1 rounded-l-lg border-0 px-4 py-3 lowercase focus:ring-0" required @disabled(! $canUpdate)>
                            <span class="flex items-center rounded-r-lg border-l border-slate-200 bg-slate-50 px-3 text-sm font-semibold text-slate-700">.{{ $baseDomain }}</span>
                        </div>
                        <div class="mt-2 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-sm font-semibold text-slate-700">URL publik: {{ $previewUrl }}</p>
                            @if($subdomainValue)
                                <a href="{{ $previewUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-lg bg-emerald-900 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-800">
                                    {{ $websiteButtonLabel }}
                                </a>
                            @endif
                        </div>
                        <p class="mt-1 text-xs text-slate-600">Gunakan huruf kecil, angka, dan strip. Reserved: {{ implode(', ', $reservedSubdomains) }}.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700">Slogan</label>
                    <input type="text" name="slogan" value="{{ old('slogan', $websiteSetting?->slogan) }}" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" @disabled(! $canUpdate)>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700">Deskripsi Singkat</label>
                    <textarea name="deskripsi_singkat" rows="4" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" @disabled(! $canUpdate)>{{ old('deskripsi_singkat', $websiteSetting?->deskripsi_singkat) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700">Alamat Publik</label>
                    <textarea name="alamat_publik" rows="3" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" @disabled(! $canUpdate)>{{ old('alamat_publik', $websiteSetting?->alamat_publik) }}</textarea>
                </div>

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <div>
                        <label class="block text-sm font-bold text-slate-700">No. WhatsApp Publik</label>
                        <input type="text" name="no_whatsapp_publik" value="{{ old('no_whatsapp_publik', $websiteSetting?->no_whatsapp_publik) }}" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" @disabled(! $canUpdate)>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700">Email Publik</label>
                        <input type="email" name="email_publik" value="{{ old('email_publik', $websiteSetting?->email_publik) }}" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" @disabled(! $canUpdate)>
                    </div>
                </div>

                <section class="rounded-2xl border border-emerald-100 bg-emerald-50/70 p-5">
                    <div class="mb-5">
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-amber-700">Media Sosial</p>
                        <h3 class="mt-1 text-lg font-black text-slate-950">Media Sosial Resmi</h3>
                        <p class="mt-1 text-sm font-semibold text-slate-700">Isi link akun resmi masjid. Kosongkan jika belum ada.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                        <div>
                            <label class="block text-sm font-bold text-slate-700">Instagram URL</label>
                            <input type="url" name="instagram_url" value="{{ old('instagram_url', $websiteSetting?->instagram_url) }}" placeholder="https://instagram.com/namamasjid" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3" @disabled(! $canUpdate)>
                            @error('instagram_url')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700">TikTok URL</label>
                            <input type="url" name="tiktok_url" value="{{ old('tiktok_url', $websiteSetting?->tiktok_url) }}" placeholder="https://www.tiktok.com/@namamasjid" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3" @disabled(! $canUpdate)>
                            @error('tiktok_url')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700">Facebook URL</label>
                            <input type="url" name="facebook_url" value="{{ old('facebook_url', $websiteSetting?->facebook_url) }}" placeholder="https://facebook.com/namamasjid" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3" @disabled(! $canUpdate)>
                            @error('facebook_url')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700">YouTube URL</label>
                            <input type="url" name="youtube_url" value="{{ old('youtube_url', $websiteSetting?->youtube_url) }}" placeholder="https://youtube.com/@namamasjid" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3" @disabled(! $canUpdate)>
                            @error('youtube_url')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-5">
                    <div class="mb-5">
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-amber-700">Menu Publik</p>
                        <h3 class="mt-1 text-lg font-black text-slate-950">Pengaturan Website Publik</h3>
                        <p class="mt-1 text-sm font-semibold text-slate-700">Jika dimatikan, menu dan halaman publik tidak akan ditampilkan. Data admin tetap aman.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                        <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <input type="checkbox" name="show_public_donasi" value="1" class="mt-1 rounded border-slate-300 text-emerald-700 focus:ring-emerald-700" @checked(old('show_public_donasi', $websiteSetting?->show_public_donasi ?? true)) @disabled(! $canUpdate)>
                            <span>
                                <span class="block text-sm font-black text-slate-900">Tampilkan Donasi</span>
                                <span class="mt-1 block text-sm font-semibold leading-6 text-slate-700">Menu Donasi dan halaman /donasi dapat diakses publik.</span>
                            </span>
                        </label>

                        <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <input type="checkbox" name="show_public_pengumuman" value="1" class="mt-1 rounded border-slate-300 text-emerald-700 focus:ring-emerald-700" @checked(old('show_public_pengumuman', $websiteSetting?->show_public_pengumuman ?? true)) @disabled(! $canUpdate)>
                            <span>
                                <span class="block text-sm font-black text-slate-900">Tampilkan Pengumuman</span>
                                <span class="mt-1 block text-sm font-semibold leading-6 text-slate-700">Menu Pengumuman dan halaman /pengumuman dapat diakses publik.</span>
                            </span>
                        </label>

                        <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <input type="checkbox" name="show_public_informasi" value="1" class="mt-1 rounded border-slate-300 text-emerald-700 focus:ring-emerald-700" @checked(old('show_public_informasi', $websiteSetting?->show_public_informasi ?? true)) @disabled(! $canUpdate)>
                            <span>
                                <span class="block text-sm font-black text-slate-900">Tampilkan Informasi</span>
                                <span class="mt-1 block text-sm font-semibold leading-6 text-slate-700">Menu Informasi dan halaman /informasi dapat diakses publik.</span>
                            </span>
                        </label>
                    </div>

                    @error('show_public_pengumuman')<p class="mt-3 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    @error('show_public_informasi')<p class="mt-3 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    @error('show_public_donasi')<p class="mt-3 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </section>

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                    <div>
                        <label class="block text-sm font-bold text-slate-700">Status Website</label>
                        <select name="status_website" class="mt-2 w-full rounded-lg border border-slate-300 px-4 py-3" required @disabled(! $canUpdate)>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" @selected(old('status_website', $websiteSetting?->status_website ?? 'draft') === $status)>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700">Logo</label>
                        <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm" @disabled(! $canUpdate)>
                        @if($websiteSetting?->logo)
                            <div class="mt-3 flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 p-3">
                                <img src="{{ $logoUrl }}" alt="Preview logo website" class="h-16 w-16 rounded-lg border border-slate-200 bg-white object-contain">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Logo saat ini</p>
                                    <p class="truncate text-xs font-semibold text-slate-700">{{ $websiteSetting->logo }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700">Banner</label>
                        <input type="file" name="banner" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-sm" @disabled(! $canUpdate)>
                        @if($websiteSetting?->banner)
                            <div class="mt-3 rounded-lg border border-slate-200 bg-slate-50 p-3">
                                <img src="{{ $bannerUrl }}" alt="Preview banner website" class="h-24 w-full rounded-lg border border-slate-200 bg-white object-cover">
                                <p class="mt-2 truncate text-xs font-semibold text-slate-700">{{ $websiteSetting->banner }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-3 font-bold text-white hover:bg-indigo-700" @disabled(! $canUpdate)>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        @endif
    </section>
</div>
@endsection
