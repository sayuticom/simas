@extends('layouts.admin')

@section('title', 'Tambah Pengeluaran Kas Masjid - SIMAS')
@section('page_title', 'Tambah Pengeluaran Kas Masjid')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800">Tambah Pengeluaran Kas Masjid</h2>
        <p class="text-sm text-gray-500">Catat pengeluaran operasional masjid. Pengeluaran tidak boleh melebihi saldo operasional tersedia.</p>
    </div>

    <div class="mb-4">
        <div class="rounded-lg bg-indigo-50 p-3">
            <p class="text-xs text-gray-600">Saldo Operasional Tersedia</p>
            <p class="text-2xl font-bold text-indigo-700">Rp {{ number_format($operationalBalance ?? 0, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-gray-600">Pengeluaran tidak boleh melebihi saldo operasional tersedia.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('keuangan.transaksi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700">Tanggal Transaksi</label>
                <input type="date" name="transaction_date" value="{{ old('transaction_date') }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
                @error('transaction_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-semibold text-gray-700">Kategori Pengeluaran</label>
                    @if(auth()->user()->isSuperuser())
                        <a href="{{ route('keuangan.kategori.create', ['return_to' => 'transaction_create']) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">Tambah Kategori</a>
                    @endif
                </div>
                <select name="transaction_category_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
                    <option value="">Pilih kategori pengeluaran</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('transaction_category_id', $selectedCategoryId) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Kategori pemasukan tidak tampil di transaksi manual.</p>
                @error('transaction_category_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-2">
            <div>
                <label class="block text-sm font-semibold text-gray-700">Keterangan</label>
                <textarea name="description" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Jumlah (Rp)</label>
                <input id="amount-input" type="number" name="amount" value="{{ old('amount') }}" step="0.01" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
                <p class="mt-1 text-xs text-gray-500">Maksimal sesuai saldo operasional tersedia.</p>
                <p id="amount-warning" class="mt-1 text-sm text-red-600 hidden">Nominal pengeluaran melebihi saldo operasional tersedia.</p>
                @error('amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-2">
            <div>
                <label class="block text-sm font-semibold text-gray-700">Bukti Transaksi <span class="text-red-600">*</span></label>
                <input type="file" name="proof_file" accept="image/*,.pdf" required class="mt-2 w-full text-sm text-gray-700">
                <p class="mt-1 text-xs text-gray-500">Wajib dilampirkan. Upload nota, struk, invoice, atau bukti pembayaran.</p>
                @error('proof_file')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <!-- reserved for helper or small validation note -->
            </div>
        </div>


        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('keuangan.index') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50 transition">Batal</a>
            <button id="submit-button" type="submit" class="rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700 transition">Simpan</button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const operationalBalance = parseFloat({{ json_encode((float) ($operationalBalance ?? 0)) }});
            const amountInput = document.getElementById('amount-input');
            const amountWarning = document.getElementById('amount-warning');
            const submitButton = document.getElementById('submit-button');

            const validateAmount = () => {
                const val = parseFloat(amountInput.value || 0);
                if (val > operationalBalance) {
                    amountWarning.classList.remove('hidden');
                    submitButton.disabled = true;
                } else {
                    amountWarning.classList.add('hidden');
                    submitButton.disabled = false;
                }
            };

            amountInput.addEventListener('input', validateAmount);

            validateAmount();
        });
    </script>
</div>
</div>
@endsection
