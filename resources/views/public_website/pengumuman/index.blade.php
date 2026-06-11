@extends('public_website.layout')

@section('title', 'Pengumuman - ' . $website->display_name)

@section('content')
<section class="bg-emerald-950 px-4 py-10 text-white sm:px-6 sm:py-12 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <p class="text-xs font-black uppercase leading-relaxed tracking-[0.16em] text-amber-200 sm:text-sm">Informasi Publik</p>
        <h1 class="mt-3 text-3xl font-black leading-tight sm:text-5xl">Pengumuman</h1>
        <p class="mt-3 max-w-2xl text-sm font-medium leading-7 text-emerald-50 sm:mt-4 sm:text-base">Pengumuman resmi yang ditayangkan oleh pengurus {{ $website->display_name }}.</p>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8 lg:py-16">
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($pengumumans as $pengumuman)
            @php
                $summary = $pengumuman->excerpt ?: \Illuminate\Support\Str::limit($pengumuman->isi, 150);
            @endphp
            <article class="flex h-full flex-col overflow-hidden rounded-2xl border border-emerald-900/10 bg-white shadow-lg shadow-emerald-950/5">
                @if($pengumuman->featured_image)
                    <img src="{{ asset('storage/' . $pengumuman->featured_image) }}" alt="{{ $pengumuman->judul }}" class="h-44 w-full object-cover sm:h-52">
                @else
                    <div class="flex h-32 w-full items-center justify-center bg-emerald-950 p-5 text-center text-white sm:h-36">
                        <span class="rounded-full border border-amber-200/30 bg-white/10 px-4 py-2 text-xs font-black uppercase tracking-[0.14em] text-amber-100">
                            Pengumuman
                        </span>
                    </div>
                @endif

                <div class="flex flex-1 flex-col p-5 sm:p-6">
                    <p class="text-sm font-black uppercase leading-relaxed tracking-[0.14em] text-amber-700">
                        {{ $pengumuman->published_at?->format('d M Y') }}
                    </p>
                    <h2 class="mt-2 text-xl font-black leading-tight text-emerald-950 sm:text-2xl">{{ $pengumuman->judul }}</h2>
                    @if($summary)
                        <p class="mt-4 text-sm font-medium leading-7 text-slate-700">{{ $summary }}</p>
                    @endif
                    <div class="mt-auto pt-6">
                        <a href="{{ route('public-website.pengumuman.show', ['subdomain' => $website->subdomain, 'slug' => $pengumuman->slug]) }}" class="inline-flex w-full justify-center rounded-xl bg-emerald-950 px-4 py-3 text-sm font-black text-white hover:bg-emerald-800 sm:w-fit">
                            Baca Selengkapnya
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-emerald-900/20 bg-white p-6 text-center text-sm font-semibold text-slate-700 md:col-span-2 lg:col-span-3 sm:rounded-3xl sm:p-8">
                Belum ada pengumuman yang ditayangkan.
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $pengumumans->links() }}
    </div>
</section>
@endsection
