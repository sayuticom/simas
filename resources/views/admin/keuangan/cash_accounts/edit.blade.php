@extends('layouts.admin')

@section('title', 'Edit Akun Kas - SIMAS')
@section('page_title', 'Edit Akun Kas')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800">Edit Akun Kas / Tempat Dana</h2>
        <p class="text-sm text-gray-500">Akun nonaktif tetap menjaga riwayat lama, tetapi tidak muncul untuk input transaksi baru.</p>
    </div>

    <form action="{{ route('keuangan.akun-kas.update', $cashAccount) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.keuangan.cash_accounts.partials.form')
        <div class="flex justify-end gap-3">
            <a href="{{ route('keuangan.akun-kas.index') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
