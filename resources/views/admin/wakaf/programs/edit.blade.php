@extends('layouts.admin')

@section('title', 'Edit Program Wakaf - SIMAS')
@section('page_title', 'Edit Program Wakaf')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('wakaf.programs.update', $program) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.wakaf.programs._form', ['program' => $program])
        <div class="flex justify-end gap-3">
            <a href="{{ route('wakaf.programs.index') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">Batal</a>
            <button class="rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
