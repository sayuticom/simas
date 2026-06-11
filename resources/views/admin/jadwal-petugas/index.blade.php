@extends('layouts.admin')

@section('title', 'Jadwal Petugas - SIMAS')
@section('page_title', 'Jadwal Petugas')

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

<div class="bg-white rounded-lg shadow p-6">
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3">{{ session('error') }}</div>
    @endif

    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Jadwal Petugas</h2>
            <p class="text-sm text-gray-500">Kelola jadwal imam, khatib, muadzin, bilal, dan petugas masjid.</p>
        </div>
        <a href="{{ route('jadwal-petugas.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
            <i class="fas fa-plus"></i> Tambah Jadwal
        </a>
    </div>

    <form method="GET" action="{{ route('jadwal-petugas.index') }}" class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            <div>
                <label for="tanggal_awal" class="mb-1 block text-sm font-medium text-gray-700">Tanggal Awal</label>
                <input type="date" id="tanggal_awal" name="tanggal_awal" value="{{ $filters['tanggal_awal'] ?? '' }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="tanggal_akhir" class="mb-1 block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                <input type="date" id="tanggal_akhir" name="tanggal_akhir" value="{{ $filters['tanggal_akhir'] ?? '' }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="status" class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                <select id="status" name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    @foreach($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="kegiatan_id" class="mb-1 block text-sm font-medium text-gray-700">Kegiatan</label>
                <select id="kegiatan_id" name="kegiatan_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua Kegiatan</option>
                    @foreach($kegiatans as $kegiatan)
                        <option value="{{ $kegiatan->id }}" @selected((string) ($filters['kegiatan_id'] ?? '') === (string) $kegiatan->id)>{{ $kegiatan->nama_kegiatan }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="q" class="mb-1 block text-sm font-medium text-gray-700">Cari</label>
                <input type="text" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Petugas, tugas, lokasi" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>
        <div class="mt-4 flex flex-wrap items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                <i class="fas fa-filter"></i> Terapkan Filter
            </button>
            <a href="{{ route('jadwal-petugas.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                <i class="fas fa-rotate-left"></i> Reset
            </a>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jenis Tugas</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama Petugas</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kegiatan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Lokasi</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($jadwalPetugas as $jadwal)
                    <tr>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $jadwal->tanggal?->format('d-m-Y') ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">
                            {{ $jadwal->waktu_mulai ? substr($jadwal->waktu_mulai, 0, 5) : '-' }}
                            @if($jadwal->waktu_selesai)
                                - {{ substr($jadwal->waktu_selesai, 0, 5) }}
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-800">{{ $jadwal->jenis_tugas }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $jadwal->nama_petugas_label }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $jadwal->kegiatan?->nama_kegiatan ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $jadwal->lokasi ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$jadwal->status] ?? $statusClasses['terjadwal'] }}">
                                {{ $statusLabels[$jadwal->status] ?? 'Terjadwal' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right text-sm">
                            <div class="flex flex-wrap justify-end gap-3">
                                <a href="{{ route('jadwal-petugas.show', $jadwal) }}" class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                                <a href="{{ route('jadwal-petugas.edit', $jadwal) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                <form action="{{ route('jadwal-petugas.destroy', $jadwal) }}" method="POST" class="inline" onsubmit="return confirm('Hapus jadwal petugas ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada jadwal petugas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $jadwalPetugas->links() }}
    </div>
</div>
@endsection
