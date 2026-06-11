<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700">Wakif <span class="text-red-600">*</span></label>
        <select name="wakif_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Pilih wakif</option>
            @foreach($wakifs as $wakif)
                <option value="{{ $wakif->id }}" {{ (int) old('wakif_id', $wakafCash?->wakif_id) === $wakif->id ? 'selected' : '' }}>{{ $wakif->nama }}</option>
            @endforeach
        </select>
        @error('wakif_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Nazhir <span class="text-red-600">*</span></label>
        <select name="nazhir_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Pilih nazhir</option>
            @foreach($nazhirs as $nazhir)
                <option value="{{ $nazhir->id }}" {{ (int) old('nazhir_id', $wakafCash?->nazhir_id) === $nazhir->id ? 'selected' : '' }}>{{ $nazhir->nama }}</option>
            @endforeach
        </select>
        @error('nazhir_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Program Wakaf <span class="text-red-600">*</span></label>
        <select name="waqf_program_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Pilih Program Wakaf</option>
            @foreach($programs as $program)
                <option value="{{ $program->id }}" {{ (int) old('waqf_program_id', $wakafCash?->waqf_program_id) === $program->id ? 'selected' : '' }}>{{ $program->nama }}</option>
            @endforeach
        </select>
        @if($programs->isEmpty())
            <p class="mt-1 text-sm font-semibold text-amber-700">Belum ada Program Wakaf. Silakan buat Program Wakaf terlebih dahulu.</p>
        @endif
        @error('waqf_program_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Tanggal Terima <span class="text-red-600">*</span></label>
        <input type="date" name="tanggal_terima" value="{{ old('tanggal_terima', $wakafCash?->tanggal_terima?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
        @error('tanggal_terima')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Nominal <span class="text-red-600">*</span></label>
        <input type="number" name="nominal" value="{{ old('nominal', $wakafCash?->nominal ?? 0) }}" min="0" step="0.01" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
        @error('nominal')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Metode Pembayaran</label>
        <select id="metode-pembayaran" name="metode_pembayaran" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="tunai" {{ old('metode_pembayaran', $wakafCash?->metode_pembayaran ?? 'tunai') === 'tunai' ? 'selected' : '' }}>Tunai</option>
            <option value="transfer" {{ old('metode_pembayaran', $wakafCash?->metode_pembayaran) === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
            <option value="qris" {{ old('metode_pembayaran', $wakafCash?->metode_pembayaran) === 'qris' ? 'selected' : '' }}>QRIS</option>
            <option value="ewallet" {{ old('metode_pembayaran', $wakafCash?->metode_pembayaran) === 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
            <option value="lainnya" {{ old('metode_pembayaran', $wakafCash?->metode_pembayaran) === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
        </select>
        @error('metode_pembayaran')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Akun Penerimaan Dana</label>
        <select name="cash_account_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Belum dipilih</option>
            @foreach($cashAccounts as $cashAccount)
                <option value="{{ $cashAccount->id }}" {{ (int) old('cash_account_id', $wakafCash?->cash_account_id) === $cashAccount->id ? 'selected' : '' }}>
                    {{ $cashAccount->name }} - {{ $cashAccount->accountTypeLabel() }}
                </option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-gray-500">Pilih akun kas yang menerima dana wakaf tunai. Tahap ini belum otomatis membuat transaksi kas.</p>
        @error('cash_account_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Status</label>
        <select name="status" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Pilih status</option>
            <option value="tercatat" {{ old('status', $wakafCash?->status ?? 'tercatat') === 'tercatat' ? 'selected' : '' }}>Tercatat</option>
            <option value="diverifikasi" {{ old('status', $wakafCash?->status) === 'diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
            <option value="batal" {{ old('status', $wakafCash?->status) === 'batal' ? 'selected' : '' }}>Batal</option>
        </select>
        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Tujuan Investasi</label>
    <textarea name="tujuan_investasi" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('tujuan_investasi', $wakafCash?->tujuan_investasi) }}</textarea>
    @error('tujuan_investasi')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div id="bukti-pembayaran-wrapper">
        <label class="block text-sm font-semibold text-gray-700">Bukti Pembayaran Non-Tunai</label>
        <p class="mt-1 text-xs text-gray-500">Diisi jika pembayaran melalui transfer, QRIS, e-wallet, atau kanal non-tunai lainnya. Untuk pembayaran tunai, cukup cetak Bukti Tanda Terima.</p>
        <p id="bukti-tunai-note" class="mt-1 hidden text-xs font-semibold text-amber-700">Metode tunai dipilih. Upload bukti pembayaran non-tunai tidak wajib.</p>
        <input type="file" name="bukti_file" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @if($wakafCash?->bukti_file)
            <a href="{{ asset('storage/'.$wakafCash->bukti_file) }}" target="_blank" class="mt-2 inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-900">Lihat file tersimpan</a>
        @endif
        @error('bukti_file')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700">Dokumen Ikrar Khusus / Tambahan (Opsional)</label>
        <p class="mt-1 text-xs text-gray-500">Untuk wakaf tunai rutin/kecil, cukup gunakan bukti tanda terima. Dokumen ini dipakai jika ada ikrar khusus dari wakif.</p>
        <input type="file" name="dokumen_ikrar" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
        @if($wakafCash?->dokumen_ikrar)
            <a href="{{ asset('storage/'.$wakafCash->dokumen_ikrar) }}" target="_blank" class="mt-2 inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-900">Lihat file tersimpan</a>
        @endif
        @error('dokumen_ikrar')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700">Keterangan</label>
    <textarea name="keterangan" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('keterangan', $wakafCash?->keterangan) }}</textarea>
    @error('keterangan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const metodeSelect = document.getElementById('metode-pembayaran');
        const buktiWrapper = document.getElementById('bukti-pembayaran-wrapper');
        const tunaiNote = document.getElementById('bukti-tunai-note');

        const syncBuktiPembayaran = () => {
            const isTunai = metodeSelect.value === 'tunai';
            buktiWrapper.classList.toggle('rounded-lg', isTunai);
            buktiWrapper.classList.toggle('border', isTunai);
            buktiWrapper.classList.toggle('border-amber-200', isTunai);
            buktiWrapper.classList.toggle('bg-amber-50', isTunai);
            buktiWrapper.classList.toggle('p-4', isTunai);
            tunaiNote.classList.toggle('hidden', !isTunai);
        };

        metodeSelect.addEventListener('change', syncBuktiPembayaran);
        syncBuktiPembayaran();
    });
</script>
