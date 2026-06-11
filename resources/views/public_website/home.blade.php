@extends('public_website.layout')

@section('title', $website->display_name)

@section('content')
@php
    $whatsappNumber = null;
    if ($website->no_whatsapp_publik) {
        $whatsappNumber = preg_replace('/\D+/', '', $website->no_whatsapp_publik);
        if (str_starts_with($whatsappNumber, '08')) {
            $whatsappNumber = '62' . substr($whatsappNumber, 1);
        }
    }

    $description = $website->deskripsi_singkat ?: 'Website resmi masjid untuk berbagi informasi profil, alamat, dan kontak pengurus kepada jamaah dan masyarakat.';
    $address = $website->alamat_publik ?: ($website->mosque?->address ?: 'Alamat publik belum tersedia.');
@endphp

<section class="relative overflow-hidden bg-emerald-950 text-white">
    @if($website->banner)
        <img src="{{ asset('storage/' . $website->banner) }}" alt="{{ $website->display_name }}" class="absolute inset-0 h-full w-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-r from-emerald-950 via-emerald-950/85 to-emerald-950/35"></div>
    @else
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(250,204,21,0.22),transparent_34%),linear-gradient(135deg,#022c22_0%,#064e3b_58%,#14532d_100%)]"></div>
    @endif

    <div class="relative mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-16 lg:px-8 lg:py-24">
        <div class="max-w-3xl overflow-hidden">
            <p class="text-xs font-black uppercase tracking-[0.2em] text-amber-200 sm:text-sm sm:tracking-[0.24em]">Website Masjid</p>
            <h1 class="mt-3 text-3xl font-black leading-tight sm:mt-4 sm:text-5xl lg:text-6xl">{{ $website->display_name }}</h1>
            @if($website->slogan)
                <p class="mt-4 text-lg font-bold text-emerald-50 sm:mt-5 sm:text-2xl">{{ $website->slogan }}</p>
            @endif
            <p class="mt-4 max-w-2xl text-sm font-medium leading-relaxed text-emerald-50 sm:mt-6 sm:text-lg sm:leading-8">{{ $description }}</p>

            <div class="mt-6 flex w-full max-w-full flex-col gap-3 sm:mt-8 sm:flex-row">
                @if($whatsappNumber)
                    <a href="https://wa.me/{{ $whatsappNumber }}" class="inline-flex w-full justify-center rounded-xl bg-amber-300 px-5 py-3 text-sm font-black text-emerald-950 shadow-lg hover:bg-amber-200 sm:w-auto sm:text-base">
                        Hubungi WhatsApp
                    </a>
                @endif
                <a href="{{ route('public-website.profile', ['subdomain' => $website->subdomain]) }}" class="inline-flex w-full justify-center rounded-xl border border-white/70 px-5 py-3 text-sm font-black text-white hover:bg-white hover:text-emerald-950 sm:w-auto sm:text-base">
                    Lihat Profil
                </a>
            </div>
        </div>
    </div>
</section>

<section class="mx-auto grid max-w-6xl gap-4 px-4 pt-8 sm:grid-cols-3 sm:px-6 lg:px-8 lg:pt-14">
    <article class="rounded-2xl border border-emerald-900/10 bg-white p-5 shadow-lg shadow-emerald-950/5 sm:p-6">
        <p class="text-sm font-black uppercase leading-relaxed tracking-[0.14em] text-amber-700">Profil Masjid</p>
        <h2 class="mt-3 text-xl font-black text-emerald-950">{{ $website->mosque?->name ?? $website->display_name }}</h2>
        <p class="mt-2 text-sm font-medium leading-6 text-slate-700">{{ \Illuminate\Support\Str::limit($description, 110) }}</p>
    </article>

    <article class="rounded-2xl border border-emerald-900/10 bg-white p-5 shadow-lg shadow-emerald-950/5 sm:p-6">
        <p class="text-sm font-black uppercase leading-relaxed tracking-[0.14em] text-amber-700">Kontak</p>
        <h2 class="mt-3 text-xl font-black text-emerald-950">{{ $website->no_whatsapp_publik ?: 'Kontak pengurus' }}</h2>
        <p class="mt-2 text-sm font-medium leading-6 text-slate-700">{{ $website->email_publik ?: 'Email publik belum tersedia.' }}</p>
    </article>

    <article class="rounded-2xl border border-emerald-900/10 bg-white p-5 shadow-lg shadow-emerald-950/5 sm:p-6">
        <p class="text-sm font-black uppercase leading-relaxed tracking-[0.14em] text-amber-700">Lokasi</p>
        <h2 class="mt-3 text-xl font-black text-emerald-950">Alamat Masjid</h2>
        <p class="mt-2 text-sm font-medium leading-6 text-slate-700">{{ \Illuminate\Support\Str::limit($address, 130) }}</p>
    </article>
</section>

<section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8 lg:py-20">
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
        <div>
            <p class="text-sm font-black uppercase leading-relaxed tracking-[0.16em] text-amber-700">Sambutan</p>
            <h2 class="mt-3 text-2xl font-black leading-tight text-emerald-950 sm:text-4xl">Selamat datang di {{ $website->display_name }}</h2>
            <p class="mt-4 text-base font-medium leading-8 text-slate-800 sm:mt-5">{{ $description }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-900/10 bg-white p-5 shadow-lg shadow-emerald-950/5 sm:rounded-3xl sm:p-7">
            <p class="text-sm font-black uppercase leading-relaxed tracking-[0.14em] text-amber-700">Informasi Publik</p>
            <dl class="mt-5 space-y-4">
                <div>
                    <dt class="text-sm font-black text-emerald-950">Nama</dt>
                    <dd class="mt-1 font-medium text-slate-800">{{ $website->mosque?->name ?? $website->display_name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-black text-emerald-950">Alamat</dt>
                    <dd class="mt-1 font-medium leading-7 text-slate-800">{{ $address }}</dd>
                </div>
            </dl>
        </div>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 pb-12 sm:px-6 sm:pb-16 lg:px-8 lg:pb-20">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-black uppercase leading-relaxed tracking-[0.16em] text-amber-700">Agenda Publik</p>
            <h2 class="mt-2 text-2xl font-black text-emerald-950 sm:text-3xl">Kajian &amp; Kegiatan Terbaru</h2>
        </div>
        <a href="{{ route('public-website.events', ['subdomain' => $website->subdomain]) }}" class="inline-flex rounded-xl border border-emerald-900/15 px-4 py-3 text-sm font-black text-emerald-950 hover:bg-emerald-950 hover:text-white sm:border-0 sm:p-0 sm:hover:bg-transparent sm:hover:text-emerald-700">Lihat Semua Kegiatan</a>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
        @forelse(($latestEvents ?? collect()) as $event)
            @php
                $eventTitle = $event->nama_kegiatan;
                $eventDescription = $event->deskripsi_publik ?: $event->deskripsi;
            @endphp
            <article class="flex h-full flex-col overflow-hidden rounded-2xl border border-emerald-900/10 bg-white shadow-lg shadow-emerald-950/5 sm:rounded-3xl">
                @if($event->poster_publik)
                    <img src="{{ asset('storage/' . $event->poster_publik) }}" alt="{{ $eventTitle }}" class="h-48 w-full object-cover sm:h-auto sm:aspect-square">
                @else
                    <div class="flex h-40 w-full items-center justify-center bg-emerald-950 p-5 text-center text-white sm:h-auto sm:aspect-square sm:p-6">
                        <span class="text-base font-black leading-tight sm:text-lg">{{ $eventTitle }}</span>
                    </div>
                @endif
                <div class="flex flex-1 flex-col p-5">
                    <p class="text-sm font-black uppercase leading-relaxed tracking-[0.14em] text-amber-700">{{ $event->jenis_kegiatan ?: 'Kegiatan' }}</p>
                    <h3 class="mt-2 text-lg font-black leading-tight text-emerald-950 sm:text-xl">{{ $eventTitle }}</h3>
                    @if($event->tema_materi)
                        <p class="mt-2 text-sm font-black leading-6 text-emerald-900">Tema: {{ $event->tema_materi }}</p>
                    @endif
                    <p class="mt-3 text-sm font-semibold text-slate-700">
                        {{ $event->tanggal_mulai?->format('d M Y') ?? 'Tanggal menyusul' }}
                        @if($event->tanggal_mulai)
                            <span class="block">{{ $event->tanggal_mulai->format('H:i') }}{{ $event->tanggal_selesai ? ' - ' . $event->tanggal_selesai->format('H:i') : '' }}</span>
                        @endif
                    </p>
                    @if($event->lokasi)
                        <p class="mt-2 text-sm font-medium text-slate-700">{{ $event->lokasi }}</p>
                    @endif
                    @if($eventDescription)
                        <p class="mt-3 text-sm font-medium leading-6 text-slate-700">{{ \Illuminate\Support\Str::limit($eventDescription, 100) }}</p>
                    @endif
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-emerald-900/20 bg-white p-6 text-center text-sm font-semibold text-slate-700 md:col-span-3 sm:rounded-3xl sm:p-8">
                Belum ada kajian atau kegiatan yang ditayangkan.
            </div>
        @endforelse
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
    <div class="rounded-2xl bg-emerald-950 px-5 py-8 text-white shadow-xl shadow-emerald-950/10 sm:rounded-3xl sm:px-10 sm:py-10">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-black uppercase leading-relaxed tracking-[0.16em] text-amber-200">Kontak Pengurus</p>
                <h2 class="mt-3 text-2xl font-black sm:text-3xl">Hubungi Pengurus Masjid</h2>
                <p class="mt-3 max-w-2xl font-medium leading-7 text-emerald-50">Gunakan kontak publik yang tersedia untuk informasi kegiatan, layanan masjid, atau kebutuhan komunikasi dengan pengurus.</p>
            </div>
            @if($whatsappNumber)
                <a href="https://wa.me/{{ $whatsappNumber }}" class="inline-flex w-full justify-center rounded-xl bg-amber-300 px-5 py-3 text-sm font-black text-emerald-950 hover:bg-amber-200 sm:w-auto sm:text-base">
                    WhatsApp Pengurus
                </a>
            @endif
        </div>
    </div>
</section>
@endsection
