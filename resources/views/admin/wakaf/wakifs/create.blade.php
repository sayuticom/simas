@extends('layouts.admin')

@section('title', 'Tambah Wakif - SIMAS')
@section('page_title', 'Tambah Wakif')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('wakaf.wakifs.store') }}" method="POST" class="space-y-6">
        @csrf
        @include('admin.wakaf.wakifs._form', ['wakif' => null])
        <div class="flex justify-end gap-3">
            <a href="{{ route('wakaf.wakifs.index') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">Batal</a>
            <button class="rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
