@extends('layouts.admin')

@section('title', 'Detail Aset Produktif Wakaf - SIMAS')
@section('page_title', 'Detail Aset Produktif Wakaf')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $productiveAsset->wakafAsset?->nama_aset ?? 'Aset Produktif Wakaf' }}</h2>
            <p class="text-sm text-gray-500">Detail pengelolaan aset wakaf produktif.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('wakaf.productive-assets.edit', $productiveAsset) }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-3 text-white hover:bg-green-700">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('wakaf.productive-assets.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Aset Induk</p>
            <p class="text-lg font-semibold text-gray-800">{{ $productiveAsset->wakafAsset?->nama_aset ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Nazhir Aset</p>
            <p class="text-lg font-semibold text-gray-800">{{ $productiveAsset->wakafAsset?->nazhir?->nama ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Jenis Pengelolaan</p>
            <p class="text-lg font-semibold text-gray-800">{{ $productiveAsset->jenis_pengelolaan ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Penyewa / Mitra</p>
            <p class="text-lg font-semibold text-gray-800">{{ $productiveAsset->nama_penyewa_atau_mitra ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Target Pendapatan</p>
            <p class="text-lg font-semibold text-gray-800">Rp {{ number_format((float) $productiveAsset->target_pendapatan, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Periode Pendapatan</p>
            <p class="text-lg font-semibold text-gray-800">{{ $productiveAsset->periode_pendapatan ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Masa Kontrak</p>
            <p class="text-lg font-semibold text-gray-800">
                {{ $productiveAsset->tanggal_mulai_kontrak?->format('d-m-Y') ?? '-' }}
                -
                {{ $productiveAsset->tanggal_selesai_kontrak?->format('d-m-Y') ?? '-' }}
            </p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Status</p>
            <p class="text-lg font-semibold text-gray-800">{{ $productiveAsset->status ? ucfirst($productiveAsset->status) : '-' }}</p>
        </div>
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-5">
        <p class="text-sm text-gray-500">Detail Aset Induk</p>
        <p class="mt-2 text-gray-800">
            {{ $productiveAsset->wakafAsset?->jenis_aset ?: '-' }}
            @if($productiveAsset->wakafAsset?->lokasi)
                | {{ $productiveAsset->wakafAsset->lokasi }}
            @endif
            @if($productiveAsset->wakafAsset?->nilai_estimasi)
                | Rp {{ number_format((float) $productiveAsset->wakafAsset->nilai_estimasi, 0, ',', '.') }}
            @endif
        </p>
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-5">
        <p class="text-sm text-gray-500">Keterangan</p>
        <p class="mt-2 text-gray-800">{{ $productiveAsset->keterangan ?: '-' }}</p>
    </div>
</div>
@endsection
