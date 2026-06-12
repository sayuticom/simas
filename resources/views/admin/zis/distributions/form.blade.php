@php
    $sourceReceipt = $sourceReceipt ?? $distribution?->receipt;
    $sourceAmount = $sourceReceipt?->amount ?? $sourceReceipt?->nominal_uang ?? 0;
    $sourceDistributed = $sourceReceipt ? $sourceReceipt->distributions()->when($distribution?->id, fn ($query) => $query->where('id', '!=', $distribution->id))->sum('amount') : 0;
    $sourceRemaining = $sourceReceipt ? max($sourceAmount - $sourceDistributed, 0) : 0;
@endphp

@if($sourceReceipt)
    <div class="mb-6 rounded-lg border border-indigo-200 bg-indigo-50 p-4">
        <p class="text-sm font-semibold text-indigo-800">Sumber dana: {{ $sourceReceipt->category?->name ?? '-' }} - {{ $sourceReceipt->donor_name ?: '-' }} - Rp {{ number_format($sourceAmount, 0, ',', '.') }}</p>
        <p class="mt-1 text-sm text-indigo-700">Tanggal penerimaan: {{ $sourceReceipt->receipt_date?->format('d-m-Y') ?? '-' }}</p>
        <p class="mt-1 text-sm font-semibold text-indigo-900">Sisa dana yang dapat disalurkan: Rp {{ number_format($sourceRemaining, 0, ',', '.') }}</p>
    </div>
    <input type="hidden" name="zis_receipt_id" value="{{ old('zis_receipt_id', $sourceReceipt->id) }}">
