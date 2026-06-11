<?php

namespace App\Http\Controllers;

use App\Models\DesignPromptTemplate;
use App\Models\DesignRequest;
use App\Services\DesignPrompts\DesignPromptGenerator;
use App\Support\DesignPromptOptions;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DesignRequestController extends Controller
{
    public function __construct(private readonly DesignPromptGenerator $generator)
    {
    }

    public function index(Request $request)
    {
        $filters = $request->only(['q', 'status', 'source_type']);

        $designRequests = DesignRequest::with('template')
            ->where('mosque_id', $this->activeMosqueId())
            ->when($filters['q'] ?? null, fn ($query, $keyword) => $query->where('title', 'like', "%{$keyword}%"))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['source_type'] ?? null, fn ($query, $sourceType) => $query->where('source_type', $sourceType))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.design_requests.index', compact('designRequests', 'filters'));
    }

    public function create(Request $request)
    {
        $mosqueId = $this->activeMosqueId();
        $sourceType = $request->query('source_type');
        $sourceId = $request->integer('source_id') ?: null;
        $sourceData = $this->generator->getSourceData($sourceType, $sourceId, $mosqueId);
        $selectedOptions = $this->defaultPromptOptions($sourceType);
        $promptText = $sourceType && $sourceId
            ? $this->generator->buildPromptFromOptions($sourceType, $sourceId, $mosqueId, $selectedOptions)
            : $this->generator->buildPromptText($sourceData, $selectedOptions);

        return view('admin.design_requests.create', [
            'designRequest' => null,
            'sourceData' => $sourceData,
            'sourceType' => $sourceType,
            'sourceId' => $sourceId,
            'promptOptions' => DesignPromptOptions::all(),
            'selectedOptions' => $selectedOptions,
            'promptText' => $promptText,
            'returnUrl' => $this->safeReturnUrl($request->query('return_url')),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $mosqueId = $this->activeMosqueId();
        $sourceData = $this->generator->getSourceData($data['source_type'] ?? null, $data['source_id'] ?? null, $mosqueId);
        $generatedPrompt = $this->generator->buildPromptText($sourceData, $data['selected_options'] ?? []);

        $data['mosque_id'] = $mosqueId;
        $data['title'] = ($data['title'] ?? null) ?: $this->generator->buildTitle($sourceData);
        $data['prompt_text'] = $request->filled('prompt_text') ? $data['prompt_text'] : $generatedPrompt;
        $data['source_snapshot'] = $sourceData;
        $data['status'] = $data['status'] ?? DesignRequest::STATUS_PROMPT_READY;
        $data['design_prompt_template_id'] = null;
        $data['negative_prompt'] = null;
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $designRequest = DesignRequest::create($data);
        $returnUrl = $this->safeReturnUrl($request->input('return_url'));

        return ($returnUrl ? redirect($returnUrl) : redirect()->route('design-requests.index'))
            ->with('success', 'Prompt desain berhasil disimpan.');
    }

    public function show(DesignRequest $designRequest)
    {
        $this->authorizeDesignRequest($designRequest);

        return view('admin.design_requests.show', compact('designRequest'));
    }

    public function edit(Request $request, DesignRequest $designRequest)
    {
        $this->authorizeDesignRequest($designRequest);

        return view('admin.design_requests.edit', [
            'designRequest' => $designRequest,
            'sourceData' => $designRequest->source_snapshot ?? [],
            'sourceType' => $designRequest->source_type,
            'sourceId' => $designRequest->source_id,
            'promptOptions' => DesignPromptOptions::all(),
            'selectedOptions' => $designRequest->selected_options ?? $this->defaultPromptOptions($designRequest->source_type),
            'promptText' => $designRequest->prompt_text,
            'returnUrl' => $this->safeReturnUrl($request->query('return_url')),
        ]);
    }

    public function update(Request $request, DesignRequest $designRequest)
    {
        $this->authorizeDesignRequest($designRequest);
        $data = $this->validatedData($request, $designRequest);
        if (! $request->filled('prompt_text')) {
            $data['prompt_text'] = $this->generator->buildPromptText($designRequest->source_snapshot ?? [], $data['selected_options'] ?? []);
        }
        $data['updated_by'] = auth()->id();

        $designRequest->update($data);
        $returnUrl = $this->safeReturnUrl($request->input('return_url'));

        return ($returnUrl ? redirect($returnUrl) : redirect()->route('design-requests.index'))
            ->with('success', 'Prompt desain berhasil diperbarui.');
    }

    public function destroy(DesignRequest $designRequest)
    {
        $this->authorizeDesignRequest($designRequest);
        $designRequest->delete();

        return redirect()->route('design-requests.index')->with('success', 'Prompt desain berhasil dihapus.');
    }

    private function validatedData(Request $request, ?DesignRequest $designRequest = null): array
    {
        $sourceTypes = array_keys(DesignPromptGenerator::sourceOptions());

        $data = $request->validate([
            'source_type' => ['nullable', Rule::in($sourceTypes)],
            'source_id' => 'nullable|integer',
            'title' => 'nullable|string|max:255',
            'prompt_text' => 'nullable|string',
            'prompt_pakai_foto_narasumber' => 'nullable',
            'prompt_tujuan_flyer' => ['nullable', 'string', Rule::in(DesignPromptOptions::flyerPurposeOptions())],
            'prompt_nuansa_desain' => ['nullable', 'string', Rule::in(DesignPromptOptions::designToneOptions())],
            'prompt_warna_utama' => ['nullable', 'string', Rule::in(DesignPromptOptions::mainColorOptions())],
            'prompt_gaya_desain' => ['nullable', 'string', Rule::in(DesignPromptOptions::designStyleOptions())],
            'prompt_target_audiens' => ['nullable', 'string', Rule::in(DesignPromptOptions::targetAudienceOptions())],
            'prompt_tingkat_keramaian' => ['nullable', 'string', Rule::in(DesignPromptOptions::crowdLevelOptions())],
            'prompt_fokus_utama' => ['nullable', 'string', Rule::in(DesignPromptOptions::mainFocusOptions())],
            'prompt_model_layout' => ['nullable', 'string', Rule::in(DesignPromptOptions::layoutModelOptions())],
            'prompt_kepadatan_teks' => ['nullable', 'string', Rule::in(DesignPromptOptions::textDensityOptions())],
            'prompt_posisi_foto_pemateri' => ['nullable', 'string', Rule::in(DesignPromptOptions::speakerPhotoPositionOptions())],
            'prompt_elemen_desain' => 'nullable|array',
            'prompt_elemen_desain.*' => ['nullable', 'string', Rule::in(DesignPromptOptions::designElementOptions())],
            'prompt_catatan_tambahan' => 'nullable|string|max:1000',
            'status' => ['nullable', Rule::in(array_keys(DesignRequest::statusOptions()))],
        ]);

        $data['source_id'] = $data['source_id'] ?? null;
        $data['selected_options'] = $this->extractPromptOptions($request);
        unset(
            $data['prompt_pakai_foto_narasumber'],
            $data['prompt_tujuan_flyer'],
            $data['prompt_nuansa_desain'],
            $data['prompt_warna_utama'],
            $data['prompt_gaya_desain'],
            $data['prompt_target_audiens'],
            $data['prompt_tingkat_keramaian'],
            $data['prompt_fokus_utama'],
            $data['prompt_model_layout'],
            $data['prompt_kepadatan_teks'],
            $data['prompt_posisi_foto_pemateri'],
            $data['prompt_elemen_desain'],
            $data['prompt_catatan_tambahan'],
        );

        if ($designRequest) {
            unset($data['source_type'], $data['source_id']);
        }

        return $data;
    }

    private function findTemplate(int $templateId): DesignPromptTemplate
    {
        return DesignPromptTemplate::query()
            ->availableForMosque($this->activeMosqueId())
            ->findOrFail($templateId);
    }

    private function templateOptions(?string $sourceType)
    {
        return DesignPromptTemplate::query()
            ->active()
            ->availableForMosque($this->activeMosqueId())
            ->forModule($sourceType)
            ->orderByRaw('mosque_id is null')
            ->orderBy('name')
            ->get();
    }

    private function authorizeDesignRequest(DesignRequest $designRequest): void
    {
        abort_unless((int) $designRequest->mosque_id === $this->activeMosqueId(), 404);
    }

    private function activeMosqueId(): int
    {
        return (int) (session('active_mosque_id') ?: auth()->user()?->active_mosque_id);
    }

    private function safeReturnUrl(?string $returnUrl): ?string
    {
        if (! $returnUrl) {
            return null;
        }

        $returnUrl = trim($returnUrl);

        if (
            ! str_starts_with($returnUrl, '/')
            || str_starts_with($returnUrl, '//')
            || str_contains($returnUrl, '://')
            || str_contains($returnUrl, "\r")
            || str_contains($returnUrl, "\n")
        ) {
            return null;
        }

        return $returnUrl;
    }

    private function extractPromptOptions(Request $request): array
    {
        return [
            'prompt_pakai_foto_narasumber' => (string) $request->input('prompt_pakai_foto_narasumber', '0'),
            'prompt_tujuan_flyer' => $request->input('prompt_tujuan_flyer'),
            'prompt_nuansa_desain' => $request->input('prompt_nuansa_desain'),
            'prompt_warna_utama' => $request->input('prompt_warna_utama'),
            'prompt_gaya_desain' => $request->input('prompt_gaya_desain'),
            'prompt_target_audiens' => $request->input('prompt_target_audiens'),
            'prompt_tingkat_keramaian' => $request->input('prompt_tingkat_keramaian'),
            'prompt_fokus_utama' => $request->input('prompt_fokus_utama'),
            'prompt_model_layout' => $request->input('prompt_model_layout'),
            'prompt_kepadatan_teks' => $request->input('prompt_kepadatan_teks'),
            'prompt_posisi_foto_pemateri' => $request->input('prompt_posisi_foto_pemateri'),
            'prompt_elemen_desain' => array_values(array_filter($request->input('prompt_elemen_desain', []))),
            'prompt_catatan_tambahan' => $request->input('prompt_catatan_tambahan'),
        ];
    }

    private function defaultPromptOptions(?string $sourceType): array
    {
        return [
            'prompt_pakai_foto_narasumber' => '0',
            'prompt_tujuan_flyer' => $sourceType === DesignPromptGenerator::SOURCE_DONASI ? 'Ajakan Donasi' : 'Mengajak Hadir',
            'prompt_nuansa_desain' => 'Islami Modern',
            'prompt_warna_utama' => 'Hijau Tua, Putih, Emas',
            'prompt_gaya_desain' => $sourceType === DesignPromptGenerator::SOURCE_DONASI ? 'Modern Minimalis' : 'Poster Kajian Ilmiah',
            'prompt_target_audiens' => $sourceType === DesignPromptGenerator::SOURCE_DONASI ? 'Donatur' : 'Jamaah Umum',
            'prompt_tingkat_keramaian' => 'Rapi Seimbang',
            'prompt_fokus_utama' => $sourceType === DesignPromptGenerator::SOURCE_DONASI ? 'Target Donasi' : 'Tema Materi',
            'prompt_model_layout' => $sourceType === DesignPromptGenerator::SOURCE_DONASI ? 'Donasi Progress Dominan' : 'Seimbang',
            'prompt_kepadatan_teks' => 'Normal',
            'prompt_posisi_foto_pemateri' => null,
            'prompt_elemen_desain' => [],
            'prompt_catatan_tambahan' => null,
        ];
    }
}
