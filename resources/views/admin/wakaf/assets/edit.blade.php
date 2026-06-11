@extends('layouts.admin')

@section('title', 'Edit Aset Wakaf - SIMAS')
@section('page_title', 'Edit Aset Wakaf')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('wakaf.assets.update', $asset) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.wakaf.assets._form')
        <div class="flex justify-end gap-3">
            <a href="{{ route('wakaf.assets.index') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
