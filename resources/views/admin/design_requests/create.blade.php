@extends('layouts.admin')

@section('title', 'Buat Prompt Desain - SIMAS')
@section('page_title', 'Buat Prompt Desain')

@section('content')
<form method="POST" action="{{ route('design-requests.store') }}" enctype="multipart/form-data" class="space-y-6 rounded-lg bg-white p-6 shadow">
    @csrf
    @include('admin.design_requests._form')
    <div class="flex justify-end gap-3">
        <a href="{{ route('design-requests.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-bold text-gray-700">Batal</a>
        <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white">Simpan Prompt</button>
    </div>
</form>
@endsection