@else
    <input type="hidden" name="zis_receipt_id" value="{{ old('zis_receipt_id', $distribution?->zis_receipt_id) }}">
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700">Tanggal Penyaluran</label>
        <input type="date" name="distribution_date" value="{{ old('distribution_date', $distribution?->distribution_date?->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border px-4 py-3" required>
        @error('distribution_date')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700">Sumber Dana / Kategori ZIS</label>
        <select id="zis-category-id" name="zis_category_id" class="mt-2 w-full rounded-lg border px-4 py-3" required>
            <option value="">Pilih kategori dana</option>
            @foreach($categories as $category)
                @php
                    $categoryBalance = $category->available_balance ?? 0;
                    $selectedCategoryId = old('zis_category_id', $preselectedCategoryId ?? $sourceReceipt?->zis_category_id ?? $distribution?->zis_category_id);
                    $isSelectedCategory = $selectedCategoryId == $category->id;
                @endphp
                <option
                    value="{{ $category->id }}"
                    data-type="{{ $category->type }}"
                    data-allow-operational-transfer="{{ $category->allow_operational_transfer ? '1' : '0' }}"
                    data-balance="{{ $categoryBalance }}"
                    data-balance-label="Saldo tersedia kategori ini: Rp {{ number_format($categoryBalance, 0, ',', '.') }}"
                    {{ $isSelectedCategory ? 'selected' : '' }}
                    {{ $categoryBalance <= 0 && ! $isSelectedCategory ? 'disabled' : '' }}
                >
                    {{ $category->name }} - {{ ucfirst(str_replace('_', ' ', $category->type)) }} | Saldo: Rp {{ number_format($categoryBalance, 0, ',', '.') }}
                </option>
            @endforeach
        </select>
        @error('zis_category_id')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        @if($categories->isEmpty())
            <p class="mt-1 text-xs text-amber-700">Belum ada kategori ZIS dengan saldo tersedia. Tambahkan penerimaan ZIS terlebih dahulu sebelum membuat penyaluran.</p>
        @endif
        @unless($sourceReceipt)
            <p id="category-balance-note" class="mt-1 text-xs font-semibold text-indigo-700"></p>
        @endunless
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700">Tujuan Penyaluran</label>
        <select id="distribution-target" name="distribution_target" class="mt-2 w-full rounded-lg border px-4 py-3" required>
            @foreach($distributionTargets as $value => $label)
                <option value="{{ $value }}" {{ old('distribution_target', $distribution?->distribution_target ?? 'penerima_manfaat') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('distribution_target')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        <p id="operational-transfer-note" class="mt-1 hidden text-xs text-amber-700">Kategori ini termasuk dana terikat/khusus, sehingga tidak bisa dipindahkan ke kas operasional.</p>
        <p id="operational-transfer-info" class="mt-1 hidden text-xs font-semibold text-green-700">Dana ini akan dipindahkan ke saldo Keuangan Masjid sebagai Transfer dari ZIS.</p>
    </div>
    <div class="recipient-field">
        <label class="block text-sm font-semibold text-gray-700">Nama Penerima</label>
        <input type="text" name="recipient_name" value="{{ old('recipient_name', $distribution?->recipient_name) }}" class="mt-2 w-full rounded-lg border px-4 py-3" required>
        @error('recipient_name')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="recipient-field">
        <label class="block text-sm font-semibold text-gray-700">No. HP Penerima</label>
        <input type="text" name="recipient_phone" value="{{ old('recipient_phone', $distribution?->recipient_phone) }}" class="mt-2 w-full rounded-lg border px-4 py-3">
    </div>
    <div class="recipient-field">
        <label class="block text-sm font-semibold text-gray-700">Jenis Penerima</label>
        <select id="recipient-type" name="recipient_type" class="mt-2 w-full rounded-lg border px-4 py-3">
            <option value="">Pilih jenis</option>
            @foreach($recipientTypes as $value => $label)
                <option value="{{ $value }}" {{ old('recipient_type', $distribution?->recipient_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('recipient_type')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700">Nominal</label>
        <input
            id="distribution-amount"
            type="number"
            step="0.01"
            min="0.01"
            name="amount"
            value="{{ old('amount', $distribution?->amount) }}"
            class="mt-2 w-full rounded-lg border px-4 py-3"
            @if($sourceReceipt) max="{{ $sourceRemaining }}" data-source-remaining="{{ $sourceRemaining }}" @endif
            required
        >
        @if($sourceReceipt)
            <p class="mt-1 text-xs font-semibold text-indigo-700">Maksimal penyaluran dari penerimaan ini: Rp {{ number_format($sourceRemaining, 0, ',', '.') }}</p>
        @else
            <p id="amount-balance-note" class="mt-1 text-xs font-semibold text-indigo-700"></p>
        @endif
        @error('amount')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700">Bukti Penyaluran</label>
        <input type="file" name="proof_file" accept="image/*,.pdf" class="mt-2 w-full text-sm">
    </div>
</div>
<div class="recipient-field">
    <label class="block text-sm font-semibold text-gray-700">Alamat Penerima</label>
    <textarea name="recipient_address" rows="2" class="mt-2 w-full rounded-lg border px-4 py-3">{{ old('recipient_address', $distribution?->recipient_address) }}</textarea>
</div>
<div>
    <label class="block text-sm font-semibold text-gray-700">Keterangan</label>
    <textarea name="description" rows="3" class="mt-2 w-full rounded-lg border px-4 py-3">{{ old('description', $distribution?->description) }}</textarea>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const categorySelect = document.getElementById('zis-category-id');
        const targetSelect = document.getElementById('distribution-target');
        const recipientName = document.querySelector('input[name="recipient_name"]');
        const recipientType = document.getElementById('recipient-type');
        const note = document.getElementById('operational-transfer-note');
        const transferInfo = document.getElementById('operational-transfer-info');
        const categoryBalanceNote = document.getElementById('category-balance-note');
        const amountBalanceNote = document.getElementById('amount-balance-note');
        const amountInput = document.getElementById('distribution-amount');
        const recipientFields = document.querySelectorAll('.recipient-field');

        const syncRules = () => {
            const selected = categorySelect.options[categorySelect.selectedIndex];
            const categoryType = selected?.dataset.type;
            const allowOperationalTransfer = selected?.dataset.allowOperationalTransfer === '1';
            const balanceLabel = selected?.dataset.balanceLabel || '';
            const operationalOption = [...targetSelect.options].find((option) => option.value === 'kas_operasional');

            if (operationalOption) {
                operationalOption.hidden = categoryType === 'zakat' || !allowOperationalTransfer;
                operationalOption.disabled = categoryType === 'zakat' || !allowOperationalTransfer;
            }

            if ((categoryType === 'zakat' || !allowOperationalTransfer) && targetSelect.value === 'kas_operasional') {
                targetSelect.value = 'penerima_manfaat';
            }

            const isOperationalTarget = targetSelect.value === 'kas_operasional';

            note.classList.toggle('hidden', !isOperationalTarget || allowOperationalTransfer);
            transferInfo.classList.toggle('hidden', !isOperationalTarget || !allowOperationalTransfer);
            recipientFields.forEach((field) => {
                field.classList.toggle('hidden', isOperationalTarget);
                field.querySelectorAll('input, select, textarea').forEach((input) => {
                    input.disabled = isOperationalTarget;
                });
            });
            recipientName.required = !isOperationalTarget;

            if (!isOperationalTarget && categoryType === 'zakat') {
                recipientType.required = true;
            } else {
                recipientType.required = false;
            }

            if (categoryBalanceNote) {
                categoryBalanceNote.textContent = balanceLabel;
            }

            if (amountBalanceNote) {
                amountBalanceNote.textContent = balanceLabel;
            }

            if (amountInput && !amountInput.dataset.sourceRemaining) {
                const balance = selected?.dataset.balance;
                if (balance) {
                    amountInput.max = balance;
                } else {
                    amountInput.removeAttribute('max');
                }
            }
        };

        categorySelect.addEventListener('change', syncRules);
        targetSelect.addEventListener('change', syncRules);
        syncRules();
    });
</script>
