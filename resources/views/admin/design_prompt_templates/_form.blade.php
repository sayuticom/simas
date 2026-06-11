@php
    $arrayText = fn ($value) => implode("\n", is_array($value) ? $value : []);
@endphp

<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    <div>
        <label class="block text-sm font-semibold text-gray-700">Nama Template <span class="text-red-600">*</span></label>
        <input type="text" name="name" value="{{ old('name', $template?->name) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3" required>
        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700">Modul</label>
        <select name="module_type" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3">
            <option value="">Umum / semua modul</option>
            @foreach($moduleOptions as $value => $label)
                <option value="{{ $value }}" @selected(old('module_type', $template?->module_type) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('module_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700">Jenis Desain <span class="text-red-600">*</span></label>
        <input type="text" name="design_type" value="{{ old('design_type', $template?->design_type ?? 'poster') }}" placeholder="poster, flyer, banner" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3" required>
        @error('design_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700">Ukuran Canvas</label>
        <input type="text" name="canvas_size" value="{{ old('canvas_size', $template?->canvas_size ?? '1080x1080') }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3">
        @error('canvas_size')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    @foreach([
        'tone' => 'Nuansa',
        'style' => 'Gaya',
        'color_palette' => 'Warna',
        'target_audience' => 'Target Audiens',
        'layout_density' => 'Kepadatan Layout',
    ] as $field => $label)
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ $label }}</label>
            <input type="text" name="{{ $field }}" value="{{ old($field, $template?->{$field}) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3">
            @error($field)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    @foreach([
        'platforms' => 'Platform',
        'elements' => 'Elemen Desain',
        'required_text_rules' => 'Aturan Teks Wajib',
        'photo_rules' => 'Aturan Foto',
    ] as $field => $label)
        <div>
            <label class="block text-sm font-semibold text-gray-700">{{ $label }}</label>
            <textarea name="{{ $field }}" rows="4" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3">{{ old($field, $arrayText($template?->{$field})) }}</textarea>
            <p class="mt-1 text-xs font-semibold text-slate-600">Isi satu item per baris.</p>
            @error($field)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    @endforeach
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Struktur Prompt <span class="text-red-600">*</span></label>
    <textarea name="prompt_structure" rows="14" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 font-mono text-sm" required>{{ old('prompt_structure', $template?->prompt_structure ?? "Buatkan desain {module_type} untuk masjid {nama_masjid}.\n\nJudul: {judul}\nDeskripsi: {deskripsi}\n\nPastikan desain rapi, informatif, mudah dibaca di HP, dan cocok untuk media sosial.") }}</textarea>
    <p class="mt-1 text-xs font-semibold text-slate-600">Placeholder: {judul}, {deskripsi}, {nama_masjid}, {tanggal}, {lokasi}, {narasumber}, {target_amount}, {rekening}, {qris}.</p>
    @error('prompt_structure')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<label class="inline-flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 font-bold text-slate-800">
    <input type="checkbox" name="is_active" value="1" class="h-5 w-5 rounded border-slate-300 text-emerald-700" @checked(old('is_active', $template?->is_active ?? true))>
    Template aktif
</label>
