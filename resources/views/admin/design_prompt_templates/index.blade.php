@extends('layouts.admin')

@section('title', 'Template Prompt Desain - SIMAS')
@section('page_title', 'Template Prompt Desain')

@section('content')
@php($moduleOptions = \App\Models\DesignPromptTemplate::moduleOptions())
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="rounded-lg bg-white p-6 shadow">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Template Prompt Desain</h2>
                <p class="mt-1 text-sm text-gray-600">Template reusable untuk membuat prompt desain lintas modul.</p>
            </div>
            <a href="{{ route('design-prompt-templates.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
                <i class="fas fa-plus"></i> Tambah Template
            </a>
        </div>
    </div>

    <div class="rounded-lg bg-white p-6 shadow">
        <form method="GET" action="{{ route('design-prompt-templates.index') }}" class="mb-5 grid gap-4 md:grid-cols-4">
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama template" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <select name="module_type" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="">Semua modul</option>
                @foreach($moduleOptions as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['module_type'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <input type="text" name="design_type" value="{{ $filters['design_type'] ?? '' }}" placeholder="Jenis desain" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white">Filter</button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Template</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Modul</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Jenis</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($templates as $template)
                        <tr>
                            <td class="px-4 py-4">
                                <p class="font-semibold text-gray-900">{{ $template->name }}</p>
                                <p class="mt-1 text-xs font-semibold text-gray-600">{{ $template->mosque_id ? 'Template masjid' : 'Template global' }}</p>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $moduleOptions[$template->module_type] ?? 'Umum' }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $template->design_type }} {{ $template->canvas_size ? '('.$template->canvas_size.')' : '' }}</td>
                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $template->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $template->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('design-prompt-templates.show', $template) }}" class="rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700">Lihat</a>
                                    @if($template->mosque_id || auth()->user()?->isSuperuser())
                                        <a href="{{ route('design-prompt-templates.edit', $template) }}" class="rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-bold text-green-700">Edit</a>
                                        <form method="POST" action="{{ route('design-prompt-templates.destroy', $template) }}" onsubmit="return confirm('Hapus template ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-bold text-red-700">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada template prompt.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $templates->links() }}</div>
    </div>
</div>
@endsection
