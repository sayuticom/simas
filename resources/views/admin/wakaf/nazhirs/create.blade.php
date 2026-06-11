@extends('layouts.admin')

@section('title', 'Tambah Nazhir - SIMAS')
@section('page_title', 'Tambah Nazhir')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('wakaf.nazhirs.store') }}" method="POST" class="space-y-6">
        @csrf
        @include('admin.wakaf.nazhirs._form', ['nazhir' => null])
        <div class="flex justify-end gap-3">
            <a href="{{ route('wakaf.nazhirs.index') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">Batal</a>
            <button class="rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
