@extends('layouts.admin')

@section('title', 'Detail Program Wakaf - SIMAS')
@section('page_title', 'Detail Program Wakaf')

@section('content')
@php
    $statusClasses = [
        'aktif' => 'bg-green-100 text-green-700',
        'nonaktif' => 'bg-gray-100 text-gray-700',
        'selesai' => 'bg-blue-100 text-blue-700',
    ];
@endphp
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $program->nama }}</h2>
            <p class="text-sm text-gray-500">Detail program wakaf.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('wakaf.programs.edit', $program) }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-3 text-white hover:bg-green-700">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('wakaf.programs.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Nama Program</p>
            <p class="text-lg font-semibold text-gray-800">{{ $program->nama }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Target Dana</p>
            <p class="text-lg font-semibold text-gray-800">Rp {{ number_format((float) $program->target_dana, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Status</p>
            <span class="mt-2 inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $statusClasses[$program->status] ?? $statusClasses['aktif'] }}">
                {{ ucfirst($program->status ?: 'aktif') }}
            </span>
        </div>
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-5">
        <p class="text-sm text-gray-500">Tujuan</p>
        <p class="mt-2 text-gray-800">{{ $program->tujuan ?: '-' }}</p>
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-5">
        <p class="text-sm text-gray-500">Deskripsi</p>
        <p class="mt-2 text-gray-800">{{ $program->deskripsi ?: '-' }}</p>
    </div>
</div>
@endsection
