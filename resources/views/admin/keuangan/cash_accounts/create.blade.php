@extends('layouts.admin')

@section('title', 'Tambah Akun Kas - SIMAS')
@section('page_title', 'Tambah Akun Kas')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800">Tambah Akun Kas / Tempat Dana</h2>
        <p class="text-sm text-gray-500">Gunakan untuk mencatat posisi dana seperti tunai, bank, QRIS, atau e-wallet.</p>
    </div>

    <form action="{{ route('keuangan.akun-kas.store') }}" method="POST" class="space-y-6">
        @csrf
        @include('admin.keuangan.cash_accounts.partials.form')
        <div class="flex justify-end gap-3">
            <a href="{{ route('keuangan.akun-kas.index') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
