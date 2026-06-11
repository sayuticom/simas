@extends('layouts.admin')

@section('title', 'Tambah Jadwal Petugas - SIMAS')
@section('page_title', 'Tambah Jadwal Petugas')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('jadwal-petugas.store') }}" method="POST" class="space-y-6">
        @csrf
        @include('admin.jadwal-petugas._form', ['jadwalPetugas' => null])

        <div class="flex flex-wrap justify-end gap-3">
            <a href="{{ route('jadwal-petugas.index') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
