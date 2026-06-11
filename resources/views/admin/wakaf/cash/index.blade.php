@extends('layouts.admin')

@section('title', 'Wakaf Tunai - SIMAS')
@section('page_title', 'Wakaf Tunai')

@section('content')
@php
    $statusClasses = [
        'tercatat' => 'bg-indigo-100 text-indigo-700',
        'diverifikasi' => 'bg-green-100 text-green-700',
        'batal' => 'bg-red-50 text-red-700',
    ];
    $paymentLabels = [
        'tunai' => 'Tunai',
        'transfer' => 'Transfer Bank',
        'qris' => 'QRIS',
        'ewallet' => 'E-Wallet',
        'lainnya' => 'Lainnya',
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
            <h2 class="text-lg font-bold text-gray-800">Wakaf Tunai</h2>
            <p class="text-sm text-gray-500">Catat penerimaan wakaf tunai untuk masjid aktif.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('wakaf.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left"></i> Dashboard Wakaf
            </a>
            <a href="{{ route('wakaf.cash.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                <i class="fas fa-plus"></i> Tambah Wakaf Tunai
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Wakif</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nazhir</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Program</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nominal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Metode</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Akun Penerimaan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($cashRecords as $cash)
                    <tr>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $cash->tanggal_terima?->format('d-m-Y') }}</td>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-800">{{ $cash->wakif?->nama ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $cash->nazhir?->nama ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $cash->program?->nama ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-800">Rp {{ number_format((float) $cash->nominal, 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $paymentLabels[$cash->metode_pembayaran] ?? ($cash->metode_pembayaran ?: '-') }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $cash->cashAccount ? $cash->cashAccount->name.' - '.$cash->cashAccount->accountTypeLabel() : '-' }}</td>
                        <td class="px-4 py-4 text-sm">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$cash->status] ?? $statusClasses['tercatat'] }}">
                                {{ ucfirst($cash->status ?: 'tercatat') }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right text-sm">
                            <div class="flex flex-wrap justify-end gap-3">
                                <a href="{{ route('wakaf.cash.show', $cash) }}" class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                                <a href="{{ route('wakaf.cash.receipt', $cash) }}" class="text-gray-700 hover:text-gray-900">Cetak Bukti</a>
                                <a href="{{ route('wakaf.cash.edit', $cash) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                <form action="{{ route('wakaf.cash.destroy', $cash) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data Wakaf Tunai ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada data wakaf tunai.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $cashRecords->links() }}
    </div>
</div>
@endsection
