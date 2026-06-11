@php
    $statusOptions = \App\Models\DesignRequest::statusOptions();
    $sourceOptions = \App\Services\DesignPrompts\DesignPromptGenerator::sourceOptions();
    $sourceSnapshot = $sourceData ?: ($designRequest?->source_snapshot ?? []);
    $selectedOptions = old('selected_options') ?: ($selectedOptions ?? $designRequest?->selected_options ?? []);
    $selectedElements = old('prompt_elemen_desain', $selectedOptions['prompt_elemen_desain'] ?? []);
    if (is_string($selectedElements)) {
        $selectedElements = array_filter(array_map('trim', explode(',', $selectedElements)));
    }
    $automaticElements = \App\Support\DesignPromptOptions::automaticInformationElements($sourceType);
@endphp

<input type="hidden" name="source_type" value="{{ old('source_type', $sourceType) }}">
<input type="hidden" name="source_id" value="{{ old('source_id', $sourceId) }}">
<input type="hidden" name="return_url" value="{{ old('return_url', $returnUrl ?? request('return_url')) }}">

@if($sourceSnapshot)
    <div id="design-source" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4"
        data-source='@json($sourceSnapshot)'
        data-auto-elements='@json($automaticElements)'>
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-emerald-800">Sumber Data</p>
        <h3 class="mt-1 text-lg font-black text-slate-950">{{ $sourceSnapshot['judul'] ?? 'Sumber desain' }}</h3>
        <div class="mt-3 grid gap-2 text-sm font-semibold text-slate-700 md:grid-cols-2">
            <p>Modul: {{ $sourceSnapshot['module_label'] ?? ($sourceOptions[$sourceType] ?? 'Umum') }}</p>
            @if(!empty($sourceSnapshot['tanggal']))<p>Tanggal: {{ $sourceSnapshot['tanggal'] }}</p>@endif
            @if(!empty($sourceSnapshot['jam']))<p>Waktu: {{ $sourceSnapshot['jam'] }}</p>@endif
            @if(!empty($sourceSnapshot['lokasi']))<p>Lokasi: {{ $sourceSnapshot['lokasi'] }}</p>@endif
            @if(!empty($sourceSnapshot['category']))<p>Kategori: {{ $sourceSnapshot['category'] }}</p>@endif
            @if(!empty($sourceSnapshot['target_amount']))<p>Target: {{ $sourceSnapshot['target_amount'] }}</p>@endif
            @if(!empty($sourceSnapshot['collected_amount']))<p>Terkumpul: {{ $sourceSnapshot['collected_amount'] }}</p>@endif
        </div>
    </div>
@endif

