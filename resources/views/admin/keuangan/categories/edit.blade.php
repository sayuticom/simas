@extends('layouts.admin')

@section('title', 'Edit Kategori Pengeluaran - SIMAS')
@section('page_title', 'Edit Kategori Pengeluaran')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800">Edit Kategori Pengeluaran</h2>
        <p class="text-sm text-gray-500">Perbarui kategori pengeluaran operasional masjid. Tipe kategori akan tetap "keluar".</p>
    </div>

    <form action="{{ route('keuangan.kategori.update', $category) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        @include('admin.keuangan.categories.partials.form')

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('keuangan.kategori.index') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50 transition">Batal</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700 transition">Simpan</button>
        </div>
    </form>
</div>
@endsection
