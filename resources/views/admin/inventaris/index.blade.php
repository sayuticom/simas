@extends('layouts.admin')

@section('title', 'Inventaris - SIMAS')
@section('page_title', 'Inventaris')

@section('content')
@php
    $statusLabels = [
        'aktif' => 'Aktif',
        'nonaktif' => 'Nonaktif',
        'dipinjam' => 'Dipinjam',
        'hilang' => 'Hilang',
        'dihapus' => 'Dihapus',
    ];
    $statusClasses = [
        'aktif' => 'bg-green-100 text-green-700',
        'nonaktif' => 'bg-gray-100 text-gray-700',
        'dipinjam' => 'bg-blue-100 text-blue-700',
        'hilang' => 'bg-red-100 text-red-700',
        'dihapus' => 'bg-slate-100 text-slate-700',
    ];
    $conditionLabels = [
        'baik' => 'Baik',
        'rusak_ringan' => 'Rusak Ringan',
        'rusak_berat' => 'Rusak Berat',
        'hilang' => 'Hilang',
    ];
    $conditionClasses = [
        'baik' => 'bg-green-100 text-green-700',
        'rusak_ringan' => 'bg-amber-100 text-amber-700',
        'rusak_berat' => 'bg-orange-100 text-orange-700',
        'hilang' => 'bg-red-100 text-red-700',
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
            <h2 class="text-lg font-bold text-gray-800">Inventaris Masjid</h2>
            <p class="text-sm text-gray-500">Kelola barang dan aset milik masjid aktif.</p>
        </div>
        <a href="{{ route('inventaris.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
            <i class="fas fa-plus"></i> Tambah Inventaris
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kode</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama Barang</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jumlah</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kondisi</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Lokasi</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Penanggung Jawab</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($inventaris as $item)
                    <tr>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $item->kode_barang ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-800">{{ $item->nama_barang }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $item->kategori ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ number_format($item->jumlah) }} {{ $item->satuan }}</td>
                        <td class="px-4 py-4 text-sm">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $conditionClasses[$item->kondisi] ?? $conditionClasses['baik'] }}">
                                {{ $conditionLabels[$item->kondisi] ?? 'Baik' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $item->lokasi ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $item->penanggung_jawab ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$item->status] ?? $statusClasses['aktif'] }}">
                                {{ $statusLabels[$item->status] ?? 'Aktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right text-sm">
                            <div class="flex flex-wrap justify-end gap-3">
                                <a href="{{ route('inventaris.show', $item) }}" class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                                <a href="{{ route('inventaris.edit', $item) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                <form action="{{ route('inventaris.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Hapus inventaris ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada inventaris.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $inventaris->links() }}
    </div>
</div>
@endsection
