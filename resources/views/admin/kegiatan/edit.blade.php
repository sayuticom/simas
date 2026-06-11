@extends('layouts.admin')

@section('title', 'Edit Kegiatan - SIMAS')
@section('page_title', 'Edit Kegiatan')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('kegiatan.update', $kegiatan) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.kegiatan._form')

        @include('admin.design_requests._source_card', [
            'sourceType' => 'kegiatan',
            'sourceId' => $kegiatan->id,
            'existingDesignRequest' => $existingDesignRequest ?? null,
            'returnUrl' => route('kegiatan.edit', $kegiatan, false),
        ])

        <div class="flex flex-wrap justify-end gap-3">
            <a href="{{ route('kegiatan.index') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
