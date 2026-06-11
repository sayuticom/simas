@extends('layouts.admin')

@section('title', 'Pengumuman - SIMAS')
@section('page_title', 'Pengumuman')

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

<div class="bg-white rounded-lg shadow p-6">
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3">{{ session('error') }}</div>
    @endif

    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Pengumuman Masjid</h2>
            <p class="text-sm text-gray-500">Kelola pengumuman internal dan publik untuk masjid aktif.</p>
        </div>
        <a href="{{ route('pengumuman.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
            <i class="fas fa-plus"></i> Tambah Pengumuman
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Judul</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kegiatan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Target Audiens</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal Mulai</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal Selesai</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Dashboard</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($pengumumans as $pengumuman)
                    <tr>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-800">{{ $pengumuman->judul }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $pengumuman->kegiatan?->nama_kegiatan ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $pengumuman->target_audiens ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $pengumuman->tanggal_mulai?->format('d-m-Y') ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $pengumuman->tanggal_selesai?->format('d-m-Y') ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$pengumuman->status] ?? $statusClasses['draft'] }}">
                                {{ $statusLabels[$pengumuman->status] ?? 'Draft' }}
                            </span>
                            <div class="mt-2 space-y-1 text-xs font-semibold text-slate-600">
                                <p>Publikasi: {{ $pengumuman->published_at?->format('d-m-Y H:i') ?? '-' }}</p>
                                <p>Slug: {{ $pengumuman->slug ?: '-' }}</p>
                                <p>Gambar: {{ $pengumuman->featured_image ? 'Ada' : '-' }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $pengumuman->tampil_di_dashboard ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $pengumuman->tampil_di_dashboard ? 'Ya' : 'Tidak' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right text-sm">
                            <div class="flex flex-wrap justify-end gap-3">
                                <a href="{{ route('pengumuman.show', $pengumuman) }}" class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                                <a href="{{ route('pengumuman.edit', $pengumuman) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                <form action="{{ route('pengumuman.destroy', $pengumuman) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pengumuman ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada pengumuman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $pengumumans->links() }}
    </div>
</div>
@endsection
