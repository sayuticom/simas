@extends('layouts.admin')

@section('title', 'Edit Wakaf Non-Tunai - SIMAS')
@section('page_title', 'Edit Wakaf Non-Tunai')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('wakaf.non-cash.update', $wakafNonCash) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.wakaf.non-cash._form', ['wakafNonCash' => $wakafNonCash])
        <div class="flex justify-end gap-3">
            <a href="{{ route('wakaf.non-cash.index') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">Batal</a>
            <button class="rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
