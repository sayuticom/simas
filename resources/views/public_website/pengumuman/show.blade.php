@extends('public_website.layout')

@section('title', $pengumuman->judul . ' - ' . $website->display_name)

@section('content')
<article>
    <section class="bg-emerald-950 px-4 py-10 text-white sm:px-6 sm:py-12 lg:px-8">
        <div class="mx-auto max-w-4xl">
            <p class="text-xs font-black uppercase leading-relaxed tracking-[0.16em] text-amber-200 sm:text-sm">
                {{ $pengumuman->published_at?->format('d M Y') }}
            </p>
            <h1 class="mt-3 text-3xl font-black leading-tight sm:text-5xl">{{ $pengumuman->judul }}</h1>
            @if($pengumuman->excerpt)
                <p class="mt-3 max-w-2xl text-sm font-medium leading-7 text-emerald-50 sm:mt-4 sm:text-base">{{ $pengumuman->excerpt }}</p>
            @endif
        </div>
    </section>

    <section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 sm:py-12 lg:px-8 lg:py-14">
        @if($pengumuman->featured_image)
            <img src="{{ asset('storage/' . $pengumuman->featured_image) }}" alt="{{ $pengumuman->judul }}" class="mb-6 max-h-56 w-full rounded-2xl object-cover shadow-lg shadow-emerald-950/10 sm:mb-8 sm:max-h-none sm:aspect-[16/9] sm:rounded-3xl">
        @endif

        <div class="rounded-2xl border border-emerald-900/10 bg-white p-5 shadow-lg shadow-emerald-950/5 sm:rounded-3xl sm:p-8">
            <div class="prose max-w-none text-base font-medium leading-8 text-slate-800">
                {!! nl2br(e($pengumuman->isi)) !!}
            </div>
        </div>

        <a href="{{ route('public-website.pengumuman', ['subdomain' => $website->subdomain]) }}" class="mt-6 inline-flex w-full justify-center rounded-xl border border-emerald-900/20 bg-white px-4 py-3 text-sm font-black text-emerald-950 hover:bg-emerald-950 hover:text-white sm:mt-8 sm:w-fit">
            Kembali ke Daftar Pengumuman
        </a>
    </section>
</article>
@endsection
