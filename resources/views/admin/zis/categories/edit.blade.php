@extends('layouts.admin')

@section('title', 'Edit Kategori ZIS - SIMAS')
@section('page_title', 'Edit Kategori ZIS')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('zis.categories.update', $category) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.zis.categories.form')
        <div class="flex justify-end gap-3">
            <a href="{{ route('zis.categories.index') }}" class="rounded-lg border px-5 py-3 text-gray-700">Batal</a>
            <button class="rounded-lg bg-indigo-600 px-5 py-3 text-white">Simpan</button>
        </div>
    </form>
</div>
@endsection
