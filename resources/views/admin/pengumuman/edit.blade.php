@extends('layouts.admin')

@section('title', 'Edit Pengumuman - SIMAS')
@section('page_title', 'Edit Pengumuman')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('pengumuman.update', $pengumuman) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.pengumuman._form')

        <div class="flex flex-wrap justify-end gap-3">
            <a href="{{ route('pengumuman.index') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
