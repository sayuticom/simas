@extends('layouts.admin')

@section('title', 'Detail Transaksi - SIMAS')
@section('page_title', 'Detail Transaksi')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    @if(session('error'))
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $transaction->category?->name ?? 'Transaksi Keuangan' }}</h2>
            <p class="text-sm text-gray-500">Rincian transaksi kas masjid umum.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            @if($transaction->isFromZisDistribution())
                @if($transaction->sourceDistribution)
                    <a href="{{ route('zis.distributions.show', $transaction->sourceDistribution) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-3 text-white hover:bg-blue-700 transition"><i class="fas fa-hand-holding-heart"></i> Lihat Penyaluran ZIS</a>
                @endif
            @elseif($transaction->isFromWakaf())
                <span class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-gray-600"><i class="fas fa-lock"></i> Dikelola dari Modul Wakaf</span>
            @else
                <a href="{{ route('keuangan.transaksi.edit', $transaction) }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-3 text-white hover:bg-green-700 transition"><i class="fas fa-edit"></i> Edit</a>
            @endif
            <a href="{{ route('keuangan.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50 transition"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Tanggal Transaksi</p>
            <p class="text-lg font-semibold text-gray-800">{{ $transaction->transaction_date->format('d-m-Y') }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Jenis Transaksi</p>
            <span class="mt-2 inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $transaction->type === 'masuk' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                {{ ucfirst($transaction->type) }}
            </span>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Sumber Transaksi</p>
            @if($transaction->isFromZisDistribution())
                <span class="mt-2 inline-flex rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-700">{{ $transaction->sourceLabel() }}</span>
                <p class="mt-2 text-sm text-gray-600">Transaksi ini berasal dari Penyaluran ZIS dan tidak bisa dihapus langsung dari Keuangan Operasional.</p>
            @elseif($transaction->isFromWakafCash())
                <span class="mt-2 inline-flex rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-700">{{ $transaction->sourceLabel() }}</span>
                <p class="mt-2 text-sm text-gray-600">Transaksi ini berasal dari Modul Wakaf dan harus dikelola dari data sumbernya.</p>
            @elseif($transaction->isFromWakafManagementResult())
                <span class="mt-2 inline-flex rounded-full bg-teal-100 px-3 py-1 text-sm font-semibold text-teal-700">{{ $transaction->sourceLabel() }}</span>
                <p class="mt-2 text-sm text-gray-600">Transaksi ini berasal dari Modul Wakaf dan harus dikelola dari data sumbernya.</p>
            @elseif($transaction->isFromWakafAssetMaintenance())
                <span class="mt-2 inline-flex rounded-full bg-orange-100 px-3 py-1 text-sm font-semibold text-orange-700">{{ $transaction->sourceLabel() }}</span>
                <p class="mt-2 text-sm text-gray-600">Transaksi ini berasal dari Modul Wakaf dan harus dikelola dari data sumbernya.</p>
            @else
                <span class="mt-2 inline-flex rounded-full bg-gray-100 px-3 py-1 text-sm font-semibold text-gray-700">{{ $transaction->sourceLabel() }}</span>
            @endif
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Jumlah</p>
            <p class="text-lg font-semibold text-gray-800">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Akun Kas</p>
            <p class="text-lg font-semibold text-gray-800">{{ $transaction->cashAccount?->name ?? '-' }}</p>
            <p class="text-sm text-gray-500">{{ $transaction->cashAccount?->accountTypeLabel() ?? ($transaction->payment_method ?: '-') }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5 lg:col-span-2">
            <p class="text-sm text-gray-500">Keterangan</p>
            <p class="text-lg font-semibold text-gray-800 mt-2">{{ $transaction->description ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5 lg:col-span-2">
            <p class="text-sm text-gray-500">Dibuat oleh</p>
            <p class="text-lg font-semibold text-gray-800">{{ $transaction->created_by }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5 lg:col-span-2">
            <p class="text-sm text-gray-500">Bukti Transaksi</p>
            @if($transaction->proof_file)
                <a href="{{ asset('storage/' . $transaction->proof_file) }}" target="_blank" class="mt-2 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 transition">
                    <i class="fas fa-file-download"></i> Lihat Bukti
                </a>
            @else
                <p class="text-gray-800 mt-2">Tidak ada bukti transaksi.</p>
            @endif
        </div>
    </div>
</div>
@endsection