<section class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
    <div class="mb-5">
        <p class="text-xs font-bold uppercase tracking-[0.22em] text-amber-800">Generator Prompt</p>
        <h3 class="mt-1 text-lg font-black text-slate-950">Arahan Utama Desain</h3>
        <p class="mt-1 text-sm font-semibold leading-6 text-slate-700">Pilih arahan desain, klik Refresh Prompt, lalu edit prompt final jika diperlukan sebelum disimpan.</p>
    </div>

    <div class="rounded-xl border border-amber-200 bg-white p-4">
        <p class="text-sm font-black text-slate-900">Foto Narasumber</p>
        <div class="mt-3 flex flex-wrap gap-3">
            @foreach($promptOptions['photoUsageOptions'] as $value => $label)
                <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm font-bold text-slate-800">
                    <input type="radio" name="prompt_pakai_foto_narasumber" value="{{ $value }}" class="text-amber-700 focus:ring-amber-700" @checked((string) old('prompt_pakai_foto_narasumber', $selectedOptions['prompt_pakai_foto_narasumber'] ?? '0') === (string) $value)>
                    {{ $label }}
                </label>
            @endforeach
        </div>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
        @foreach([
            ['name' => 'prompt_tujuan_flyer', 'label' => 'Tujuan Flyer', 'options' => $promptOptions['flyerPurposeOptions']],
            ['name' => 'prompt_nuansa_desain', 'label' => 'Nuansa Desain', 'options' => $promptOptions['designToneOptions']],
            ['name' => 'prompt_warna_utama', 'label' => 'Warna Utama', 'options' => $promptOptions['mainColorOptions']],
            ['name' => 'prompt_gaya_desain', 'label' => 'Gaya Desain', 'options' => $promptOptions['designStyleOptions']],
            ['name' => 'prompt_target_audiens', 'label' => 'Target Audiens', 'options' => $promptOptions['targetAudienceOptions']],
            ['name' => 'prompt_fokus_utama', 'label' => 'Fokus Utama Poster', 'options' => $promptOptions['mainFocusOptions']],
            ['name' => 'prompt_model_layout', 'label' => 'Model Layout Flyer', 'options' => $promptOptions['layoutModelOptions']],
        ] as $field)
            <div>
                <label class="block text-sm font-semibold text-gray-700">{{ $field['label'] }}</label>
                <select name="{{ $field['name'] }}" class="mt-2 w-full rounded-lg border border-amber-200 bg-white px-4 py-3">
                    <option value="">Pilih {{ strtolower($field['label']) }}</option>
                    @foreach($field['options'] as $option)
                        <option value="{{ $option }}" @selected(old($field['name'], $selectedOptions[$field['name']] ?? null) === $option)>{{ $option }}</option>
                    @endforeach
                </select>
                @error($field['name'])<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        @endforeach
    </div>

    <details class="mt-5 rounded-xl border border-amber-200 bg-white p-4">
        <summary class="cursor-pointer text-sm font-black text-slate-900">Opsi Lanjutan</summary>
        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
            @foreach([
                ['name' => 'prompt_tingkat_keramaian', 'label' => 'Tingkat Keramaian Desain', 'options' => $promptOptions['crowdLevelOptions']],
                ['name' => 'prompt_kepadatan_teks', 'label' => 'Kepadatan Teks', 'options' => $promptOptions['textDensityOptions']],
            ] as $field)
                <div>
                    <label class="block text-sm font-semibold text-gray-700">{{ $field['label'] }}</label>
                    <select name="{{ $field['name'] }}" class="mt-2 w-full rounded-lg border border-amber-200 bg-white px-4 py-3">
                        <option value="">Pilih {{ strtolower($field['label']) }}</option>
                        @foreach($field['options'] as $option)
                            <option value="{{ $option }}" @selected(old($field['name'], $selectedOptions[$field['name']] ?? null) === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                    @error($field['name'])<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            @endforeach

            <div id="speaker-photo-position-field">
                <label class="block text-sm font-semibold text-gray-700">Posisi Foto Pemateri</label>
                <select name="prompt_posisi_foto_pemateri" class="mt-2 w-full rounded-lg border border-amber-200 bg-white px-4 py-3">
                    <option value="">Pilih posisi foto pemateri</option>
                    @foreach($promptOptions['speakerPhotoPositionOptions'] as $option)
                        <option value="{{ $option }}" @selected(old('prompt_posisi_foto_pemateri', $selectedOptions['prompt_posisi_foto_pemateri'] ?? null) === $option)>{{ $option }}</option>
                    @endforeach
                </select>
                @error('prompt_posisi_foto_pemateri')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </details>

    <div class="mt-5">
        <p class="text-sm font-black text-slate-900">Elemen Desain</p>
        <p class="mt-1 text-sm font-semibold text-slate-700">Elemen informasi utama akan ditambahkan otomatis sesuai modul. Pilih elemen visual tambahan secukupnya.</p>
        @if($automaticElements)
            <div class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3">
                <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-800">Otomatis dari modul</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach($automaticElements as $element)
                        <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-emerald-900 ring-1 ring-emerald-200">{{ $element }}</span>
                    @endforeach
                </div>
            </div>
        @endif
        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
            @foreach($promptOptions['groupedDesignElementOptions'] as $group => $elements)
                <div class="rounded-xl border border-amber-200 bg-white p-4">
                    <p class="text-sm font-black text-slate-900">{{ $group }}</p>
                    <div class="mt-3 space-y-2">
                        @foreach($elements as $element)
                            <label class="flex items-start gap-2 text-sm font-semibold text-slate-800">
                                <input type="checkbox" name="prompt_elemen_desain[]" value="{{ $element }}" class="mt-1 rounded border-slate-300 text-amber-700 focus:ring-amber-700" @checked(in_array($element, $selectedElements, true))>
                                <span>{{ $element }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        @error('prompt_elemen_desain')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        @error('prompt_elemen_desain.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="mt-4">
        <label class="block text-sm font-semibold text-gray-700">Catatan Visual Tambahan</label>
        <textarea name="prompt_catatan_tambahan" rows="3" maxlength="1000" placeholder="Contoh: tambahkan foto asli kurma/takjil, gunakan nuansa hangat, rekening dibuat sangat jelas, jangan terlalu ramai." class="mt-2 w-full rounded-lg border border-amber-200 bg-white px-4 py-3">{{ old('prompt_catatan_tambahan', $selectedOptions['prompt_catatan_tambahan'] ?? '') }}</textarea>
        @error('prompt_catatan_tambahan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</section>

<div>
    <label class="block text-sm font-semibold text-gray-700">Prompt Final</label>
    <textarea id="prompt_text" name="prompt_text" rows="22" class="mt-2 min-h-[460px] w-full rounded-xl border border-gray-300 px-4 py-3 font-mono text-sm leading-6">{{ old('prompt_text', $promptText) }}</textarea>
    <p class="mt-1 text-xs font-semibold text-slate-600">Prompt final bisa diedit manual sebelum disimpan.</p>
    @error('prompt_text')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<div class="flex flex-wrap justify-end gap-3">
    <button type="button" id="refreshDesignPrompt" class="rounded-lg border border-amber-300 bg-white px-4 py-2 text-sm font-bold text-amber-900 hover:bg-amber-100">Refresh Prompt</button>
    <button type="button" id="copyDesignPromptForm" class="rounded-lg bg-amber-700 px-4 py-2 text-sm font-bold text-white hover:bg-amber-800">Salin Prompt</button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sourceEl = document.getElementById('design-source');
    const source = JSON.parse(sourceEl?.dataset.source || '{}');
    const automaticElements = JSON.parse(sourceEl?.dataset.autoElements || '[]');
    const promptText = document.getElementById('prompt_text');
    const photoPositionField = document.getElementById('speaker-photo-position-field');
    const value = (name) => document.querySelector(`[name="${name}"]`)?.value?.trim() || '';
    const radio = (name) => document.querySelector(`[name="${name}"]:checked`)?.value || '0';
    const checked = (name) => Array.from(document.querySelectorAll(`[name="${name}[]"]:checked`)).map((el) => el.value);
    const line = (label, val) => val ? `- ${label}: ${val}` : null;
    const lower = (text) => text ? text.toLowerCase() : '';

    function togglePhotoPosition() {
        if (! photoPositionField) return;
        const usesPhoto = radio('prompt_pakai_foto_narasumber') === '1';
        photoPositionField.classList.toggle('hidden', ! usesPhoto);
    }

    function generatePrompt() {
        const elements = checked('prompt_elemen_desain');
        const usesPhoto = radio('prompt_pakai_foto_narasumber') === '1';
        const allElements = [...new Set([...elements, ...automaticElements])];
        const moduleType = source.module_type || 'umum';
        const title = source.judul || 'konten masjid';
        const isDonation = moduleType === 'donasi';
        const subject = isDonation ? 'donasi masjid' : 'kegiatan masjid';
        const purpose = value('prompt_tujuan_flyer') || (isDonation ? 'Ajakan Donasi' : 'Mengajak Hadir');
        const info = [
            line('Nama/Judul', source.judul),
            line('Jenis kegiatan', source.jenis_kegiatan),
            line('Tema materi', source.tema_materi),
            line('Narasumber', source.narasumber),
            line('Tanggal', source.tanggal),
            line('Waktu', source.jam),
            line('Lokasi', source.lokasi),
            line('Kategori', source.category),
            line('Target dana', source.target_amount),
            line('Dana terkumpul', source.collected_amount),
            line('Rekening', [source.bank_name, source.bank_account_number, source.bank_account_name].filter(Boolean).join(' ')),
            line('QRIS', source.qris),
            line('Kontak', [source.label_kontak, source.kontak_person, source.nomor_kontak, source.whatsapp_number].filter(Boolean).join(' ')),
            line('Deskripsi singkat', source.deskripsi),
        ].filter(Boolean).join('\n');

        promptText.value = `Buatkan desain flyer/poster ${subject} dalam bahasa Indonesia untuk media sosial ukuran 1080 x 1080 px dengan rasio 1:1. Desain harus terlihat resmi, rapi, menarik, mudah dibaca di layar HP, dan cocok dibagikan ke Instagram, Facebook, WhatsApp, serta website.

Poster ini bertujuan sebagai ${lower(purpose)} untuk "${title}".

Gunakan nuansa ${lower(value('prompt_nuansa_desain') || 'Islami Modern')} dengan gaya ${lower(value('prompt_gaya_desain') || 'Modern Minimalis')}. Warna utama menggunakan ${value('prompt_warna_utama') || 'Hijau Tua, Putih, Emas'}. Target audiens adalah ${lower(value('prompt_target_audiens') || (isDonation ? 'Donatur' : 'Jamaah Umum'))}, sehingga desain harus terasa amanah, profesional, dan mudah dipahami.${value('prompt_tingkat_keramaian') || value('prompt_kepadatan_teks') ? ` Komposisi dibuat ${[lower(value('prompt_tingkat_keramaian')), value('prompt_kepadatan_teks') ? 'teks ' + lower(value('prompt_kepadatan_teks')) : ''].filter(Boolean).join(' dan ')}.` : ''}

${isDonation
    ? `Fokus utama poster adalah ${lower(value('prompt_fokus_utama') || 'Target Donasi')}. Buat target donasi, progress donasi, rekening donasi, dan QRIS jika tersedia tampil jelas sebagai informasi utama.`
    : `Fokus utama poster adalah ${lower(value('prompt_fokus_utama') || 'Nama Kegiatan')}. Buat judul, tema, tanggal, jam, lokasi, dan narasumber jika tersedia mudah ditemukan dalam satu pandangan.`}

Informasi wajib yang harus tampil:
${info}

Susun layout dengan model ${lower(value('prompt_model_layout') || 'Seimbang')}. Bagian atas digunakan untuk identitas atau logo masjid, judul dibuat besar dan dominan, informasi utama diletakkan di area yang mudah terlihat, dan deskripsi dibuat singkat agar tidak terlalu padat.${isDonation ? ' Progress donasi, nomor rekening, dan QRIS dibuat jelas, besar, dan mudah ditemukan.' : ' Tanggal, waktu, lokasi, dan kontak jika tersedia dibuat dalam blok informasi yang rapi.'}

${allElements.length ? `Gunakan elemen pendukung seperlunya seperti ${allElements.join(', ')}. Elemen pendukung tidak harus semua tampil besar; tempatkan secukupnya agar memperkuat suasana visual tanpa mengganggu keterbacaan informasi utama.\n\n` : ''}${usesPhoto ? `Gunakan foto narasumber yang diberikan${value('prompt_posisi_foto_pemateri') ? ' dengan posisi ' + lower(value('prompt_posisi_foto_pemateri')) : ''}. Jangan mengubah wajah, identitas visual, atau ekspresi utama narasumber secara berlebihan.${source.narasumber ? ' Jika nama narasumber tersedia, tampilkan nama "' + source.narasumber + '" di bawah foto dengan rapi.' : ''}` : 'Jangan tampilkan foto narasumber. Jika ada nama narasumber, tampilkan sebagai teks biasa yang rapi.'}

${value('prompt_catatan_tambahan') ? '\nPreferensi visual tambahan: ' + value('prompt_catatan_tambahan') + '\n' : ''}
Jangan menambahkan informasi yang tidak tersedia dalam data. Hindari teks terlalu kecil, warna abu-abu muda, atau kontras yang lemah. Pastikan semua informasi penting terbaca jelas di layar HP.`;
    }

    document.querySelectorAll('[name="prompt_pakai_foto_narasumber"]').forEach((el) => {
        el.addEventListener('change', togglePhotoPosition);
    });
    togglePhotoPosition();

    document.getElementById('refreshDesignPrompt')?.addEventListener('click', generatePrompt);
    document.getElementById('copyDesignPromptForm')?.addEventListener('click', async function () {
        await navigator.clipboard.writeText(promptText.value);
        const original = this.textContent;
        this.textContent = 'Prompt Disalin';
        setTimeout(() => this.textContent = original, 1500);
    });
});
</script>
