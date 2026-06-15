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
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($receipts as $receipt)
                    @php
                        $receiptAmount = $receipt->amount ?? $receipt->nominal_uang ?? 0;
                    @endphp
                    <tr>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $receipt->receipt_date?->format('d-m-Y') ?? $receipt->tanggal?->format('d-m-Y') }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $receipt->category?->name ?? $receipt->jenis_penerimaan }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $receipt->cashAccount?->name ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $receipt->donor_name ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">Rp {{ number_format($receiptAmount, 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-right text-sm">
                            @php
                                $publicUrl = $receipt->public_receipt_token ? route('zis.penerimaan.receipt.public', $receipt->public_receipt_token) : null;
                            @endphp
                            <div class="flex flex-wrap justify-end gap-2">
                                <a href="{{ route('zis.receipts.show', $receipt) }}" class="inline-flex items-center gap-1 rounded-full border border-indigo-200 px-2 py-1 text-xs text-indigo-700 hover:bg-indigo-50">
                                    <i class="fas fa-info-circle"></i> <span>Detail</span>
                                </a>
                                <a href="{{ route('zis.receipts.kwitansi', $receipt) }}" target="_blank" class="inline-flex items-center gap-1 rounded-full border border-gray-200 px-2 py-1 text-xs text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-print"></i> <span>Cetak</span>
                                </a>
                                @if($publicUrl)
                                    <a href="{{ $publicUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 rounded-full bg-emerald-600 px-2 py-1 text-xs text-white hover:bg-emerald-700">
                                        <i class="fas fa-link"></i> <span>Bukti Digital</span>
                                    </a>
                                @else
                                    <button type="button" disabled class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-400" title="Bukti digital belum tersedia">
                                        <i class="fas fa-link"></i> <span>Bukti Digital</span>
                                    </button>
                                @endif
                                @if($receipt->canBeEdited())
                                    <a href="{{ route('zis.receipts.edit', $receipt) }}" class="inline-flex items-center gap-1 rounded-full bg-amber-300 px-2 py-1 text-xs text-amber-900 hover:bg-amber-200">
                                        <i class="fas fa-edit"></i> <span>Edit</span>
                                    </a>
                                @endif
                                @if($receipt->canBeDeleted())
                                    <form action="{{ route('zis.receipts.destroy', $receipt) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex items-center gap-1 rounded-full bg-red-600 px-2 py-1 text-xs text-white hover:bg-red-700" onclick="return confirm('Hapus penerimaan ini?');">
                                            <i class="fas fa-trash-alt"></i> <span>Hapus</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada penerimaan ZIS.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $receipts->links() }}</div>
</div>
@endsection
