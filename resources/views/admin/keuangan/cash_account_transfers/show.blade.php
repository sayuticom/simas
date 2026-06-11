@extends('layouts.admin')

@section('title', 'Detail Mutasi Akun Kas - SIMAS')
@section('page_title', 'Detail Mutasi Akun Kas')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Mutasi Rp {{ number_format($cashAccountTransfer->amount, 0, ',', '.') }}</h2>
            <p class="text-sm text-gray-500">Detail pemindahan dana antar akun kas.</p>
        </div>
        <a href="{{ route('keuangan.mutasi-akun-kas.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Tanggal Mutasi</p>
            <p class="text-lg font-semibold text-gray-800">{{ $cashAccountTransfer->transfer_date?->format('d-m-Y') }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Nominal</p>
            <p class="text-lg font-semibold text-gray-800">Rp {{ number_format($cashAccountTransfer->amount, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-red-50 p-5">
            <p class="text-sm text-red-700">Akun Asal</p>
            <p class="text-lg font-semibold text-gray-800">{{ $cashAccountTransfer->fromAccount?->name ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-green-50 p-5">
            <p class="text-sm text-green-700">Akun Tujuan</p>
            <p class="text-lg font-semibold text-gray-800">{{ $cashAccountTransfer->toAccount?->name ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Petugas Pencatat</p>
            <p class="text-lg font-semibold text-gray-800">{{ $cashAccountTransfer->creator?->name ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Dicatat Pada</p>
            <p class="text-lg font-semibold text-gray-800">{{ $cashAccountTransfer->created_at?->format('d-m-Y H:i') }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5 md:col-span-2">
            <p class="text-sm text-gray-500">Catatan</p>
            <p class="mt-2 text-lg font-semibold text-gray-800">{{ $cashAccountTransfer->note ?: '-' }}</p>
        </div>
    </div>
</div>
@endsection
