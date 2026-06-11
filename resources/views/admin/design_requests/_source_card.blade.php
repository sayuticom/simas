@php
    $existingDesignRequest = $existingDesignRequest ?? null;
    $sourceType = $sourceType ?? null;
    $sourceId = $sourceId ?? null;
    $returnUrl = $returnUrl ?? null;
    $isSavedSource = filled($sourceType) && filled($sourceId);
    $statusOptions = \App\Models\DesignRequest::statusOptions();
    $statusLabel = $existingDesignRequest ? ($statusOptions[$existingDesignRequest->status] ?? 'Prompt Siap') : null;
@endphp

<div class="rounded-lg border border-amber-200 bg-amber-50 p-5">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-bold uppercase tracking-[0.16em] text-amber-800">Prompt Desain / Poster</p>
            <h3 class="mt-1 text-lg font-bold text-gray-900">Generator Prompt Desain</h3>
            <p class="mt-1 text-sm text-gray-700">
                Buat prompt poster dari data ini setelah data sumber tersimpan.
            </p>
            <div class="mt-3 flex flex-wrap gap-2">
                @if($existingDesignRequest)
                    <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-800">Prompt Siap</span>
                    <span class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-bold text-amber-900 ring-1 ring-amber-200">{{ $statusLabel }}</span>
                @elseif($isSavedSource)
                    <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-700">Belum Dibuat</span>
                @else
                    <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-bold text-gray-700">Belum Tersedia</span>
                @endif
            </div>
        </div>

        <div class="shrink-0">
            @if($existingDesignRequest)
                <a href="{{ route('design-requests.edit', array_filter(['designRequest' => $existingDesignRequest, 'return_url' => $returnUrl])) }}" class="inline-flex w-full items-center justify-center rounded-lg bg-amber-700 px-4 py-3 text-sm font-bold text-white hover:bg-amber-800 sm:w-auto">
                    Edit Prompt Desain
                </a>
            @elseif($isSavedSource)
                <a href="{{ route('design-requests.create', array_filter(['source_type' => $sourceType, 'source_id' => $sourceId, 'return_url' => $returnUrl])) }}" class="inline-flex w-full items-center justify-center rounded-lg bg-amber-700 px-4 py-3 text-sm font-bold text-white hover:bg-amber-800 sm:w-auto">
                    Buat Prompt Desain
                </a>
            @else
                <button type="button" disabled class="inline-flex w-full cursor-not-allowed items-center justify-center rounded-lg bg-gray-200 px-4 py-3 text-sm font-bold text-gray-500 sm:w-auto">
                    Buat Prompt Desain
                </button>
                <p class="mt-2 max-w-xs text-xs font-semibold text-gray-600">Simpan data terlebih dahulu untuk membuat Prompt Desain.</p>
            @endif
        </div>
    </div>
</div>
