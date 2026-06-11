@php
    $statusValue = old('status', $program?->status ?? 'draft');
    $paymentModeValue = old('payment_mode', $program?->payment_mode ?? 'manual');
    $featuredImageUrl = $program?->featured_image ? asset('storage/' . $program->featured_image) : null;
    $qrisImageUrl = $program?->qris_image ? asset('storage/' . $program->qris_image) : null;
@endphp

<div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm font-semibold leading-6 text-amber-900">
    QRIS dinamis belum aktif pada tahap ini. Gunakan mode Manual dahulu. Gambar QRIS di form ini adalah QRIS statis/manual.
</div>

<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    <div class="lg:col-span-2">
        <label class="block text-sm font-semibold text-gray-700">Judul Program <span class="text-red-600">*</span></label>
        <input type="text" name="title" value="{{ old('title', $program?->title) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
        @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Slug Publik</label>
        <input type="text" name="slug" value="{{ old('slug', $program?->slug) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" placeholder="program-donasi">
        <p class="mt-1 text-xs font-semibold text-slate-600">Kosongkan untuk dibuat otomatis dari judul.</p>
        @error('slug')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Kategori</label>
        <input type="text" name="category" value="{{ old('category', $program?->category) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Pembangunan, Sosial, Operasional">
        @error('category')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Status</label>
        <select name="status" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
            @foreach($statuses as $value => $label)
                <option value="{{ $value }}" @selected($statusValue === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Mode Pembayaran</label>
        <select name="payment_mode" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
            @foreach($paymentModes as $value => $label)
                <option value="{{ $value }}" @selected($paymentModeValue === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('payment_mode')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Target Dana</label>
        <input type="number" name="target_amount" min="0" step="0.01" value="{{ old('target_amount', $program?->target_amount) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('target_amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Dana Terkumpul</label>
        <input type="number" name="collected_amount" min="0" step="0.01" value="{{ old('collected_amount', $program?->collected_amount ?? 0) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        <p class="mt-1 text-xs font-semibold text-slate-600">Sementara diisi manual admin.</p>
        @error('collected_amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Tanggal Mulai</label>
        <input type="date" name="start_date" value="{{ old('start_date', $program?->start_date?->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('start_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Tanggal Selesai</label>
        <input type="date" name="end_date" value="{{ old('end_date', $program?->end_date?->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('end_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Deskripsi <span class="text-red-600">*</span></label>
    <textarea name="description" rows="8" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('description', $program?->description) }}</textarea>
    @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    <div>
        <label class="block text-sm font-semibold text-gray-700">Gambar Program</label>
        <input type="file" name="featured_image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        <p class="mt-1 text-xs font-semibold text-slate-600">Format JPG, JPEG, PNG, atau WEBP. Maksimal 4MB.</p>
        @error('featured_image')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        @if($featuredImageUrl)
            <img src="{{ $featuredImageUrl }}" alt="Preview gambar program" class="mt-3 h-44 w-full rounded-lg object-cover">
        @endif
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Gambar QRIS Manual</label>
        <input type="file" name="qris_image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        <p class="mt-1 text-xs font-semibold text-slate-600">Gunakan QRIS statis/manual untuk tahap ini.</p>
        @error('qris_image')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        @if($qrisImageUrl)
            <img src="{{ $qrisImageUrl }}" alt="Preview QRIS program" class="mt-3 h-44 w-full rounded-lg object-contain">
        @endif
    </div>
</div>

<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    <div>
        <label class="block text-sm font-semibold text-gray-700">Nama Bank</label>
        <input type="text" name="bank_name" value="{{ old('bank_name', $program?->bank_name) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('bank_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Nomor Rekening</label>
        <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $program?->bank_account_number) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('bank_account_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Nama Pemilik Rekening</label>
        <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $program?->bank_account_name) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @error('bank_account_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">WhatsApp Konfirmasi</label>
        <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $program?->whatsapp_number) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" placeholder="628123456789">
        @error('whatsapp_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Akun Kas Terkait</label>
        <select name="cash_account_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Tidak dihubungkan dulu</option>
            @foreach($cashAccounts as $cashAccount)
                <option value="{{ $cashAccount->id }}" @selected((int) old('cash_account_id', $program?->cash_account_id) === (int) $cashAccount->id)>
                    {{ $cashAccount->name }} - {{ $cashAccount->accountTypeLabel() }}
                </option>
            @endforeach
        </select>
        @error('cash_account_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="flex flex-col justify-end gap-3">
        <label class="inline-flex items-center gap-3 rounded-lg border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700">
            <input type="checkbox" name="show_on_public" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ old('show_on_public', $program?->show_on_public ?? true) ? 'checked' : '' }}>
            Tampilkan di Website Publik
        </label>
        <label class="inline-flex items-center gap-3 rounded-lg border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700">
            <input type="checkbox" name="is_featured" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ old('is_featured', $program?->is_featured) ? 'checked' : '' }}>
            Program Unggulan
        </label>
    </div>
</div>
