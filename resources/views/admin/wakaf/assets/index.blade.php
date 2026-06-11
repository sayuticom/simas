@extends('layouts.admin')

@section('title', 'Aset Wakaf - SIMAS')
@section('page_title', 'Aset Wakaf')

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
            <h2 class="text-lg font-bold text-gray-800">Aset Wakaf</h2>
            <p class="text-sm text-gray-500">Kelola aset wakaf milik masjid aktif.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('wakaf.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left"></i> Dashboard Wakaf
            </a>
            <a href="{{ route('wakaf.assets.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                <i class="fas fa-plus"></i> Tambah Aset Wakaf
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama Aset</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jenis</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Sumber</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nazhir</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nilai Estimasi</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kondisi</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pemanfaatan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Produktif</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($assets as $asset)
                    <tr>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-800">{{ $asset->nama_aset }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $asset->jenis_aset ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">
                            {{ ['wakaf_tunai' => 'Wakaf Tunai', 'wakaf_non_tunai' => 'Wakaf Non-Tunai', 'lainnya' => 'Lainnya'][$asset->sumber_wakaf] ?? '-' }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $asset->nazhir?->nama ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-800">Rp {{ number_format((float) $asset->nilai_estimasi, 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $asset->kondisi ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $asset->status_pemanfaatan ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $asset->produktif ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $asset->produktif ? 'Ya' : 'Tidak' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right text-sm">
                            <div class="flex flex-wrap justify-end gap-3">
                                <a href="{{ route('wakaf.assets.show', $asset) }}" class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                                <a href="{{ route('wakaf.assets.edit', $asset) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                <form action="{{ route('wakaf.assets.destroy', $asset) }}" method="POST" class="inline" onsubmit="return confirm('Hapus aset wakaf ini? Data yang sudah memiliki turunan tidak akan dapat dihapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada data aset wakaf.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $assets->links() }}
    </div>
</div>
@endsection
