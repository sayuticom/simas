@extends('public_website.layout')

@section('title', 'Program Donasi - ' . $website->display_name)

@section('content')
<section class="bg-emerald-950 px-4 py-10 text-white sm:px-6 sm:py-12 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <p class="text-xs font-black uppercase leading-relaxed tracking-[0.16em] text-amber-200 sm:text-sm">Donasi Publik</p>
        <h1 class="mt-3 text-3xl font-black leading-tight sm:text-5xl">Program Donasi</h1>
        <p class="mt-3 max-w-2xl text-sm font-medium leading-7 text-emerald-50 sm:mt-4 sm:text-base">Dukung program kebaikan yang ditayangkan oleh pengurus {{ $website->display_name }}.</p>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8 lg:py-16">
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($programs as $program)
            @php
                $progress = $program->progressPercentage();
            @endphp
            <article class="flex h-full flex-col overflow-hidden rounded-2xl border border-emerald-900/10 bg-white shadow-lg shadow-emerald-950/5 sm:rounded-3xl">
                @if($program->featured_image)
                    <img src="{{ asset('storage/' . $program->featured_image) }}" alt="{{ $program->title }}" class="h-44 w-full object-cover sm:h-52">
                @else
                    <div class="flex h-32 w-full items-center justify-center bg-emerald-950 p-5 text-center text-white sm:h-36">
                        <span class="rounded-full border border-amber-200/30 bg-white/10 px-4 py-2 text-xs font-black uppercase tracking-[0.14em] text-amber-100">Donasi</span>
                    </div>
                @endif

                <div class="flex flex-1 flex-col p-5 sm:p-6">
                    <p class="text-sm font-black uppercase leading-relaxed tracking-[0.14em] text-amber-700">{{ $program->category ?: 'Program Donasi' }}</p>
                    <h2 class="mt-2 text-xl font-black leading-tight text-emerald-950 sm:text-2xl">{{ $program->title }}</h2>

                    <div class="mt-5 space-y-2 text-sm font-semibold text-slate-700">
                        <p>Target: {{ $program->target_amount ? 'Rp '.number_format((float) $program->target_amount, 0, ',', '.') : 'Tidak ditentukan' }}</p>
                        <p>Terkumpul: Rp {{ number_format((float) $program->collected_amount, 0, ',', '.') }}</p>
                    </div>

                    <div class="mt-4 h-3 rounded-full bg-emerald-50">
                        <div class="h-3 rounded-full bg-emerald-700" style="width: {{ $progress }}%"></div>
                    </div>
                    <p class="mt-2 text-xs font-black text-emerald-900">{{ $progress }}% terkumpul</p>

                    <div class="mt-auto pt-6">
                        <a href="{{ route('public-website.donasi.show', ['subdomain' => $website->subdomain, 'slug' => $program->slug]) }}" class="inline-flex w-full justify-center rounded-xl bg-emerald-950 px-4 py-3 text-sm font-black text-white hover:bg-emerald-800 sm:w-fit">
                            Lihat Program
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-emerald-900/20 bg-white p-6 text-center text-sm font-semibold text-slate-700 md:col-span-2 lg:col-span-3 sm:rounded-3xl sm:p-8">
                Belum ada program donasi yang ditayangkan.
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $programs->links() }}
    </div>
</section>
@endsection
