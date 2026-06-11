@extends('layouts.admin')

@section('title', 'Detail Hasil Kelola Wakaf - SIMAS')
@section('page_title', 'Detail Hasil Kelola Wakaf')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $result->productiveAsset?->wakafAsset?->nama_aset ?? 'Hasil Kelola Wakaf' }}</h2>
            <p class="text-sm text-gray-500">Detail penerimaan hasil pengelolaan wakaf.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('wakaf.management-results.edit', $result) }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-3 text-white hover:bg-green-700">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('wakaf.management-results.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Aset Produktif</p>
            <p class="text-lg font-semibold text-gray-800">{{ $result->productiveAsset?->wakafAsset?->nama_aset ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Tanggal Penerimaan</p>
            <p class="text-lg font-semibold text-gray-800">{{ $result->tanggal_penerimaan?->format('d-m-Y') }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Jenis Hasil</p>
            <p class="text-lg font-semibold text-gray-800">{{ $result->jenis_hasil ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Nominal</p>
            <p class="text-lg font-semibold text-gray-800">Rp {{ number_format((float) $result->nominal, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Periode</p>
            <p class="text-lg font-semibold text-gray-800">{{ $result->periode ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Nama Pembayar</p>
            <p class="text-lg font-semibold text-gray-800">{{ $result->nama_pembayar ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Masuk ke Kas Masjid</p>
            <p class="text-lg font-semibold {{ $result->masuk_ke_kas_masjid === 'Ya' ? 'text-green-700' : 'text-gray-800' }}">{{ $result->masuk_ke_kas_masjid }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Transaksi Kas Terkait</p>
            <p class="text-lg font-semibold text-gray-800">{{ $result->mosque_cash_transaction_id ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Akun Penerimaan Dana</p>
            <p class="text-lg font-semibold text-gray-800">{{ $result->cashAccount?->name ?? '-' }}</p>
        </div>
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-5">
        <p class="text-sm text-gray-500">Bukti File</p>
        @if($result->bukti_file)
            <a href="{{ asset('storage/'.$result->bukti_file) }}" target="_blank" class="mt-2 inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-900">Lihat bukti</a>
        @else
            <p class="mt-2 text-gray-800">-</p>
        @endif
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-5">
        <p class="text-sm text-gray-500">Keterangan</p>
        <p class="mt-2 text-gray-800">{{ $result->keterangan ?: '-' }}</p>
    </div>
</div>
@endsection
