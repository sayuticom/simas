@extends('layouts.admin')

@section('title', 'Program Wakaf - SIMAS')
@section('page_title', 'Program Wakaf')

@section('content')
@php
    $statusClasses = [
        'aktif' => 'bg-green-100 text-green-700',
        'nonaktif' => 'bg-gray-100 text-gray-700',
        'selesai' => 'bg-blue-100 text-blue-700',
    ];
@endphp
<div class="bg-white rounded-lg shadow p-6">
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3">{{ session('error') }}</div>
    @endif

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Program Wakaf</h2>
            <p class="text-sm text-gray-500">Kelola program dan target dana wakaf untuk masjid aktif.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('wakaf.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left"></i> Dashboard Wakaf
            </a>
            <a href="{{ route('wakaf.programs.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                <i class="fas fa-plus"></i> Tambah Program
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama Program</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ringkasan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Target Dana</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($programs as $program)
                    <tr>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-800">{{ $program->nama }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700 max-w-md">
                            <p class="line-clamp-2">{{ $program->tujuan ?: $program->deskripsi ?: '-' }}</p>
                        </td>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-800">Rp {{ number_format((float) $program->target_dana, 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-sm">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$program->status] ?? $statusClasses['aktif'] }}">
                                {{ ucfirst($program->status ?: 'aktif') }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right text-sm">
                            <div class="flex flex-wrap justify-end gap-3">
                                <a href="{{ route('wakaf.programs.show', $program) }}" class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                                <a href="{{ route('wakaf.programs.edit', $program) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                <form action="{{ route('wakaf.programs.destroy', $program) }}" method="POST" class="inline" onsubmit="return confirm('Hapus Program Wakaf ini? Program yang sudah dipakai transaksi wakaf tunai tidak akan dapat dihapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada program wakaf.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $programs->links() }}
    </div>
</div>
@endsection
