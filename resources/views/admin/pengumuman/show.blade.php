@extends('layouts.admin')

@section('title', 'Detail Pengumuman - SIMAS')
@section('page_title', 'Detail Pengumuman')

@section('content')
@php
    $statusLabels = [
        'draft' => 'Draft',
        'terbit' => 'Terbit',
        'arsip' => 'Arsip',
    ];
    $statusClasses = [
        'draft' => 'bg-gray-100 text-gray-700',
        'terbit' => 'bg-green-100 text-green-700',
        'arsip' => 'bg-blue-100 text-blue-700',
    ];
@endphp

<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $pengumuman->judul }}</h2>
                <p class="text-sm text-gray-500">{{ $pengumuman->target_audiens ?: 'Pengumuman Masjid' }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('pengumuman.edit', $pengumuman) }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-3 text-white hover:bg-green-700">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('pengumuman.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Kegiatan</p>
            <p class="text-lg font-semibold text-gray-800">{{ $pengumuman->kegiatan?->nama_kegiatan ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Status</p>
            <span class="mt-2 inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $statusClasses[$pengumuman->status] ?? $statusClasses['draft'] }}">
                {{ $statusLabels[$pengumuman->status] ?? 'Draft' }}
            </span>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Tanggal Mulai</p>
            <p class="text-lg font-semibold text-gray-800">{{ $pengumuman->tanggal_mulai?->format('d-m-Y') ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Tanggal Selesai</p>
            <p class="text-lg font-semibold text-gray-800">{{ $pengumuman->tanggal_selesai?->format('d-m-Y') ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Tampil di Dashboard</p>
            <p class="text-lg font-semibold text-gray-800">{{ $pengumuman->tampil_di_dashboard ? 'Ya' : 'Tidak' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Dibuat oleh</p>
            <p class="text-lg font-semibold text-gray-800">{{ $pengumuman->pembuat?->name ?? '-' }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-500">Isi Pengumuman</p>
        <p class="mt-2 whitespace-pre-line text-gray-800">{{ $pengumuman->isi }}</p>
    </div>
</div>
@endsection
