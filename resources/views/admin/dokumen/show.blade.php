@extends('layouts.admin')

@section('title', 'Detail Dokumen - SIMAS')
@section('page_title', 'Detail Dokumen')

@section('content')
@php
    $statusLabels = [
        'aktif' => 'Aktif',
        'arsip' => 'Arsip',
        'kedaluwarsa' => 'Kedaluwarsa',
    ];
    $isExpired = $dokumen->tanggal_berakhir && $dokumen->tanggal_berakhir->isPast();
@endphp

<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $dokumen->judul }}</h2>
                <p class="text-sm text-gray-500">{{ $dokumen->jenis_dokumen ?: 'Dokumen Umum' }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('dokumen.edit', $dokumen) }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-3 text-white hover:bg-green-700">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('dokumen.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Nomor Dokumen</p>
            <p class="text-lg font-semibold text-gray-800">{{ $dokumen->nomor_dokumen ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Status</p>
            <p class="text-lg font-semibold text-gray-800">{{ $statusLabels[$dokumen->status] ?? 'Aktif' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Tanggal Dokumen</p>
            <p class="text-lg font-semibold text-gray-800">{{ $dokumen->tanggal_dokumen?->format('d-m-Y') ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Tanggal Berakhir</p>
            @if(! $dokumen->tanggal_berakhir)
                <p class="text-lg font-semibold text-gray-800">Tidak ada masa berlaku</p>
            @elseif($isExpired)
                <p class="text-lg font-semibold text-red-700">Kedaluwarsa - {{ $dokumen->tanggal_berakhir->format('d-m-Y') }}</p>
            @else
                <p class="text-lg font-semibold text-gray-800">{{ $dokumen->tanggal_berakhir->format('d-m-Y') }}</p>
            @endif
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Sumber</p>
            <p class="text-lg font-semibold text-gray-800">{{ $dokumen->sumber ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">File Dokumen</p>
            @if($dokumen->file_dokumen)
                <a href="{{ asset('storage/'.$dokumen->file_dokumen) }}" target="_blank" class="mt-2 inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-900">Lihat dokumen</a>
            @else
                <p class="text-lg font-semibold text-gray-800">-</p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-500">Keterangan</p>
        <p class="mt-2 whitespace-pre-line text-gray-800">{{ $dokumen->keterangan ?: '-' }}</p>
    </div>
</div>
@endsection
