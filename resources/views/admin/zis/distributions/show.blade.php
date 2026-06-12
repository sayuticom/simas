@extends('layouts.admin')

@section('title', 'Detail Penyaluran ZIS - SIMAS')
@section('page_title', 'Detail Penyaluran ZIS')

@section('content')
@php
    $distributionAmount = $distribution->amount ?? $distribution->nominal ?? 0;
    $proofFile = $distribution->proof_file ?? $distribution->bukti_serah_terima;
@endphp
<div class="bg-white rounded-lg shadow p-6">
    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-lg bg-gray-50 p-4"><p class="text-sm text-gray-500">Tanggal</p><p class="font-semibold">{{ $distribution->distribution_date?->format('d-m-Y') }}</p></div>
        <div class="rounded-lg bg-gray-50 p-4"><p class="text-sm text-gray-500">Kategori</p><p class="font-semibold">{{ $distribution->category?->name ?? '-' }}</p></div>
        <div class="rounded-lg bg-gray-50 p-4 md:col-span-2">
            <p class="text-sm text-gray-500">Sumber Penerimaan</p>
            @if($distribution->receipt)
                <p class="font-semibold">{{ $distribution->receipt->category?->name ?? '-' }} - {{ $distribution->receipt->donor_name ?: '-' }} - Rp {{ number_format($distribution->receipt->amount ?? $distribution->receipt->nominal_uang ?? 0, 0, ',', '.') }}</p>
            @else
                <p class="font-semibold text-gray-500">Belum terpetakan ke penerimaan tertentu</p>
            @endif
        </div>
        <div class="rounded-lg bg-gray-50 p-4"><p class="text-sm text-gray-500">Tujuan Penyaluran</p><p class="font-semibold">{{ $distribution->distribution_target === 'kas_operasional' ? 'Kas Operasional Masjid' : 'Penerima Manfaat / Mustahik' }}</p></div>
        <div class="rounded-lg bg-gray-50 p-4"><p class="text-sm text-gray-500">Penerima</p><p class="font-semibold">{{ $distribution->recipient_name }}</p></div>
        <div class="rounded-lg bg-gray-50 p-4"><p class="text-sm text-gray-500">Asnaf/Jenis Penerima</p><p class="font-semibold">{{ $distribution->recipient_type ?: '-' }}</p></div>
        <div class="rounded-lg bg-gray-50 p-4"><p class="text-sm text-gray-500">Nominal</p><p class="font-semibold">Rp {{ number_format($distributionAmount, 0, ',', '.') }}</p></div>
        <div class="rounded-lg bg-gray-50 p-4"><p class="text-sm text-gray-500">Dibuat oleh</p><p class="font-semibold">{{ $distribution->created_by ?: '-' }}</p></div>
        <div class="rounded-lg bg-gray-50 p-4 md:col-span-2">
            <p class="text-sm text-gray-500">Bukti</p>
            @if($proofFile)
                <a href="{{ asset('storage/' . $proofFile) }}" target="_blank" class="font-semibold text-indigo-600 hover:text-indigo-800">Lihat bukti transaksi</a>
            @else
                <p class="font-semibold">-</p>
            @endif
        </div>
        <div class="rounded-lg bg-gray-50 p-4 md:col-span-2"><p class="text-sm text-gray-500">Alamat</p><p>{{ $distribution->recipient_address ?: '-' }}</p></div>
        @if($distribution->distribution_target === 'kas_operasional')
            <div class="rounded-lg bg-green-50 p-4 md:col-span-2 border border-green-200">
                <p class="text-sm font-semibold text-green-700">Transaksi Keuangan Masjid</p>
                @if($distribution->operationalTransaction)
                    <div class="mt-3 grid gap-3 md:grid-cols-4">
                        <div>
                            <p class="text-xs text-green-700">Tanggal</p>
                            <p class="font-semibold text-gray-800">{{ $distribution->operationalTransaction->transaction_date?->format('d-m-Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-green-700">Nominal</p>
                            <p class="font-semibold text-gray-800">Rp {{ number_format($distribution->operationalTransaction->amount, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-green-700">Kategori</p>
                            <p class="font-semibold text-gray-800">{{ $distribution->operationalTransaction->category?->name ?? 'Transfer dari ZIS' }}</p>
                        </div>
                    </div>
                @else
                    <p class="mt-2 text-sm text-red-700">Transaksi keuangan terkait belum ditemukan.</p>
                @endif
            </div>
        @endif
        <div class="rounded-lg bg-gray-50 p-4 md:col-span-2"><p class="text-sm text-gray-500">Keterangan</p><p>{{ $distribution->description ?: '-' }}</p></div>
    </div>
    <div class="mt-6 flex gap-3">
        <a href="{{ route('zis.distributions.edit', $distribution) }}" class="rounded-lg bg-green-600 px-4 py-2 text-white">Edit</a>
        <a href="{{ route('zis.distributions.index') }}" class="rounded-lg border px-4 py-2 text-gray-700">Kembali</a>
    </div>
</div>
@endsection
