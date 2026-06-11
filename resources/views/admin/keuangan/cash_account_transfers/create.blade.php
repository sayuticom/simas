@extends('layouts.admin')

@section('title', 'Tambah Mutasi Akun Kas - SIMAS')
@section('page_title', 'Tambah Mutasi Akun Kas')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800">Tambah Mutasi / Transfer Antar Akun Kas</h2>
        <p class="text-sm text-gray-500">Mutasi hanya memindahkan posisi dana antar akun, bukan pemasukan atau pengeluaran baru.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-semibold">Mutasi belum bisa disimpan:</p>
            <ul class="mt-2 list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('keuangan.mutasi-akun-kas.store') }}" method="POST" class="space-y-6">
        @csrf
        @include('admin.keuangan.cash_account_transfers.form')
        <div class="flex justify-end gap-3">
            <a href="{{ route('keuangan.mutasi-akun-kas.index') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700">Simpan Mutasi</button>
        </div>
    </form>
</div>
@endsection
