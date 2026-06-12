@extends('layouts.admin')

@section('title', 'Detail Penerimaan ZIS - SIMAS')
@section('page_title', 'Detail Penerimaan ZIS')

@section('content')
@php
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

    <div class="grid gap-2 md:grid-cols-2">
        <div class="rounded-lg bg-gray-50 p-3"><p class="text-sm text-gray-500">Tanggal</p><p class="font-semibold">{{ $receipt->receipt_date?->format('d-m-Y') }}</p></div>
        <div class="rounded-lg bg-gray-50 p-3"><p class="text-sm text-gray-500">Kategori</p><p class="font-semibold">{{ $receipt->category?->name ?? '-' }}</p></div>
        <div class="rounded-lg bg-gray-50 p-3"><p class="text-sm text-gray-500">Akun Kas</p><p class="font-semibold">{{ $receipt->cashAccount?->name ?? '-' }}</p><p class="text-sm text-gray-500">{{ $receipt->cashAccount?->accountTypeLabel() ?? '-' }}</p></div>
        <div class="rounded-lg bg-gray-50 p-3"><p class="text-sm text-gray-500">Donatur</p><p class="font-semibold">{{ $receipt->donor_name ?: '-' }}</p></div>
        <div class="rounded-lg bg-gray-50 p-3"><p class="text-sm text-gray-500">Nominal</p><p class="font-semibold">Rp {{ number_format($receipt->amount ?? $receipt->nominal_uang ?? 0, 0, ',', '.') }}</p></div>
        <div class="rounded-lg bg-gray-50 p-3"><p class="text-sm text-gray-500">No. HP</p><p class="font-semibold">{{ $receipt->donor_phone ?: '-' }}</p></div>
        <div class="rounded-lg bg-gray-50 p-3"><p class="text-sm text-gray-500">Dibuat oleh</p><p class="font-semibold">{{ $receipt->created_by ?: '-' }}</p></div>
        <div class="rounded-lg bg-gray-50 p-3 md:col-span-2">
            <p class="text-sm text-gray-500">Bukti Transfer / Lampiran / Foto Penyerahan Dana</p>
            @if($proofFile)
                <a href="{{ asset('storage/' . $proofFile) }}" target="_blank" class="font-semibold text-indigo-600 hover:text-indigo-800 text-sm">Lihat bukti transfer / lampiran</a>
            @else
                <p class="font-semibold">-</p>
            @endif
        </div>
        <div class="rounded-lg bg-gray-50 p-3 md:col-span-2"><p class="text-sm text-gray-500">Keterangan</p><p class="text-sm">{{ $receipt->description ?: '-' }}</p></div>
    </div>
    <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 p-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Bukti Tanda Terima Digital</p>
                <p class="mt-1 text-sm text-gray-700">Bukti digital sudah tersedia dan dapat dibagikan kepada donatur/muzakki.</p>
                <div class="mt-2 flex items-center gap-2">
                    <input id="public-receipt-url" type="text" value="{{ $publicReceiptUrl }}" readonly class="w-full rounded-lg border border-emerald-200 bg-white px-3 py-2 text-sm text-gray-700">
                    <button type="button" onclick="copyPublicReceiptUrl()" class="rounded-lg border border-emerald-700 bg-white px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">Salin Link</button>
                </div>
            </div>
            <div class="flex gap-2">
                @if($waPhone)
                    <a href="https://wa.me/{{ $waPhone }}?text={{ rawurlencode($waMessage) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-lg bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700">WhatsApp</a>
                @endif
                <a href="{{ route('zis.receipts.kwitansi', $receipt) }}" target="_blank" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cetak</a>
            </div>
        </div>
        <p id="public-receipt-copy-message" class="mt-2 hidden text-sm font-semibold text-emerald-700">Link bukti digital berhasil disalin.</p>
    </div>
    <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ $publicReceiptUrl }}" target="_blank" rel="noopener noreferrer" class="rounded-lg bg-indigo-600 px-4 py-2 text-white">Lihat Bukti Digital</a>
        <button type="button" onclick="copyPublicReceiptUrl()" class="rounded-lg border px-4 py-2 text-gray-700">Salin Link Bukti Digital</button>
        <a href="{{ route('zis.receipts.kwitansi', $receipt) }}" target="_blank" class="rounded-lg border px-4 py-2 text-gray-700">Cetak</a>
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
