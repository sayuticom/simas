@extends('layouts.admin')

@section('title', 'Detail Wakif - SIMAS')
@section('page_title', 'Detail Wakif')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $wakif->nama }}</h2>
            <p class="text-sm text-gray-500">Detail lengkap data pemberi wakaf.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('wakaf.wakifs.edit', $wakif) }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-3 text-white hover:bg-green-700">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('wakaf.wakifs.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Nama</p>
            <p class="text-lg font-semibold text-gray-800">{{ $wakif->nama }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Jenis Wakif</p>
            <p class="text-lg font-semibold text-gray-800">{{ $wakif->jenis_wakif ? ucfirst($wakif->jenis_wakif) : '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">No. HP</p>
            <p class="text-lg font-semibold text-gray-800">{{ $wakif->no_hp ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Nomor Identitas</p>
            <p class="text-lg font-semibold text-gray-800">{{ $wakif->nomor_identitas ?: '-' }}</p>
        </div>
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-5">
        <p class="text-sm text-gray-500">Alamat</p>
        <p class="mt-2 text-gray-800">{{ $wakif->alamat ?: '-' }}</p>
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-5">
        <p class="text-sm text-gray-500">Keterangan</p>
        <p class="mt-2 text-gray-800">{{ $wakif->keterangan ?: '-' }}</p>
    </div>
</div>
@endsection
