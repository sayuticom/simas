@extends('public_website.layout')

@section('title', 'Profil - ' . $website->display_name)

@section('content')
@php
    $description = $website->deskripsi_singkat ?: 'Profil publik masjid belum dilengkapi. Informasi akan diperbarui oleh pengurus masjid.';
    $address = $website->alamat_publik ?: ($website->mosque?->address ?: 'Alamat publik belum tersedia.');
@endphp

<section class="bg-emerald-950 px-4 py-10 text-white sm:px-6 sm:py-12 lg:px-8">
    <div class="mx-auto max-w-6xl">
        <p class="text-xs font-black uppercase leading-relaxed tracking-[0.16em] text-amber-200 sm:text-sm">Profil</p>
        <h1 class="mt-3 text-3xl font-black leading-tight sm:text-5xl">{{ $website->mosque?->name ?? $website->display_name }}</h1>
        @if($website->slogan)
            <p class="mt-3 text-lg font-bold text-emerald-50 sm:mt-4 sm:text-xl">{{ $website->slogan }}</p>
        @endif
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8 lg:py-16">
    <div class="rounded-2xl border border-emerald-900/10 bg-white p-5 shadow-lg shadow-emerald-950/5 sm:rounded-3xl sm:p-8">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-[0.8fr_1.2fr]">
            <div>
                @if($website->logo)
                    <img src="{{ asset('storage/' . $website->logo) }}" alt="{{ $website->display_name }}" class="h-28 w-28 rounded-2xl border border-emerald-900/10 bg-white object-contain shadow-sm">
                @else
                    <div class="flex h-28 w-28 items-center justify-center rounded-2xl bg-emerald-950 text-4xl font-black text-amber-200">M</div>
                @endif
                <h2 class="mt-5 text-xl font-black text-emerald-950 sm:text-2xl">{{ $website->display_name }}</h2>
            </div>

            <div class="space-y-6">
                <div>
                    <p class="text-sm font-black uppercase leading-relaxed tracking-[0.14em] text-amber-700">Deskripsi</p>
                    <p class="mt-3 text-base font-medium leading-8 text-slate-800">{{ $description }}</p>
                </div>

                <div class="rounded-2xl border border-emerald-900/10 bg-emerald-50 p-5 sm:p-6">
                    <p class="text-sm font-black uppercase leading-relaxed tracking-[0.14em] text-emerald-950">Alamat</p>
                    <p class="mt-2 font-medium leading-7 text-slate-800">{{ $address }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
