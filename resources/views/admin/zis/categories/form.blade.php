<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700">Nama Kategori</label>
        <input type="text" name="name" value="{{ old('name', $category?->name) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3" required>
        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700">Tipe</label>
        <select id="zis-category-type" name="type" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3" required>
            <option value="">Pilih tipe</option>
            @foreach($typeOptions as $value => $label)
                <option value="{{ $value }}" {{ old('type', $category?->type) === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700">Batas Penggunaan</label>
        <select name="usage_type" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3">
            <option value="">Otomatis sesuai tipe dana</option>
            @foreach($usageOptions as $value => $label)
                <option value="{{ $value }}" {{ old('usage_type', $category?->usage_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('usage_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
<div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
    <div class="space-y-3">
        <label class="inline-flex items-start gap-3 text-sm font-semibold text-gray-700">
            <input type="hidden" name="allow_operational_transfer" value="0">
            <input id="allow-operational-transfer" type="checkbox" name="allow_operational_transfer" value="1" class="mt-1" {{ old('allow_operational_transfer', $category?->allow_operational_transfer ?? false) ? 'checked' : '' }}>
            <span>
                Boleh dipindahkan ke Kas Operasional Masjid
                <span id="transfer-locked-label" class="ml-2 hidden rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">Terkunci untuk Zakat/Wakaf</span>
            </span>
        </label>
        <p class="text-sm text-gray-600">Jika dana ini tidak boleh dipakai untuk operasional masjid, matikan pilihan boleh transfer ke kas operasional.</p>
        <p id="transfer-locked-help" class="hidden text-xs font-semibold text-red-700">Kategori zakat dan wakaf wajib menjadi dana terikat, sehingga pilihan ini tidak dapat diaktifkan.</p>
    </div>
</div>
<div class="space-y-3">
    <label class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category?->is_active ?? true) ? 'checked' : '' }}>
        Aktif
    </label>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const typeSelect = document.getElementById('zis-category-type');
        const allowCheckbox = document.getElementById('allow-operational-transfer');
        const lockedHelp = document.getElementById('transfer-locked-help');
        const lockedLabel = document.getElementById('transfer-locked-label');
        const lockedTypes = ['zakat', 'wakaf'];

        const syncTransferRule = () => {
            const locked = lockedTypes.includes(typeSelect.value);
            allowCheckbox.disabled = locked;
            lockedHelp.classList.toggle('hidden', !locked);
            lockedLabel.classList.toggle('hidden', !locked);
            if (locked) {
                allowCheckbox.checked = false;
            }
        };

        typeSelect.addEventListener('change', syncTransferRule);
        syncTransferRule();
    });
</script>
