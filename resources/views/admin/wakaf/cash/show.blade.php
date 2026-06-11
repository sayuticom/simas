@extends('layouts.admin')

@section('title', 'Detail Wakaf Tunai - SIMAS')
@section('page_title', 'Detail Wakaf Tunai')

@section('content')
@php
    $statusClasses = [
        'tercatat' => 'bg-indigo-100 text-indigo-700',
        'diverifikasi' => 'bg-green-100 text-green-700',
        'batal' => 'bg-red-50 text-red-700',
    ];
    $paymentLabels = [
        'tunai' => 'Tunai',
        'transfer' => 'Transfer Bank',
        'qris' => 'QRIS',
        'ewallet' => 'E-Wallet',
        'lainnya' => 'Lainnya',
    ];
@endphp
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Wakaf Tunai</h2>
            <p class="text-sm text-gray-500">Detail penerimaan wakaf tunai.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('wakaf.cash.receipt', $wakafCash) }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700">
                <i class="fas fa-print"></i> Cetak Bukti
            </a>
            <a href="{{ route('wakaf.cash.edit', $wakafCash) }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-3 text-white hover:bg-green-700">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('wakaf.cash.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Tanggal Terima</p>
            <p class="text-lg font-semibold text-gray-800">{{ $wakafCash->tanggal_terima?->format('d-m-Y') }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Nominal</p>
            <p class="text-lg font-semibold text-gray-800">Rp {{ number_format((float) $wakafCash->nominal, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Wakif</p>
            <p class="text-lg font-semibold text-gray-800">{{ $wakafCash->wakif?->nama ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Nazhir</p>
            <p class="text-lg font-semibold text-gray-800">{{ $wakafCash->nazhir?->nama ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Program Wakaf</p>
            <p class="text-lg font-semibold text-gray-800">{{ $wakafCash->program?->nama ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Metode Pembayaran</p>
            <p class="text-lg font-semibold text-gray-800">{{ $paymentLabels[$wakafCash->metode_pembayaran] ?? ($wakafCash->metode_pembayaran ?: '-') }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Akun Penerimaan Dana</p>
            <p class="text-lg font-semibold text-gray-800">{{ $wakafCash->cashAccount ? $wakafCash->cashAccount->name.' - '.$wakafCash->cashAccount->accountTypeLabel() : '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Status</p>
            <span class="mt-2 inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $statusClasses[$wakafCash->status] ?? $statusClasses['tercatat'] }}">
                {{ ucfirst($wakafCash->status ?: 'tercatat') }}
            </span>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">File</p>
            <div class="mt-2 flex flex-wrap gap-3 text-sm font-semibold">
                @if($wakafCash->bukti_file)
                    <a href="{{ asset('storage/'.$wakafCash->bukti_file) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">Bukti Pembayaran</a>
                @endif
                @if($wakafCash->dokumen_ikrar)
                    <a href="{{ asset('storage/'.$wakafCash->dokumen_ikrar) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">Dokumen Ikrar</a>
                @endif
                @if(! $wakafCash->bukti_file && ! $wakafCash->dokumen_ikrar)
                    <span class="text-gray-700">-</span>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-5">
        <p class="text-sm text-gray-500">Tujuan Investasi</p>
        <p class="mt-2 text-gray-800">{{ $wakafCash->tujuan_investasi ?: '-' }}</p>
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-5">
        <p class="text-sm text-gray-500">Keterangan</p>
        <p class="mt-2 text-gray-800">{{ $wakafCash->keterangan ?: '-' }}</p>
    </div>
</div>
@endsection
