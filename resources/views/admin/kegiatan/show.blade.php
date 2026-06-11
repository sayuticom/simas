@extends('layouts.admin')

@section('title', 'Detail Kegiatan - SIMAS')
@section('page_title', 'Detail Kegiatan')

@section('content')
@php
    $statusLabels = [
        'terencana' => 'Terencana',
        'berjalan' => 'Berjalan',
        'selesai' => 'Selesai',
        'batal' => 'Batal',
    ];
    $statusClasses = [
        'terencana' => 'bg-blue-100 text-blue-700',
        'berjalan' => 'bg-amber-100 text-amber-700',
        'selesai' => 'bg-green-100 text-green-700',
        'batal' => 'bg-red-100 text-red-700',
    ];
    $jadwalStatusLabels = [
        'terjadwal' => 'Terjadwal',
        'hadir' => 'Hadir',
        'berhalangan' => 'Berhalangan',
        'selesai' => 'Selesai',
        'batal' => 'Batal',
    ];
    $jadwalStatusClasses = [
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
                <h2 class="text-xl font-bold text-gray-800">{{ $kegiatan->nama_kegiatan }}</h2>
                <p class="text-sm text-gray-500">{{ $kegiatan->jenis_kegiatan ?: 'Kegiatan Masjid' }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('kegiatan.edit', $kegiatan) }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-3 text-white hover:bg-green-700">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('kegiatan.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Tanggal Mulai</p>
            <p class="text-lg font-semibold text-gray-800">{{ $kegiatan->tanggal_mulai?->format('d-m-Y H:i') ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Tanggal Selesai</p>
            <p class="text-lg font-semibold text-gray-800">{{ $kegiatan->tanggal_selesai?->format('d-m-Y H:i') ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Lokasi</p>
            <p class="text-lg font-semibold text-gray-800">{{ $kegiatan->lokasi ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Status</p>
            <span class="mt-2 inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $statusClasses[$kegiatan->status] ?? $statusClasses['terencana'] }}">
                {{ $statusLabels[$kegiatan->status] ?? 'Terencana' }}
            </span>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Penanggung Jawab</p>
            <p class="text-lg font-semibold text-gray-800">{{ $kegiatan->penanggung_jawab ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Narasumber</p>
            <p class="text-lg font-semibold text-gray-800">{{ $kegiatan->narasumber ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <p class="text-sm text-gray-500">Target Peserta</p>
            <p class="text-lg font-semibold text-gray-800">{{ $kegiatan->target_peserta ?: '-' }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-500">Deskripsi</p>
        <p class="mt-2 whitespace-pre-line text-gray-800">{{ $kegiatan->deskripsi ?: '-' }}</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Jadwal Petugas Kegiatan Ini</h3>
                <p class="text-sm text-gray-500">Daftar petugas yang terhubung dengan kegiatan ini.</p>
            </div>
            <a href="{{ route('jadwal-petugas.create', ['kegiatan_id' => $kegiatan->id]) }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                <i class="fas fa-plus"></i> Tambah Petugas untuk Kegiatan Ini
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Jenis Tugas</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Nama Petugas</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Waktu</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($kegiatan->jadwalPetugas as $jadwal)
                        <tr>
                            <td class="px-4 py-4 text-sm font-semibold text-gray-800">{{ $jadwal->jenis_tugas }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $jadwal->nama_petugas_label }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $jadwal->tanggal?->format('d-m-Y') ?? '-' }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">
                                {{ $jadwal->waktu_mulai ? substr($jadwal->waktu_mulai, 0, 5) : '-' }}
                                @if($jadwal->waktu_selesai)
                                    - {{ substr($jadwal->waktu_selesai, 0, 5) }}
                                @endif
                            </td>
                            <td class="px-4 py-4 text-sm">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $jadwalStatusClasses[$jadwal->status] ?? $jadwalStatusClasses['terjadwal'] }}">
                                    {{ $jadwalStatusLabels[$jadwal->status] ?? 'Terjadwal' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right text-sm">
                                <div class="flex flex-wrap justify-end gap-3">
                                    <a href="{{ route('jadwal-petugas.show', $jadwal) }}" class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                                    <a href="{{ route('jadwal-petugas.edit', $jadwal) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada jadwal petugas untuk kegiatan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-sm text-gray-500">Catatan</p>
        <p class="mt-2 whitespace-pre-line text-gray-800">{{ $kegiatan->catatan ?: '-' }}</p>
    </div>

    @include('admin.design_requests._source_card', [
        'sourceType' => 'kegiatan',
        'sourceId' => $kegiatan->id,
        'existingDesignRequest' => $existingDesignRequest ?? null,
        'returnUrl' => route('kegiatan.show', $kegiatan, false),
    ])
</div>
@endsection
