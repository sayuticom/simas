@extends('layouts.admin')

@section('title', 'Tambah Template Prompt - SIMAS')
@section('page_title', 'Tambah Template Prompt')

@section('content')
<form method="POST" action="{{ route('design-prompt-templates.store') }}" class="space-y-6 rounded-lg bg-white p-6 shadow">
    @csrf
    @include('admin.design_prompt_templates._form')
    <div class="flex justify-end gap-3">
        <a href="{{ route('design-prompt-templates.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-bold text-gray-700">Batal</a>
        <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white">Simpan</button>
    </div>
</form>
@endsection
