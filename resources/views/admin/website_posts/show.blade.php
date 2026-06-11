@extends('layouts.admin')

@section('title', 'Detail Konten Website - SIMAS')
@section('page_title', 'Detail Konten Website')

@section('content')
@php
    $statusClasses = [
        'draft' => 'bg-gray-100 text-gray-700',
        'published' => 'bg-green-100 text-green-700',
        'archived' => 'bg-blue-100 text-blue-700',
    ];
@endphp

<div class="space-y-6">
    <div class="rounded-lg bg-white p-6 shadow">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.16em] text-amber-700">{{ $typeOptions[$post->type] ?? ucfirst($post->type) }}</p>
                <h2 class="mt-2 text-2xl font-bold text-gray-900">{{ $post->title }}</h2>
                <p class="mt-1 text-sm text-gray-600">Slug: {{ $post->slug }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('website-posts.edit', $post) }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-3 text-white hover:bg-green-700">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('website-posts.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Status</p>
            <span class="mt-2 inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $statusClasses[$post->status] ?? $statusClasses['draft'] }}">
                {{ $statusOptions[$post->status] ?? 'Draft' }}
            </span>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Tanggal Publikasi</p>
            <p class="mt-2 text-lg font-semibold text-gray-800">{{ $post->published_at?->format('d-m-Y H:i') ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Unggulan</p>
            <p class="mt-2 text-lg font-semibold text-gray-800">{{ $post->is_featured ? 'Ya' : 'Tidak' }}</p>
        </div>
    </div>

    @if($post->featured_image)
        <div class="rounded-lg bg-white p-6 shadow">
            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="max-h-80 w-full rounded-xl object-cover">
        </div>
    @endif

    @if($post->excerpt)
        <div class="rounded-lg bg-white p-6 shadow">
            <p class="text-sm text-gray-500">Ringkasan</p>
            <p class="mt-2 text-gray-800">{{ $post->excerpt }}</p>
        </div>
    @endif

    <div class="rounded-lg bg-white p-6 shadow">
        <p class="text-sm text-gray-500">Isi Konten</p>
        <p class="mt-2 whitespace-pre-line text-gray-800">{{ $post->content }}</p>
    </div>
</div>
@endsection
