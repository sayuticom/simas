@extends('layouts.admin')

@section('title', 'Tambah Wakaf Tunai - SIMAS')
@section('page_title', 'Tambah Wakaf Tunai')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('wakaf.cash.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @include('admin.wakaf.cash._form', ['wakafCash' => null])
        <div class="flex justify-end gap-3">
            <a href="{{ route('wakaf.cash.index') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">Batal</a>
            <button class="rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
