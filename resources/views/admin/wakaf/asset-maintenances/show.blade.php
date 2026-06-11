@extends('layouts.admin')

@section('title', 'Detail Perawatan Aset Wakaf - SIMAS')
@section('page_title', 'Detail Perawatan Aset Wakaf')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $maintenance->wakafAsset?->nama_aset ?? 'Perawatan Aset Wakaf' }}</h2>
            <p class="text-sm text-gray-500">Detail biaya perawatan aset wakaf.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('wakaf.asset-maintenances.edit', $maintenance) }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-3 text-white hover:bg-green-700">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('wakaf.asset-maintenances.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Aset Wakaf</p>
            <p class="text-lg font-semibold text-gray-800">{{ $maintenance->wakafAsset?->nama_aset ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Tanggal Pengeluaran</p>
            <p class="text-lg font-semibold text-gray-800">{{ $maintenance->tanggal_pengeluaran?->format('d-m-Y') }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Jenis Biaya</p>
            <p class="text-lg font-semibold text-gray-800">{{ $maintenance->jenis_biaya ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Nominal</p>
            <p class="text-lg font-semibold text-gray-800">Rp {{ number_format((float) $maintenance->nominal, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Dibayar Dari</p>
            <p class="text-lg font-semibold text-gray-800">{{ $maintenance->dibayar_dari ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Akun Pembayaran</p>
            <p class="text-lg font-semibold text-gray-800">{{ $maintenance->cashAccount?->name ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Transaksi Kas Terkait</p>
            <p class="text-lg font-semibold text-gray-800">{{ $maintenance->mosque_cash_transaction_id ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Penanggung Jawab</p>
            <p class="text-lg font-semibold text-gray-800">{{ $maintenance->penanggung_jawab ?: '-' }}</p>
        </div>
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-5">
        <p class="text-sm text-gray-500">Bukti File</p>
        @if($maintenance->bukti_file)
            <a href="{{ asset('storage/'.$maintenance->bukti_file) }}" target="_blank" class="mt-2 inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-900">Lihat bukti</a>
        @else
            <p class="mt-2 text-gray-800">-</p>
        @endif
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-5">
        <p class="text-sm text-gray-500">Keterangan</p>
        <p class="mt-2 text-gray-800">{{ $maintenance->keterangan ?: '-' }}</p>
    </div>
</div>
@endsection
