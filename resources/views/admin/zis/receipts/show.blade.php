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
    $waPhone = preg_replace('/\D+/', '', (string) $receipt->donor_phone);
    if (str_starts_with($waPhone, '0')) {
        $waPhone = '62' . substr($waPhone, 1);
    }
    $waMessage = "Assalamu'alaikum, berikut bukti tanda terima ZIS dari " . (auth()->user()?->activeMosque?->name ?? 'Masjid') . ': ' . $publicReceiptUrl . '. Jazakumullahu khairan.';
@endphp
<div class="bg-white rounded-lg shadow p-6">
    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">{{ session('success') }}</div>
    @endif
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
            <p class="text-sm text-gray-500">Bukti Transfer / Lampiran</p>
            @if($proofFile)
                <a href="{{ asset('storage/' . $proofFile) }}" target="_blank" class="font-semibold text-indigo-600 hover:text-indigo-800">Lihat bukti transfer / lampiran</a>
            @else
                <p class="font-semibold">-</p>
            @endif
        </div>
        <div class="rounded-lg bg-gray-50 p-4 md:col-span-2"><p class="text-sm text-gray-500">Keterangan</p><p>{{ $receipt->description ?: '-' }}</p></div>
    </div>
    <div class="mt-8 rounded-xl border border-emerald-200 bg-emerald-50 p-5">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Paperless</p>
                <h3 class="mt-1 text-lg font-bold text-gray-900">Bukti Tanda Terima Digital</h3>
                <p class="mt-2 text-sm leading-relaxed text-gray-700">Scan QR Code atau bagikan link bukti digital ini kepada donatur/muzakki. Bukti ini dibuat dari data sistem dan dapat dicek tanpa login.</p>
                <div class="mt-4">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Link Bukti Digital</label>
                    <input id="public-receipt-url" type="text" value="{{ $publicReceiptUrl }}" readonly class="mt-2 w-full rounded-lg border border-emerald-200 bg-white px-4 py-3 text-sm text-gray-700">
                </div>
                <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:flex-wrap">
                    <a href="{{ $publicReceiptUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-800">Lihat Bukti Digital</a>
                    @if($waPhone)
                        <a href="https://wa.me/{{ $waPhone }}?text={{ rawurlencode($waMessage) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700">Kirim via WhatsApp</a>
                    @endif
                    <button type="button" onclick="copyPublicReceiptUrl()" class="inline-flex items-center justify-center rounded-lg border border-emerald-700 bg-white px-4 py-2.5 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">Salin Link Bukti Digital</button>
                    <a href="{{ route('zis.receipts.kwitansi', $receipt) }}" target="_blank" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cetak jika diperlukan</a>
                </div>
                <p id="public-receipt-copy-message" class="mt-2 hidden text-sm font-semibold text-emerald-700">Link bukti digital berhasil disalin.</p>
            </div>
            <div class="shrink-0 rounded-2xl border border-emerald-200 bg-white p-4 shadow-sm">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(180)->margin(2)->generate($publicReceiptUrl) !!}
            </div>
        </div>
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
        <a href="{{ $publicReceiptUrl }}" target="_blank" rel="noopener noreferrer" class="rounded-lg bg-indigo-600 px-4 py-2 text-white">Lihat Bukti Digital</a>
        <a href="{{ route('zis.receipts.edit', $receipt) }}" class="rounded-lg bg-green-600 px-4 py-2 text-white">Edit</a>
        <a href="{{ route('zis.receipts.index') }}" class="rounded-lg border px-4 py-2 text-gray-700">Kembali</a>
    </div>
</div>
<script>
    function copyPublicReceiptUrl() {
        const input = document.getElementById('public-receipt-url');
        const message = document.getElementById('public-receipt-copy-message');

        navigator.clipboard.writeText(input.value).then(function () {
            message.classList.remove('hidden');
            setTimeout(function () {
                message.classList.add('hidden');
            }, 2500);
        });
    }
</script>
@endsection
