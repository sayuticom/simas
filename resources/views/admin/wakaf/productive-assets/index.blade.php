@extends('layouts.admin')

@section('title', 'Aset Produktif Wakaf - SIMAS')
@section('page_title', 'Aset Produktif Wakaf')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3">{{ session('error') }}</div>
    @endif

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Aset Produktif Wakaf</h2>
            <p class="text-sm text-gray-500">Kelola aset wakaf yang digunakan untuk menghasilkan pendapatan.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('wakaf.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left"></i> Dashboard Wakaf
            </a>
            <a href="{{ route('wakaf.productive-assets.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                <i class="fas fa-plus"></i> Tambah Aset Produktif
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama Aset</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jenis Pengelolaan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Mitra/Penyewa</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Target</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Periode</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Masa Kontrak</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($productiveAssets as $productiveAsset)
                    @php
                        $statusClass = match ($productiveAsset->status) {
                            'aktif' => 'bg-green-50 text-green-700',
                            'selesai' => 'bg-blue-50 text-blue-700',
                            'nonaktif' => 'bg-gray-100 text-gray-700',
                            default => 'bg-gray-100 text-gray-700',
                        };
                    @endphp
                    <tr>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-800">{{ $productiveAsset->wakafAsset?->nama_aset ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $productiveAsset->jenis_pengelolaan ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $productiveAsset->nama_penyewa_atau_mitra ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-800">Rp {{ number_format((float) $productiveAsset->target_pendapatan, 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $productiveAsset->periode_pendapatan ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">
                            {{ $productiveAsset->tanggal_mulai_kontrak?->format('d-m-Y') ?? '-' }}
                            -
                            {{ $productiveAsset->tanggal_selesai_kontrak?->format('d-m-Y') ?? '-' }}
                        </td>
                        <td class="px-4 py-4 text-sm">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                {{ $productiveAsset->status ? ucfirst($productiveAsset->status) : '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right text-sm">
                            <div class="flex flex-wrap justify-end gap-3">
                                <a href="{{ route('wakaf.productive-assets.show', $productiveAsset) }}" class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                                <a href="{{ route('wakaf.productive-assets.edit', $productiveAsset) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                <form action="{{ route('wakaf.productive-assets.destroy', $productiveAsset) }}" method="POST" class="inline" onsubmit="return confirm('Hapus aset produktif wakaf ini? Data yang sudah memiliki hasil kelola tidak akan dapat dihapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada data aset produktif wakaf.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $productiveAssets->links() }}
    </div>
</div>
@endsection
