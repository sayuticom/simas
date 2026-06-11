@extends('layouts.admin')

@section('title', 'Tambah Kategori ZIS - SIMAS')
@section('page_title', 'Tambah Kategori ZIS')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('zis.categories.store') }}" method="POST" class="space-y-6">
        @csrf
        @include('admin.zis.categories.form', ['category' => null])
        <div class="flex justify-end gap-3">
            <a href="{{ route('zis.categories.index') }}" class="rounded-lg border px-5 py-3 text-gray-700">Batal</a>
            <button class="rounded-lg bg-indigo-600 px-5 py-3 text-white">Simpan</button>
        </div>
    </form>
</div>
@endsection
