@extends('layouts.admin')

@section('title', 'Penerimaan ZIS - SIMAS')
@section('page_title', 'Penerimaan ZIS')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3">{{ session('error') }}</div>
    @endif
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Penerimaan ZIS</h2>
            <p class="text-sm text-gray-500">Dana zakat, infak, dan sedekah yang diterima.</p>
        </div>
        <a href="{{ route('zis.receipts.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Tambah Penerimaan</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Akun Kas</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Donatur</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nominal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Total Disalurkan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Sisa</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($receipts as $receipt)
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
                    @endphp
                    <tr>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $receipt->receipt_date?->format('d-m-Y') ?? $receipt->tanggal?->format('d-m-Y') }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $receipt->category?->name ?? $receipt->jenis_penerimaan }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $receipt->cashAccount?->name ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $receipt->donor_name ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">Rp {{ number_format($receiptAmount, 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">Rp {{ number_format($distributedAmount, 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">Rp {{ number_format($remainingAmount, 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-sm">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $status }}</span>
                        </td>
                        <td class="px-4 py-4 text-right text-sm space-x-2">
                            <a href="{{ route('zis.receipts.show', $receipt) }}" class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                            <a href="{{ route('zis.receipts.kwitansi', $receipt) }}" target="_blank" class="text-slate-600 hover:text-slate-900">Cetak jika perlu</a>
                            @if($remainingAmount > 0)
                                <a href="{{ route('zis.distributions.create', ['receipt_id' => $receipt->id]) }}" class="text-blue-600 hover:text-blue-900">Salurkan</a>
                            @else
                                <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-600">Sisa Rp 0</span>
                            @endif
                            <a href="{{ route('zis.receipts.edit', $receipt) }}" class="text-green-600 hover:text-green-900">Edit</a>
                            <form action="{{ route('zis.receipts.destroy', $receipt) }}" method="POST" class="inline" onsubmit="return confirm('Hapus penerimaan ini?');">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada penerimaan ZIS.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $receipts->links() }}</div>
</div>
@endsection
