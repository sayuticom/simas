@extends('layouts.admin')

@section('title', 'Dokumen Umum - SIMAS')
@section('page_title', 'Dokumen Umum')

@section('content')
@php
    $statusLabels = [
        'aktif' => 'Aktif',
        'arsip' => 'Arsip',
        'kedaluwarsa' => 'Kedaluwarsa',
    ];
    $statusClasses = [
        'aktif' => 'bg-green-100 text-green-700',
        'arsip' => 'bg-blue-100 text-blue-700',
        'kedaluwarsa' => 'bg-red-100 text-red-700',
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
            <h2 class="text-lg font-bold text-gray-800">Dokumen Umum</h2>
            <p class="text-sm text-gray-500">Kelola arsip dan file dokumen umum masjid aktif.</p>
        </div>
        <a href="{{ route('dokumen.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
            <i class="fas fa-plus"></i> Tambah Dokumen
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Judul</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jenis</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nomor</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal Dokumen</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal Berakhir</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Sumber</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($dokumens as $dokumen)
                    @php
                        $isExpired = $dokumen->tanggal_berakhir && $dokumen->tanggal_berakhir->isPast();
                    @endphp
                    <tr>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-800">{{ $dokumen->judul }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $dokumen->jenis_dokumen ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $dokumen->nomor_dokumen ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $dokumen->tanggal_dokumen?->format('d-m-Y') ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">
                            @if(! $dokumen->tanggal_berakhir)
                                <span class="text-gray-600">Tidak ada masa berlaku</span>
                            @elseif($isExpired)
                                <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">Kedaluwarsa</span>
                                <p class="mt-1 text-xs text-gray-500">{{ $dokumen->tanggal_berakhir->format('d-m-Y') }}</p>
                            @else
                                {{ $dokumen->tanggal_berakhir->format('d-m-Y') }}
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $dokumen->sumber ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$dokumen->status] ?? $statusClasses['aktif'] }}">
                                {{ $statusLabels[$dokumen->status] ?? 'Aktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right text-sm">
                            <div class="flex flex-wrap justify-end gap-3">
                                <a href="{{ route('dokumen.show', $dokumen) }}" class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                                <a href="{{ route('dokumen.edit', $dokumen) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                <form action="{{ route('dokumen.destroy', $dokumen) }}" method="POST" class="inline" onsubmit="return confirm('Hapus dokumen ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada dokumen umum.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $dokumens->links() }}
    </div>
</div>
@endsection
