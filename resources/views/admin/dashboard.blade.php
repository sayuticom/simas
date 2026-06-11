@extends('layouts.admin')

@section('title', 'Dashboard - SIMAS')
@section('page_title', 'Dashboard')

@section('content')
@php
    $rupiah = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
    $activeMosqueName = $activeMosque?->name ?? 'SIMAS Masjid';
    $activeMosqueAddress = $activeMosque?->address ?? 'Pilih masjid aktif untuk melihat ringkasan operasional.';
    $mainPhoto = $activeMosque?->photos()?->where('is_featured', true)->first() ?? $activeMosque?->photos()?->latest()?->first();
@endphp

@if(isset($mosques))
<div class="space-y-6">
    <section class="overflow-hidden rounded-2xl bg-emerald-950 text-white shadow-2xl">
        <div class="relative p-6 sm:p-8">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(212,175,55,0.24),transparent_34%)]"></div>
            <div class="relative flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-amber-200">Executive Console</p>
                    <h1 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">Daftar Masjid dan Pengelola</h1>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300">Pilih masjid aktif, pantau kelengkapan kontak, dan kelola struktur pengurus dari satu tampilan ringkas.</p>
                </div>
                <a href="{{ route('mosque.create') }}" class="inline-flex items-center justify-center rounded-xl bg-yellow-400 px-5 py-3 text-sm font-bold text-slate-950 shadow-lg shadow-amber-950/20 transition hover:bg-yellow-300">
                    <i class="fas fa-plus mr-2"></i> Tambah Masjid Baru
                </a>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['label' => 'Total Masjid', 'value' => $mosques->count(), 'icon' => 'fa-mosque'],
            ['label' => 'Total Pengelola', 'value' => $totalManagers, 'icon' => 'fa-user-tie'],
            ['label' => 'Masjid dengan Kontak', 'value' => $totalContacts, 'icon' => 'fa-address-book'],
            ['label' => 'Total Entitas', 'value' => $mosques->sum(fn($mosque) => $mosque->users->count()), 'icon' => 'fa-layer-group'],
        ] as $card)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ $card['label'] }}</p>
                        <p class="mt-3 text-3xl font-black text-slate-950">{{ $card['value'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-emerald-950 p-3 text-amber-200">
                        <i class="fas {{ $card['icon'] }} text-xl"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-5">
            <h3 class="text-lg font-bold text-slate-950">Daftar Masjid</h3>
            <p class="mt-1 text-sm text-slate-500">Klik nama masjid untuk menjadikannya masjid aktif.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                        <th class="px-5 py-4">Nama Masjid</th>
                        <th class="px-5 py-4">Alamat</th>
                        <th class="px-5 py-4">Telepon</th>
                        <th class="px-5 py-4">Pengelola DKM</th>
                        <th class="px-5 py-4">User</th>
                        <th class="px-5 py-4">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($mosques as $mosque)
                        @php
                            $otherUsers = [];
                            foreach ($mosque->users as $user) {
                                $hasDkmRole = false;
                                foreach ($user->roles as $role) {
                                    if ((int) $role->pivot->mosque_id === (int) $mosque->id && in_array($role->name, ['ketua_dkm', 'bendahara', 'sekretaris'])) {
                                        $hasDkmRole = true;
                                    }
                                }
                                if (! $hasDkmRole) {
                                    $otherUsers[] = ['name' => $user->name, 'email' => $user->email];
                                }
                            }
                        @endphp
                        <tr class="align-top hover:bg-slate-50">
                            <td class="px-5 py-4 text-sm">
                                <form action="{{ route('mosque.switch') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="mosque_id" value="{{ $mosque->id }}">
                                    <button type="submit" class="text-left font-bold text-slate-950 hover:text-amber-700">{{ $mosque->name }}</button>
                                </form>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $mosque->address ?? '-' }}</td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $mosque->phone ?? '-' }}</td>
                            <td class="px-5 py-4 text-sm text-slate-700">
                                <div class="space-y-2">
                                    <p><span class="font-semibold text-slate-950">Ketua:</span> {{ $mosquePengurus[$mosque->id]['ketua_dkm'] }}</p>
                                    <p><span class="font-semibold text-slate-950">Bendahara:</span> {{ $mosquePengurus[$mosque->id]['bendahara'] }}</p>
                                    <p><span class="font-semibold text-slate-950">Sekretaris:</span> {{ $mosquePengurus[$mosque->id]['sekretaris'] }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600">
                                @forelse($otherUsers as $user)
                                    <div class="mb-2 rounded-lg bg-slate-50 px-3 py-2">
                                        <p class="font-semibold text-slate-900">{{ $user['name'] }}</p>
                                        <p class="text-xs text-slate-500">{{ $user['email'] }}</p>
                                    </div>
                                @empty
                                    <span class="text-slate-500">Tidak ada user tambahan</span>
                                @endforelse
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-600">{{ $mosque->notes ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="space-y-6">
    <section class="overflow-hidden rounded-3xl bg-emerald-950 text-white shadow-2xl">
        <div class="relative grid gap-6 p-6 sm:p-8 lg:grid-cols-[1.4fr_0.8fr] lg:items-center">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(212,175,55,0.25),transparent_34%)]"></div>
            <div class="relative">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] !text-yellow-300">SIMAS Executive Dashboard</p>
                <h1 class="mt-4 text-3xl font-black tracking-tight !text-white sm:text-5xl">{{ $activeMosqueName }}</h1>
                <p class="mt-4 max-w-3xl text-sm leading-6 !text-slate-200">{{ $activeMosqueAddress }}</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <span class="inline-flex items-center rounded-full border border-amber-200/30 bg-amber-200/10 px-4 py-2 text-sm font-semibold text-amber-100">
                        <i class="fas fa-user-shield mr-2"></i>{{ auth()->user()?->name ?? 'Admin' }}
                    </span>
                    <span class="inline-flex items-center rounded-full border border-white/10 bg-white/10 px-4 py-2 text-sm font-semibold text-slate-100">
                        <i class="fas fa-wallet mr-2 text-amber-200"></i>Total Dana: {{ $rupiah($financialSummary['totalDanaTerkelola']) }}
                    </span>
                </div>
            </div>
            <div class="relative rounded-2xl border border-white/10 bg-white/10 p-5 backdrop-blur">
                <p class="text-sm font-semibold text-slate-200">Ringkasan Hari Ini</p>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-emerald-950/60 p-4">
                        <p class="text-xs text-slate-400">Jamaah</p>
                        <p class="mt-2 text-2xl font-black text-white">{{ $totalJamaah }}</p>
                    </div>
                    <div class="rounded-xl bg-yellow-400 p-4 text-slate-950">
                        <p class="text-xs font-semibold">Saldo</p>
                        <p class="mt-2 text-lg font-black">{{ $rupiah($financialSummary['saldoKeuangan']) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-emerald-100 bg-white shadow-sm">
        @if($activeMosque)
            <div class="grid grid-cols-1 lg:grid-cols-[300px_1fr]">
                <div class="h-56 lg:h-full">
                    @if($mainPhoto)
                        <img src="{{ asset('storage/' . $mainPhoto->path) }}" alt="Foto {{ $activeMosque->name }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full min-h-56 w-full flex-col items-center justify-center bg-emerald-950 text-amber-200">
                            <i class="fas fa-mosque text-5xl"></i>
                            <p class="mt-3 text-sm font-semibold text-white">Foto masjid belum tersedia</p>
                        </div>
                    @endif
                </div>
                <div class="p-6 sm:p-7">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.24em] text-amber-700">Masjid Aktif</p>
                            <h2 class="mt-2 text-2xl font-black text-slate-950 sm:text-3xl">{{ $activeMosque->name }}</h2>
                            <div class="mt-4 grid gap-3 text-sm text-slate-700 md:grid-cols-2">
                                <p class="flex gap-2"><i class="fas fa-location-dot mt-1 text-emerald-800"></i><span>{{ $activeMosque->address ?? 'Alamat belum diisi' }}</span></p>
                                <p class="flex gap-2"><i class="fas fa-phone mt-1 text-emerald-800"></i><span>{{ $activeMosque->phone ?? 'Telepon belum diisi' }}</span></p>
                                <p class="flex gap-2 md:col-span-2"><i class="fas fa-note-sticky mt-1 text-emerald-800"></i><span>{{ $activeMosque->notes ?? 'Catatan masjid belum diisi' }}</span></p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            @if($publicWebsiteUrl)
                                <a href="{{ $publicWebsiteUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center rounded-xl bg-amber-400 px-4 py-2 text-sm font-bold text-slate-950 hover:bg-amber-300">
                                    <i class="fas fa-up-right-from-square mr-2"></i> Buka Website
                                </a>
                            @elseif(Route::has('website-settings.edit'))
                                <a href="{{ route('website-settings.edit') }}" class="inline-flex items-center rounded-xl border border-amber-300 bg-amber-50 px-4 py-2 text-sm font-bold text-amber-900 hover:bg-amber-100">
                                    <i class="fas fa-globe mr-2"></i> Atur Website
                                </a>
                            @endif
                            @if(Route::has('profile'))
                                <a href="{{ route('profile') }}" class="inline-flex items-center rounded-xl bg-emerald-950 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-900">
                                    <i class="fas fa-pen-to-square mr-2"></i> Profil Masjid
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Ketua DKM</p>
                            <p class="mt-2 font-black text-slate-950">{{ $pengurus['ketua_dkm'] }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Bendahara</p>
                            <p class="mt-2 font-black text-slate-950">{{ $pengurus['bendahara'] }}</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-500">Sekretaris</p>
                            <p class="mt-2 font-black text-slate-950">{{ $pengurus['sekretaris'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="p-6 sm:p-7">
                <p class="text-xs font-black uppercase tracking-[0.24em] text-amber-700">Masjid Aktif</p>
                <h2 class="mt-2 text-2xl font-black text-slate-950">Belum ada masjid yang dipilih</h2>
                <p class="mt-2 text-sm text-slate-600">Pilih masjid aktif agar dashboard menampilkan informasi masjid, data jamaah, keuangan, dan modul operasional.</p>
            </div>
        @endif
    </section>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach([
            ['label' => 'Total Jamaah', 'value' => number_format($totalJamaah, 0, ',', '.'), 'note' => 'Data jamaah terdaftar', 'icon' => 'fa-users'],
            ['label' => 'Total Masuk', 'value' => $rupiah($financialSummary['totalMasuk']), 'note' => 'Penerimaan tercatat', 'icon' => 'fa-arrow-trend-up'],
            ['label' => 'Total Keluar', 'value' => $rupiah($financialSummary['totalKeluar']), 'note' => 'Pengeluaran tercatat', 'icon' => 'fa-arrow-trend-down'],
            ['label' => 'Saldo Keuangan', 'value' => $rupiah($financialSummary['saldoKeuangan']), 'note' => 'Saldo operasional', 'icon' => 'fa-wallet'],
            ['label' => 'Saldo ZIS', 'value' => $rupiah($financialSummary['saldoZis']), 'note' => 'Saldo dana ZIS', 'icon' => 'fa-hand-holding-heart'],
            ['label' => 'Total Dana Terkelola', 'value' => $rupiah($financialSummary['totalDanaTerkelola']), 'note' => 'Keuangan dan ZIS', 'icon' => 'fa-scale-balanced'],
        ] as $card)
            <div class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-xl">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">{{ $card['label'] }}</p>
                        <p class="mt-3 break-words text-2xl font-black text-slate-950 xl:text-3xl">{{ $card['value'] }}</p>
                        <p class="mt-2 text-sm text-slate-500">{{ $card['note'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-emerald-950 p-3 text-amber-200 shadow-lg shadow-emerald-100">
                        <i class="fas {{ $card['icon'] }} text-xl"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6">
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-amber-700">Rincian Posisi Dana</p>
                <h2 class="mt-2 text-xl font-black text-slate-950">Komposisi Kas dan Kanal Penerimaan</h2>
                <p class="mt-1 text-sm text-slate-500">Gabungan saldo operasional dan ZIS berdasarkan tempat penyimpanan uang.</p>
            </div>
            @php
                $positions = [
                    'tunai' => ['label' => 'Tunai', 'icon' => 'fa-money-bill-wave'],
                    'bank' => ['label' => 'Bank', 'icon' => 'fa-building-columns'],
                    'qris' => ['label' => 'QRIS', 'icon' => 'fa-qrcode'],
                ];
                $maxPosition = max(1, max(array_map('abs', $financialSummary['positionByType'] ?? [0])));
            @endphp
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                @foreach($positions as $key => $meta)
                    @php
                        $amount = $financialSummary['positionByType'][$key] ?? 0;
                        $width = min(100, round((abs($amount) / $maxPosition) * 100));
                    @endphp
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="rounded-xl bg-emerald-950 p-3 text-amber-200">
                                    <i class="fas {{ $meta['icon'] }}"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-950">{{ $meta['label'] }}</p>
                                    <p class="text-xs text-slate-500">Posisi dana {{ strtolower($meta['label']) }}</p>
                                </div>
                            </div>
                            <p class="text-right text-lg font-black text-slate-950">{{ $rupiah($amount) }}</p>
                        </div>
                        <div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-200">
                            <div class="h-full rounded-full bg-gradient-to-r from-amber-300 to-amber-600" style="width: {{ $width }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

    </div>

    @if(! auth()->user()->isSuperuser() && ! $activeMosque)
        <section class="rounded-3xl border border-yellow-300 bg-yellow-50 p-6">
            <h3 class="text-lg font-black text-slate-950">Pilih Masjid Aktif</h3>
            <p class="mt-1 text-sm text-slate-600">Pemilihan masjid sekarang ada di menu Pengaturan, bagian Profil Masjid.</p>
            <a href="{{ route('profile') }}" class="mt-5 inline-flex items-center rounded-xl bg-emerald-950 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-900">
                <i class="fas fa-building mr-2"></i> Buka Profil Masjid
            </a>
        </section>
    @endif

    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['label' => 'Data Jamaah', 'route' => 'jamaah.index', 'icon' => 'fa-users'],
            ['label' => 'Keuangan', 'route' => 'keuangan.index', 'icon' => 'fa-money-bill'],
            ['label' => 'Kegiatan', 'route' => 'kegiatan.index', 'icon' => 'fa-calendar-days'],
            ['label' => 'Laporan', 'route' => 'laporan.index', 'icon' => 'fa-chart-bar'],
        ] as $shortcut)
            @if(Route::has($shortcut['route']) && $activeMosque)
                <a href="{{ route($shortcut['route']) }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-lg">
                    <div class="flex items-center gap-4">
                        <div class="rounded-2xl bg-emerald-950 p-3 text-amber-200">
                            <i class="fas {{ $shortcut['icon'] }}"></i>
                        </div>
                        <div>
                            <p class="font-black text-slate-950">{{ $shortcut['label'] }}</p>
                            <p class="text-sm text-slate-500">Buka modul</p>
                        </div>
                    </div>
                </a>
            @endif
        @endforeach
    </section>
</div>
@endif
@endsection

