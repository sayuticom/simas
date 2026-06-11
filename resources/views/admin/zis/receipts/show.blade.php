@extends('layouts.admin')

@section('title', 'Detail Penerimaan ZIS - SIMAS')
@section('page_title', 'Detail Penerimaan ZIS')

@section('content')
@php
    $receiptAmount = $receipt->amount ?? $receipt->nominal_uang ?? 0;
    $distributedAmount = $receipt->distributed_amount ?? 0;
    $remainingAmount = max($receiptAmount - $distributedAmount, 0);
    $status = $receipt->distributionStatus($distributedAmount);
    $statusClass = match ($status) {
        'Sudah Disalurkan' => 'bg-green-100 text-green-700',
        'Sebagian' => 'bg-yellow-100 text-yellow-700',
        default => 'bg-gray-100 text-gray-700',
    };
    $proofFile = $receipt->proof_file ?? $receipt->bukti_file;
@endphp
<div class="bg-white rounded-lg shadow p-6">
    @if(session('error'))
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3">{{ session('error') }}</div>
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-lg bg-gray-50 p-4"><p class="text-sm text-gray-500">Tanggal</p><p class="font-semibold">{{ $receipt->receipt_date?->format('d-m-Y') }}</p></div>
        <div class="rounded-lg bg-gray-50 p-4"><p class="text-sm text-gray-500">Kategori</p><p class="font-semibold">{{ $receipt->category?->name ?? '-' }}</p></div>
        <div class="rounded-lg bg-gray-50 p-4"><p class="text-sm text-gray-500">Akun Kas</p><p class="font-semibold">{{ $receipt->cashAccount?->name ?? '-' }}</p><p class="text-sm text-gray-500">{{ $receipt->cashAccount?->accountTypeLabel() ?? '-' }}</p></div>
        <div class="rounded-lg bg-gray-50 p-4"><p class="text-sm text-gray-500">Donatur</p><p class="font-semibold">{{ $receipt->donor_name ?: '-' }}</p></div>
        <div class="rounded-lg bg-gray-50 p-4"><p class="text-sm text-gray-500">Nominal</p><p class="font-semibold">Rp {{ number_format($receiptAmount, 0, ',', '.') }}</p></div>
        <div class="rounded-lg bg-gray-50 p-4"><p class="text-sm text-gray-500">Total Disalurkan</p><p class="font-semibold">Rp {{ number_format($distributedAmount, 0, ',', '.') }}</p></div>
        <div class="rounded-lg bg-gray-50 p-4"><p class="text-sm text-gray-500">Sisa</p><p class="font-semibold">Rp {{ number_format($remainingAmount, 0, ',', '.') }}</p></div>
        <div class="rounded-lg bg-gray-50 p-4">
            <p class="text-sm text-gray-500">Status</p>
            <span class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $status }}</span>
        </div>
        <div class="rounded-lg bg-gray-50 p-4"><p class="text-sm text-gray-500">Dibuat oleh</p><p class="font-semibold">{{ $receipt->created_by ?: '-' }}</p></div>
        <div class="rounded-lg bg-gray-50 p-4 md:col-span-2">
            <p class="text-sm text-gray-500">Bukti</p>
            @if($proofFile)
                <a href="{{ asset('storage/' . $proofFile) }}" target="_blank" class="font-semibold text-indigo-600 hover:text-indigo-800">Lihat bukti transaksi</a>
            @else
                <p class="font-semibold">-</p>
            @endif
        </div>
        <div class="rounded-lg bg-gray-50 p-4 md:col-span-2"><p class="text-sm text-gray-500">Keterangan</p><p>{{ $receipt->description ?: '-' }}</p></div>
    </div>
    <div class="mt-8">
        <div class="mb-4 flex items-center justify-between gap-3">
            <h3 class="text-lg font-bold text-gray-800">Riwayat Penyaluran</h3>
            @if($remainingAmount > 0)
                <a href="{{ route('zis.distributions.create', ['receipt_id' => $receipt->id]) }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Salurkan</a>
            @else
                <span class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-600">Sudah Disalurkan</span>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Tujuan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Penerima</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Nominal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($receipt->distributions as $distribution)
                        <tr>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $distribution->distribution_date?->format('d-m-Y') ?? $distribution->tanggal?->format('d-m-Y') }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $distribution->distribution_target === 'kas_operasional' ? 'Kas Operasional Masjid' : 'Penerima Manfaat / Mustahik' }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $distribution->recipient_name ?: '-' }}</td>
                            <td class="px-4 py-4 text-sm font-semibold text-gray-800">Rp {{ number_format($distribution->amount ?? $distribution->nominal ?? 0, 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $distribution->description ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada penyaluran yang terhubung ke penerimaan ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-6 flex gap-3">
        <a href="{{ route('zis.receipts.edit', $receipt) }}" class="rounded-lg bg-green-600 px-4 py-2 text-white">Edit</a>
        <a href="{{ route('zis.receipts.index') }}" class="rounded-lg border px-4 py-2 text-gray-700">Kembali</a>
    </div>
</div>
@endsection
