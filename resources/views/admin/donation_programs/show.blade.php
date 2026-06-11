@extends('layouts.admin')

@section('title', 'Detail Program Donasi - SIMAS')
@section('page_title', 'Detail Program Donasi')

@section('content')
@php
    $progress = $program->progressPercentage();
@endphp

<div class="space-y-6">
    <div class="rounded-lg bg-white p-6 shadow">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-amber-700">{{ $program->category ?: 'Program Donasi' }}</p>
                <h2 class="mt-2 text-2xl font-bold text-gray-900">{{ $program->title }}</h2>
                <p class="mt-1 text-sm text-gray-600">Slug: {{ $program->slug }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('donation-programs.edit', $program) }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-3 text-white hover:bg-green-700">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('donation-programs.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Target Dana</p>
            <p class="mt-2 text-lg font-semibold text-gray-800">{{ $program->target_amount ? 'Rp '.number_format((float) $program->target_amount, 0, ',', '.') : '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Dana Terkumpul</p>
            <p class="mt-2 text-lg font-semibold text-gray-800">Rp {{ number_format((float) $program->collected_amount, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Progress</p>
            <div class="mt-3 h-3 rounded-full bg-gray-100">
                <div class="h-3 rounded-full bg-emerald-700" style="width: {{ $progress }}%"></div>
            </div>
            <p class="mt-2 text-sm font-bold text-emerald-900">{{ $progress }}%</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        @if($program->featured_image)
            <div class="rounded-lg bg-white p-6 shadow">
                <p class="mb-3 text-sm font-bold text-gray-500">Gambar Program</p>
                <img src="{{ asset('storage/' . $program->featured_image) }}" alt="{{ $program->title }}" class="max-h-80 w-full rounded-xl object-cover">
            </div>
        @endif
        @if($program->qris_image)
            <div class="rounded-lg bg-white p-6 shadow">
                <p class="mb-3 text-sm font-bold text-gray-500">QRIS Manual</p>
                <img src="{{ asset('storage/' . $program->qris_image) }}" alt="QRIS {{ $program->title }}" class="max-h-80 w-full rounded-xl object-contain">
            </div>
        @endif
    </div>

    <div class="rounded-lg bg-white p-6 shadow">
        <p class="text-sm text-gray-500">Deskripsi</p>
        <p class="mt-2 whitespace-pre-line text-gray-800">{{ $program->description }}</p>
    </div>

    @include('admin.design_requests._source_card', [
        'sourceType' => 'donasi',
        'sourceId' => $program->id,
        'existingDesignRequest' => $existingDesignRequest ?? null,
        'returnUrl' => route('donation-programs.show', $program, false),
    ])
</div>
@endsection
