@extends('layouts.admin')

@section('title', 'Detail Inventaris - SIMAS')
@section('page_title', 'Detail Inventaris')

@section('content')
@php
    $statusLabels = [
        'aktif' => 'Aktif',
        'nonaktif' => 'Nonaktif',
        'dipinjam' => 'Dipinjam',
        'hilang' => 'Hilang',
        'dihapus' => 'Dihapus',
    ];
    $conditionLabels = [
        'baik' => 'Baik',
        'rusak_ringan' => 'Rusak Ringan',
        'rusak_berat' => 'Rusak Berat',
        'hilang' => 'Hilang',
    ];
@endphp

<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $inventaris->nama_barang }}</h2>
                <p class="text-sm text-gray-500">{{ $inventaris->kode_barang ?: 'Tanpa kode barang' }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('inventaris.edit', $inventaris) }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-3 text-white hover:bg-green-700">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('inventaris.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    @if($inventaris->foto)
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500">Foto</p>
            <img src="{{ asset('storage/'.$inventaris->foto) }}" alt="{{ $inventaris->nama_barang }}" class="mt-3 max-h-80 rounded-lg border object-contain">
            <a href="{{ asset('storage/'.$inventaris->foto) }}" target="_blank" class="mt-3 inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-900">Buka file foto</a>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Kategori</p>
            <p class="text-lg font-semibold text-gray-800">{{ $inventaris->kategori ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Merk / Tipe Model</p>
            <p class="text-lg font-semibold text-gray-800">{{ trim(($inventaris->merk ?: '').' '.($inventaris->tipe_model ?: '')) ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Jumlah</p>
            <p class="text-lg font-semibold text-gray-800">{{ number_format($inventaris->jumlah) }} {{ $inventaris->satuan }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Kondisi</p>
            <p class="text-lg font-semibold text-gray-800">{{ $conditionLabels[$inventaris->kondisi] ?? 'Baik' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Lokasi</p>
            <p class="text-lg font-semibold text-gray-800">{{ $inventaris->lokasi ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Status</p>
            <p class="text-lg font-semibold text-gray-800">{{ $statusLabels[$inventaris->status] ?? 'Aktif' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Tanggal Perolehan</p>
            <p class="text-lg font-semibold text-gray-800">{{ $inventaris->tanggal_perolehan?->format('d-m-Y') ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Sumber Perolehan</p>
            <p class="text-lg font-semibold text-gray-800">{{ $inventaris->sumber_perolehan ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Nilai Perolehan</p>
            <p class="text-lg font-semibold text-gray-800">Rp {{ number_format((float) $inventaris->nilai_perolehan, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Penanggung Jawab</p>
            <p class="text-lg font-semibold text-gray-800">{{ $inventaris->penanggung_jawab ?: '-' }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-500">Keterangan</p>
        <p class="mt-2 whitespace-pre-line text-gray-800">{{ $inventaris->keterangan ?: '-' }}</p>
    </div>
</div>
@endsection
