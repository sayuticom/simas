@extends('layouts.admin')

@section('title', 'Dokumen Wakaf - SIMAS')
@section('page_title', 'Dokumen Wakaf')

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
            <h2 class="text-lg font-bold text-gray-800">Dokumen Wakaf</h2>
            <p class="text-sm text-gray-500">Kelola dokumen aset wakaf masjid aktif.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('wakaf.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left"></i> Dashboard Wakaf
            </a>
            <a href="{{ route('wakaf.documents.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                <i class="fas fa-plus"></i> Tambah Dokumen
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aset</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jenis Dokumen</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nomor</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Terbit</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Berakhir</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($documents as $document)
                    @php
                        $isExpired = $document->tanggal_berakhir && $document->tanggal_berakhir->isPast();
                    @endphp
                    <tr>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-800">{{ $document->wakafAsset?->nama_aset ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $document->jenis_dokumen ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $document->nomor_dokumen ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $document->tanggal_terbit?->format('d-m-Y') ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $document->tanggal_berakhir?->format('d-m-Y') ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm">
                            @if(! $document->tanggal_berakhir)
                                <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">Tidak ada masa berlaku</span>
                            @elseif($isExpired)
                                <span class="inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">Kedaluwarsa</span>
                            @else
                                <span class="inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">Aktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right text-sm">
                            <div class="flex flex-wrap justify-end gap-3">
                                <a href="{{ route('wakaf.documents.show', $document) }}" class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                                <a href="{{ route('wakaf.documents.edit', $document) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                <form action="{{ route('wakaf.documents.destroy', $document) }}" method="POST" class="inline" onsubmit="return confirm('Hapus dokumen wakaf ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada dokumen wakaf.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $documents->links() }}
    </div>
</div>
@endsection
