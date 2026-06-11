<?php

namespace App\Http\Controllers;

use App\Models\DesignPromptTemplate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DesignPromptTemplateController extends Controller
{
    public function index(Request $request)
    {
        $mosqueId = $this->activeMosqueId();
        $filters = $request->only(['q', 'module_type', 'design_type', 'is_active']);

        $templates = DesignPromptTemplate::with('mosque')
            ->availableForMosque($mosqueId)
            ->when($filters['q'] ?? null, function ($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->when($filters['module_type'] ?? null, fn ($query, $module) => $query->where('module_type', $module))
            ->when($filters['design_type'] ?? null, fn ($query, $type) => $query->where('design_type', $type))
            ->when(($filters['is_active'] ?? '') !== '', fn ($query) => $query->where('is_active', (bool) request('is_active')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.design_prompt_templates.index', compact('filters', 'templates'));
    }

    public function create()
    {
        return view('admin.design_prompt_templates.create', [
            'template' => null,
            'moduleOptions' => DesignPromptTemplate::moduleOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['mosque_id'] = $this->activeMosqueId();
        $data['is_active'] = $request->boolean('is_active');
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        DesignPromptTemplate::create($data);

        return redirect()->route('design-prompt-templates.index')->with('success', 'Template prompt desain berhasil dibuat.');
    }

    public function show(DesignPromptTemplate $designPromptTemplate)
    {
        $this->authorizeTemplate($designPromptTemplate, false);

        return view('admin.design_prompt_templates.show', [
            'template' => $designPromptTemplate,
        ]);
    }

    public function edit(DesignPromptTemplate $designPromptTemplate)
    {
        $this->authorizeTemplate($designPromptTemplate, true);

        return view('admin.design_prompt_templates.edit', [
            'template' => $designPromptTemplate,
            'moduleOptions' => DesignPromptTemplate::moduleOptions(),
        ]);
    }

    public function update(Request $request, DesignPromptTemplate $designPromptTemplate)
    {
        $this->authorizeTemplate($designPromptTemplate, true);

        $data = $this->validatedData($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['updated_by'] = auth()->id();

        $designPromptTemplate->update($data);

        return redirect()->route('design-prompt-templates.index')->with('success', 'Template prompt desain berhasil diperbarui.');
    }

    public function destroy(DesignPromptTemplate $designPromptTemplate)
    {
        $this->authorizeTemplate($designPromptTemplate, true);
        $designPromptTemplate->delete();

        return redirect()->route('design-prompt-templates.index')->with('success', 'Template prompt desain berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'module_type' => ['nullable', Rule::in(array_keys(DesignPromptTemplate::moduleOptions()))],
            'design_type' => 'required|string|max:100',
            'canvas_size' => 'nullable|string|max:50',
            'platforms' => 'nullable|string',
            'tone' => 'nullable|string|max:255',
            'style' => 'nullable|string|max:255',
            'color_palette' => 'nullable|string|max:255',
            'target_audience' => 'nullable|string|max:255',
            'layout_density' => 'nullable|string|max:255',
            'elements' => 'nullable|string',
            'required_text_rules' => 'nullable|string',
            'photo_rules' => 'nullable|string',
            'prompt_structure' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        foreach (['platforms', 'elements', 'required_text_rules', 'photo_rules'] as $field) {
            $data[$field] = $this->linesToArray($request->input($field));
        }

        return $data;
    }

    private function authorizeTemplate(DesignPromptTemplate $template, bool $write): void
    {
        $mosqueId = $this->activeMosqueId();
        abort_unless($template->mosque_id === null || (int) $template->mosque_id === (int) $mosqueId, 404);

        if ($write && $template->mosque_id === null && ! auth()->user()?->isSuperuser()) {
            abort(403);
        }

        if ($write && $template->mosque_id !== null) {
            abort_unless((int) $template->mosque_id === (int) $mosqueId, 404);
        }
    }

    private function activeMosqueId(): int
    {
        return (int) (session('active_mosque_id') ?: auth()->user()?->active_mosque_id);
    }

    private function linesToArray(?string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $value))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }
}
