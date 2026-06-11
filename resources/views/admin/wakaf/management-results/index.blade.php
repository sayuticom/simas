@extends('layouts.admin')

@section('title', 'Hasil Kelola Wakaf - SIMAS')
@section('page_title', 'Hasil Kelola Wakaf')

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
            <h2 class="text-lg font-bold text-gray-800">Hasil Kelola Wakaf</h2>
            <p class="text-sm text-gray-500">Catat penerimaan dari aset wakaf produktif.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('wakaf.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left"></i> Dashboard Wakaf
            </a>
            <a href="{{ route('wakaf.management-results.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                <i class="fas fa-plus"></i> Tambah Hasil Kelola
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aset Produktif</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jenis Hasil</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nominal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Periode</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pembayar</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Masuk Kas</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Akun Kas</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($results as $result)
                    <tr>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $result->tanggal_penerimaan?->format('d-m-Y') }}</td>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-800">{{ $result->productiveAsset?->wakafAsset?->nama_aset ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $result->jenis_hasil ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-800">Rp {{ number_format((float) $result->nominal, 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $result->periode ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $result->nama_pembayar ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $result->masuk_ke_kas_masjid === 'Ya' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $result->masuk_ke_kas_masjid }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $result->cashAccount?->name ?? '-' }}</td>
                        <td class="px-4 py-4 text-right text-sm">
                            <div class="flex flex-wrap justify-end gap-3">
                                <a href="{{ route('wakaf.management-results.show', $result) }}" class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                                <a href="{{ route('wakaf.management-results.edit', $result) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                <form action="{{ route('wakaf.management-results.destroy', $result) }}" method="POST" class="inline" onsubmit="return confirm('Hapus hasil kelola wakaf ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada data hasil kelola wakaf.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $results->links() }}
    </div>
</div>
@endsection
