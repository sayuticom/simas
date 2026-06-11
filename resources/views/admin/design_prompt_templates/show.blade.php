@extends('layouts.admin')

@section('title', 'Detail Template Prompt - SIMAS')
@section('page_title', 'Detail Template Prompt')

@section('content')
<div class="space-y-6">
    <div class="rounded-lg bg-white p-6 shadow">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $template->name }}</h2>
                <p class="mt-2 text-sm font-semibold text-gray-600">{{ $template->module_type ?: 'umum' }} | {{ $template->design_type }} | {{ $template->canvas_size ?: '-' }}</p>
            </div>
            <a href="{{ route('design-prompt-templates.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-bold text-gray-700">Kembali</a>
        </div>
    </div>

    <div class="rounded-lg bg-white p-6 shadow">
        <h3 class="mb-3 text-lg font-bold text-gray-900">Struktur Prompt</h3>
        <textarea readonly rows="16" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 font-mono text-sm">{{ $template->prompt_structure }}</textarea>
    </div>
</div>
@endsection
