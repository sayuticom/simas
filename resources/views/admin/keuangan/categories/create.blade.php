@extends('layouts.admin')

@section('title', 'Tambah Kategori Pengeluaran - SIMAS')
@section('page_title', 'Tambah Kategori Pengeluaran')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800">Tambah Kategori Pengeluaran</h2>
        <p class="text-sm text-gray-500">Buat kategori pengeluaran operasional. Tipe diset otomatis ke "keluar".</p>
    </div>

    <form action="{{ route('keuangan.kategori.store') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="return_to" value="{{ $returnTo }}">
        <input type="hidden" name="transaction_id" value="{{ $transactionId }}">

        @include('admin.keuangan.categories.partials.form', ['category' => null])

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ $returnTo === 'transaction_create' ? route('keuangan.transaksi.create') : ($returnTo === 'transaction_edit' && $transactionId ? route('keuangan.transaksi.edit', $transactionId) : route('keuangan.kategori.index')) }}" class="rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50 transition">Batal</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700 transition">Simpan</button>
        </div>
    </form>
</div>
@endsection
