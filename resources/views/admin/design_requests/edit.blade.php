@extends('layouts.admin')

@section('title', 'Edit Prompt Desain - SIMAS')
@section('page_title', 'Edit Prompt Desain')

@section('content')
<form method="POST" action="{{ route('design-requests.update', $designRequest) }}" enctype="multipart/form-data" class="space-y-6 rounded-lg bg-white p-6 shadow">
    @csrf
    @method('PUT')
    @include('admin.design_requests._form')
    <div class="flex justify-end gap-3">
        <a href="{{ route('design-requests.show', $designRequest) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-bold text-gray-700">Batal</a>
        <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white">Simpan</button>
    </div>
</form>
@endsection
