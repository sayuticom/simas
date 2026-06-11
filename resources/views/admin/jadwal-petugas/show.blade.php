@extends('layouts.admin')

@section('title', 'Detail Jadwal Petugas - SIMAS')
@section('page_title', 'Detail Jadwal Petugas')

@section('content')
@php
    $statusLabels = [
        'terjadwal' => 'Terjadwal',
        'hadir' => 'Hadir',
        'berhalangan' => 'Berhalangan',
        'selesai' => 'Selesai',
        'batal' => 'Batal',
    ];
    $statusClasses = [
        'terjadwal' => 'bg-blue-100 text-blue-700',
        'hadir' => 'bg-green-100 text-green-700',
        'berhalangan' => 'bg-amber-100 text-amber-700',
        'selesai' => 'bg-gray-100 text-gray-700',
        'batal' => 'bg-red-100 text-red-700',
    ];
@endphp

<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $jadwalPetugas->jenis_tugas }}</h2>
                <p class="text-sm text-gray-500">{{ $jadwalPetugas->nama_petugas_label }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('jadwal-petugas.edit', $jadwalPetugas) }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-3 text-white hover:bg-green-700">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('jadwal-petugas.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Tanggal</p>
            <p class="text-lg font-semibold text-gray-800">{{ $jadwalPetugas->tanggal?->format('d-m-Y') ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Waktu</p>
            <p class="text-lg font-semibold text-gray-800">
                {{ $jadwalPetugas->waktu_mulai ? substr($jadwalPetugas->waktu_mulai, 0, 5) : '-' }}
                @if($jadwalPetugas->waktu_selesai)
                    - {{ substr($jadwalPetugas->waktu_selesai, 0, 5) }}
                @endif
            </p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Petugas</p>
            <p class="text-lg font-semibold text-gray-800">{{ $jadwalPetugas->nama_petugas_label }}</p>
            <p class="text-sm text-gray-500">{{ $jadwalPetugas->user?->email ?? 'Manual' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Status</p>
            <span class="mt-2 inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $statusClasses[$jadwalPetugas->status] ?? $statusClasses['terjadwal'] }}">
                {{ $statusLabels[$jadwalPetugas->status] ?? 'Terjadwal' }}
            </span>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Kegiatan</p>
            <p class="text-lg font-semibold text-gray-800">{{ $jadwalPetugas->kegiatan?->nama_kegiatan ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Lokasi</p>
            <p class="text-lg font-semibold text-gray-800">{{ $jadwalPetugas->lokasi ?: '-' }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-500">Keterangan</p>
        <p class="mt-2 whitespace-pre-line text-gray-800">{{ $jadwalPetugas->keterangan ?: '-' }}</p>
    </div>
</div>
@endsection
