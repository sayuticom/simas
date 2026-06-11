@extends('layouts.admin')

@section('title', 'Wakif - SIMAS')
@section('page_title', 'Data Wakif')

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
            <h2 class="text-lg font-bold text-gray-800">Data Wakif</h2>
            <p class="text-sm text-gray-500">Kelola pemberi wakaf untuk masjid aktif.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('wakaf.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left"></i> Dashboard Wakaf
            </a>
            <a href="{{ route('wakaf.wakifs.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                <i class="fas fa-plus"></i> Tambah Wakif
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jenis</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No. HP</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nomor Identitas</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($wakifs as $wakif)
                    <tr>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-800">{{ $wakif->nama }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">
                            @if($wakif->jenis_wakif)
                                <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">{{ ucfirst($wakif->jenis_wakif) }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $wakif->no_hp ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $wakif->nomor_identitas ?: '-' }}</td>
                        <td class="px-4 py-4 text-right text-sm">
                            <div class="flex flex-wrap justify-end gap-3">
                                <a href="{{ route('wakaf.wakifs.show', $wakif) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                <a href="{{ route('wakaf.wakifs.edit', $wakif) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                <form action="{{ route('wakaf.wakifs.destroy', $wakif) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data Wakif ini? Data yang sudah dipakai transaksi tidak akan dapat dihapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada data wakif.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $wakifs->links() }}
    </div>
</div>
@endsection
