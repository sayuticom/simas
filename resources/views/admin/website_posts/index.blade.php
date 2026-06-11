@extends('layouts.admin')

@section('title', 'Konten Website - SIMAS')
@section('page_title', 'Konten Website')

@section('content')
@php
    $statusClasses = [
        'draft' => 'bg-gray-100 text-gray-700',
        'published' => 'bg-green-100 text-green-700',
        'archived' => 'bg-blue-100 text-blue-700',
    ];
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="rounded-lg bg-white p-6 shadow">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Konten Website</h2>
                <p class="mt-1 text-sm text-gray-600">Kelola Berita, Artikel, dan Informasi untuk website publik masjid.</p>
            </div>
            <a href="{{ route('website-posts.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
                <i class="fas fa-plus"></i> Tambah Konten
            </a>
        </div>
    </div>

    <div class="rounded-lg bg-white p-6 shadow">
        <form method="GET" action="{{ route('website-posts.index') }}" class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label for="q" class="mb-1 block text-sm font-semibold text-gray-700">Cari Judul</label>
                    <input type="text" id="q" name="q" value="{{ $filters['q'] ?? '' }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Masukkan judul konten">
                </div>
                <div>
                    <label for="type" class="mb-1 block text-sm font-semibold text-gray-700">Jenis Konten</label>
                    <select id="type" name="type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua Jenis</option>
                        @foreach($typeOptions as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['type'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="mb-1 block text-sm font-semibold text-gray-700">Status</label>
                    <select id="status" name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua Status</option>
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap gap-3">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                    <i class="fas fa-filter"></i> Terapkan Filter
                </button>
                <a href="{{ route('website-posts.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-rotate-left"></i> Reset
                </a>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Konten</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Jenis</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Publikasi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Unggulan</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($posts as $post)
                        <tr>
                            <td class="px-4 py-4">
                                <div class="flex items-start gap-3">
                                    @if($post->featured_image)
                                        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="h-14 w-20 rounded-lg object-cover">
                                    @else
                                        <div class="flex h-14 w-20 items-center justify-center rounded-lg bg-emerald-950 text-xs font-bold text-amber-100">Konten</div>
                                    @endif
                                    <div>
                                        <p class="font-semibold leading-snug text-gray-900">{{ $post->title }}</p>
                                        <p class="mt-1 text-xs font-semibold text-gray-600">Slug: {{ $post->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $typeOptions[$post->type] ?? ucfirst($post->type) }}</td>
                            <td class="px-4 py-4 text-sm">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$post->status] ?? $statusClasses['draft'] }}">
                                    {{ $statusOptions[$post->status] ?? 'Draft' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $post->published_at?->format('d-m-Y H:i') ?? '-' }}</td>
                            <td class="px-4 py-4 text-sm">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $post->is_featured ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $post->is_featured ? 'Ya' : 'Tidak' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right text-sm">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('website-posts.show', $post) }}" class="inline-flex rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700 hover:bg-indigo-100">Lihat</a>
                                    <a href="{{ route('website-posts.edit', $post) }}" class="inline-flex rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-bold text-green-700 hover:bg-green-100">Edit</a>
                                    <form action="{{ route('website-posts.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('Hapus konten ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-bold text-red-700 hover:bg-red-100">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada konten website.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $posts->links() }}
        </div>
    </div>
</div>
@endsection
