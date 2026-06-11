@extends('public_website.layout')

@section('title', 'Kontak - ' . $website->display_name)

@section('content')
@php
    $whatsappNumber = null;
    if ($website->no_whatsapp_publik) {
        $whatsappNumber = preg_replace('/\D+/', '', $website->no_whatsapp_publik);
        if (str_starts_with($whatsappNumber, '08')) {
            $whatsappNumber = '62' . substr($whatsappNumber, 1);
        }
    }

    $address = $website->alamat_publik ?: ($website->mosque?->address ?: 'Alamat publik belum tersedia.');
    $socialLinks = [
        'Instagram' => $website->instagram_url,
        'TikTok' => $website->tiktok_url,
        'Facebook' => $website->facebook_url,
        'YouTube' => $website->youtube_url,
    ];
    $socialLinks = array_filter($socialLinks);
@endphp

<section class="bg-emerald-950 px-4 py-10 text-white sm:px-6 sm:py-12 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <p class="text-xs font-black uppercase leading-relaxed tracking-[0.16em] text-amber-200 sm:text-sm">Kontak</p>
        <h1 class="mt-3 text-3xl font-black leading-tight sm:text-5xl">{{ $website->display_name }}</h1>
        <p class="mt-3 max-w-2xl text-sm font-medium leading-7 text-emerald-50 sm:mt-4 sm:text-base">Informasi kontak publik untuk jamaah dan masyarakat.</p>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8 lg:py-16">
    <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
        <article class="rounded-2xl border border-emerald-900/10 bg-white p-5 shadow-lg shadow-emerald-950/5 sm:rounded-3xl sm:p-7">
            <p class="text-sm font-black uppercase leading-relaxed tracking-[0.14em] text-amber-700">Alamat</p>
            <h2 class="mt-3 text-xl font-black text-emerald-950">Lokasi Masjid</h2>
            <p class="mt-3 font-medium leading-7 text-slate-800">{{ $address }}</p>
        </article>

        <article class="rounded-2xl border border-emerald-900/10 bg-white p-5 shadow-lg shadow-emerald-950/5 sm:rounded-3xl sm:p-7">
            <p class="text-sm font-black uppercase leading-relaxed tracking-[0.14em] text-amber-700">WhatsApp</p>
            <h2 class="mt-3 text-xl font-black text-emerald-950">Pengurus Masjid</h2>
            @if($whatsappNumber)
                <a href="https://wa.me/{{ $whatsappNumber }}" class="mt-3 inline-flex w-full justify-center rounded-xl bg-emerald-950 px-4 py-3 font-black text-white hover:bg-emerald-800 sm:w-fit">{{ $website->no_whatsapp_publik }}</a>
            @else
                <p class="mt-3 font-medium text-slate-800">Nomor WhatsApp publik belum tersedia.</p>
            @endif
        </article>

        <article class="rounded-2xl border border-emerald-900/10 bg-white p-5 shadow-lg shadow-emerald-950/5 sm:rounded-3xl sm:p-7">
            <p class="text-sm font-black uppercase leading-relaxed tracking-[0.14em] text-amber-700">Email</p>
            <h2 class="mt-3 text-xl font-black text-emerald-950">Surat Elektronik</h2>
            @if($website->email_publik)
                <a href="mailto:{{ $website->email_publik }}" class="mt-3 inline-flex break-all font-black text-emerald-950 hover:text-emerald-700">{{ $website->email_publik }}</a>
            @else
                <p class="mt-3 font-medium text-slate-800">Email publik belum tersedia.</p>
            @endif
        </article>
    </div>

    @if(! empty($socialLinks))
        <div class="mt-6 rounded-2xl border border-emerald-900/10 bg-white p-5 shadow-lg shadow-emerald-950/5 sm:rounded-3xl sm:p-7">
            <p class="text-sm font-black uppercase leading-relaxed tracking-[0.14em] text-amber-700">Ikuti Kami</p>
            <h2 class="mt-3 text-2xl font-black text-emerald-950">Media Sosial Resmi</h2>
            <p class="mt-2 font-medium text-slate-800">Ikuti akun resmi masjid untuk mendapatkan informasi terbaru.</p>

            <div class="mt-5 flex flex-wrap gap-3">
                @foreach($socialLinks as $label => $url)
                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="inline-flex rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-black text-emerald-900 hover:bg-emerald-100">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</section>
@endsection
