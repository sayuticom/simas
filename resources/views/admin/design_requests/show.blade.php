@extends('layouts.admin')

@section('title', 'Detail Prompt Desain - SIMAS')
@section('page_title', 'Detail Prompt Desain')

@section('content')
@php($statusOptions = \App\Models\DesignRequest::statusOptions())
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">{{ session('success') }}</div>
    @endif

    <div class="rounded-lg bg-white p-6 shadow">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $designRequest->title }}</h2>
                <p class="mt-2 text-sm font-semibold text-gray-600">{{ $statusOptions[$designRequest->status] ?? $designRequest->status }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('design-requests.edit', $designRequest) }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white">Edit</a>
                <a href="{{ route('design-requests.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-bold text-gray-700">Kembali</a>
            </div>
        </div>
    </div>

    <div class="rounded-lg bg-white p-6 shadow">
        <div class="mb-3 flex items-center justify-between gap-3">
            <h3 class="text-lg font-bold text-gray-900">Prompt Final</h3>
            <button type="button" id="copyDesignPrompt" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-800">Salin Prompt</button>
        </div>
        <textarea id="designPromptText" readonly rows="18" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 font-mono text-sm leading-6">{{ $designRequest->prompt_text }}</textarea>
    </div>

    @if($designRequest->source_snapshot)
        <div class="rounded-lg bg-white p-6 shadow">
            <h3 class="mb-3 text-lg font-bold text-gray-900">Source Snapshot</h3>
            <pre class="overflow-x-auto rounded-xl bg-slate-950 p-4 text-xs leading-6 text-slate-100">{{ json_encode($designRequest->source_snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    @endif
</div>

<script>
    document.getElementById('copyDesignPrompt')?.addEventListener('click', async function () {
        const textarea = document.getElementById('designPromptText');
        await navigator.clipboard.writeText(textarea.value);
        const original = this.textContent;
        this.textContent = 'Prompt Disalin';
        setTimeout(() => this.textContent = original, 1600);
    });
</script>
@endsection
