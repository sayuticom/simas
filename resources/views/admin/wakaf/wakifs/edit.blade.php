@extends('layouts.admin')

@section('title', 'Edit Wakif - SIMAS')
@section('page_title', 'Edit Wakif')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('wakaf.wakifs.update', $wakif) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.wakaf.wakifs._form', ['wakif' => $wakif])
        <div class="flex justify-end gap-3">
            <a href="{{ route('wakaf.wakifs.index') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">Batal</a>
            <button class="rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
