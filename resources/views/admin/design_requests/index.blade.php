@extends('layouts.admin')

@section('title', 'Riwayat Prompt Desain - SIMAS')
@section('page_title', 'Riwayat Prompt Desain')

@section('content')
@php
    $statusOptions = \App\Models\DesignRequest::statusOptions();
    $sourceOptions = \App\Services\DesignPrompts\DesignPromptGenerator::sourceOptions();
@endphp
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="rounded-lg bg-white p-6 shadow">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Riwayat Prompt Desain</h2>
                <p class="mt-1 text-sm text-gray-600">Prompt final tersimpan sebagai riwayat dan tidak tampil ke publik.</p>
            </div>
            <a href="{{ route('design-requests.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
                <i class="fas fa-plus"></i> Buat Prompt
            </a>
        </div>
    </div>

    <div class="rounded-lg bg-white p-6 shadow">
        <form method="GET" action="{{ route('design-requests.index') }}" class="mb-5 grid gap-4 md:grid-cols-4">
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari judul prompt" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <select name="source_type" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="">Semua sumber</option>
                @foreach($sourceOptions as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['source_type'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="">Semua status</option>
                @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white">Filter</button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Prompt</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Sumber</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($designRequests as $requestItem)
                        <tr>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-gray-900">{{ $requestItem->title }}</p>
                                <p class="mt-1 text-xs font-semibold text-gray-600">{{ $requestItem->created_at?->format('d-m-Y H:i') }}</p>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $sourceOptions[$requestItem->source_type] ?? 'Umum' }} #{{ $requestItem->source_id ?: '-' }}</td>
                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">{{ $statusOptions[$requestItem->status] ?? $requestItem->status }}</span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('design-requests.show', $requestItem) }}" class="rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700">Lihat</a>
                                    <a href="{{ route('design-requests.edit', $requestItem) }}" class="rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-bold text-green-700">Edit</a>
                                    <form method="POST" action="{{ route('design-requests.destroy', $requestItem) }}" onsubmit="return confirm('Hapus prompt desain ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-bold text-red-700">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada prompt desain.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $designRequests->links() }}</div>
    </div>
</div>
@endsection
