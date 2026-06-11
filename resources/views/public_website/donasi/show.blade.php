@extends('public_website.layout')

@section('title', $program->title . ' - ' . $website->display_name)

@section('content')
@php
    $progress = $program->progressPercentage();
    $whatsappNumber = null;
    if ($program->whatsapp_number) {
        $whatsappNumber = preg_replace('/\D+/', '', $program->whatsapp_number);
        if (str_starts_with($whatsappNumber, '08')) {
            $whatsappNumber = '62' . substr($whatsappNumber, 1);
        }
    }
    $whatsappText = rawurlencode("Assalamu'alaikum, saya ingin konfirmasi donasi untuk program {$program->title}. Nominal donasi: ");
@endphp

<article>
    <section class="bg-emerald-950 px-4 py-10 text-white sm:px-6 sm:py-12 lg:px-8">
        <div class="mx-auto max-w-4xl">
            <p class="text-xs font-black uppercase leading-relaxed tracking-[0.16em] text-amber-200 sm:text-sm">{{ $program->category ?: 'Program Donasi' }}</p>
            <h1 class="mt-3 text-3xl font-black leading-tight sm:text-5xl">{{ $program->title }}</h1>
            <p class="mt-3 max-w-2xl text-sm font-medium leading-7 text-emerald-50 sm:mt-4 sm:text-base">Program donasi manual yang dikelola oleh {{ $website->display_name }}.</p>
        </div>
    </section>

    <section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 sm:py-12 lg:px-8 lg:py-14">
        @if($program->featured_image)
            <img src="{{ asset('storage/' . $program->featured_image) }}" alt="{{ $program->title }}" class="mb-6 max-h-56 w-full rounded-2xl object-cover shadow-lg shadow-emerald-950/10 sm:mb-8 sm:max-h-none sm:aspect-[16/9] sm:rounded-3xl">
        @endif

        <div class="rounded-2xl border border-emerald-900/10 bg-white p-5 shadow-lg shadow-emerald-950/5 sm:rounded-3xl sm:p-8">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-2xl bg-emerald-50 p-4">
                    <p class="text-xs font-black uppercase tracking-[0.14em] text-emerald-900">Target</p>
                    <p class="mt-2 text-lg font-black text-emerald-950">{{ $program->target_amount ? 'Rp '.number_format((float) $program->target_amount, 0, ',', '.') : '-' }}</p>
                </div>
                <div class="rounded-2xl bg-emerald-50 p-4">
                    <p class="text-xs font-black uppercase tracking-[0.14em] text-emerald-900">Terkumpul</p>
                    <p class="mt-2 text-lg font-black text-emerald-950">Rp {{ number_format((float) $program->collected_amount, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-2xl bg-emerald-50 p-4">
                    <p class="text-xs font-black uppercase tracking-[0.14em] text-emerald-900">Progress</p>
                    <p class="mt-2 text-lg font-black text-emerald-950">{{ $progress }}%</p>
                </div>
            </div>

            <div class="mt-6 h-3 rounded-full bg-emerald-50">
                <div class="h-3 rounded-full bg-emerald-700" style="width: {{ $progress }}%"></div>
            </div>

            <div class="mt-8 text-base font-medium leading-8 text-slate-800">
                {!! nl2br(e($program->description)) !!}
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            @if($program->bank_name || $program->bank_account_number || $program->bank_account_name)
                <div class="rounded-2xl border border-emerald-900/10 bg-white p-5 shadow-lg shadow-emerald-950/5 sm:rounded-3xl sm:p-6">
                    <p class="text-sm font-black uppercase leading-relaxed tracking-[0.14em] text-amber-700">Transfer Bank</p>
                    <dl class="mt-4 space-y-3 text-sm font-semibold text-slate-800">
                        @if($program->bank_name)
                            <div><dt class="text-emerald-950">Bank</dt><dd class="mt-1">{{ $program->bank_name }}</dd></div>
                        @endif
                        @if($program->bank_account_number)
                            <div><dt class="text-emerald-950">Nomor Rekening</dt><dd class="mt-1 break-all text-lg font-black">{{ $program->bank_account_number }}</dd></div>
                        @endif
                        @if($program->bank_account_name)
                            <div><dt class="text-emerald-950">Atas Nama</dt><dd class="mt-1">{{ $program->bank_account_name }}</dd></div>
                        @endif
                    </dl>
                </div>
            @endif

            @if($program->qris_image)
                <div class="rounded-2xl border border-emerald-900/10 bg-white p-5 shadow-lg shadow-emerald-950/5 sm:rounded-3xl sm:p-6">
                    <p class="text-sm font-black uppercase leading-relaxed tracking-[0.14em] text-amber-700">QRIS Manual</p>
                    <img src="{{ asset('storage/' . $program->qris_image) }}" alt="QRIS {{ $program->title }}" class="mt-4 max-h-80 w-full rounded-xl object-contain">
                </div>
            @endif
        </div>

        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
            @if($whatsappNumber)
                <a href="https://wa.me/{{ $whatsappNumber }}?text={{ $whatsappText }}" class="inline-flex w-full justify-center rounded-xl bg-emerald-950 px-4 py-3 text-sm font-black text-white hover:bg-emerald-800 sm:w-auto">
                    Konfirmasi via WhatsApp
                </a>
            @endif
            <a href="{{ route('public-website.donasi', ['subdomain' => $website->subdomain]) }}" class="inline-flex w-full justify-center rounded-xl border border-emerald-900/20 bg-white px-4 py-3 text-sm font-black text-emerald-950 hover:bg-emerald-950 hover:text-white sm:w-auto">
                Kembali ke Daftar Donasi
            </a>
        </div>
    </section>
</article>
@endsection
