@extends('layouts.admin')

@section('title', 'Tambah Wakaf Non-Tunai - SIMAS')
@section('page_title', 'Tambah Wakaf Non-Tunai')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('wakaf.non-cash.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @include('admin.wakaf.non-cash._form', ['wakafNonCash' => null])
        <div class="flex justify-end gap-3">
            <a href="{{ route('wakaf.non-cash.index') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">Batal</a>
            <button class="rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
