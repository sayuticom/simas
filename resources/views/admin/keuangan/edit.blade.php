@extends('layouts.admin')

@section('title', 'Edit Transaksi - SIMAS')
@section('page_title', 'Edit Transaksi Keuangan')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="mb-4">
        <h2 class="text-xl font-bold text-gray-800">{{ $transaction->type === 'masuk' ? 'Detail Pemasukan Kas Masjid' : 'Edit Pengeluaran Kas Masjid' }}</h2>
        <p class="text-sm text-gray-500">{{ $transaction->type === 'masuk' ? 'Transaksi pemasukan lama ditampilkan sebagai readonly. Dana masuk baru dicatat melalui Modul ZIS.' : 'Catat pengeluaran operasional masjid. Pengeluaran tidak boleh melebihi saldo operasional tersedia.' }}</p>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    @if($readonly ?? false)
        <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-amber-800">
            Transaksi pemasukan lama tidak bisa diedit melalui form pengeluaran manual. Riwayat tetap tampil dan tetap dihitung di dashboard keuangan.
        </div>
    @endif

    <form action="{{ route('keuangan.transaksi.update', $transaction) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <div class="rounded-lg bg-indigo-50 p-3">
                    <p class="text-xs text-gray-600">Saldo Operasional Tersedia</p>
                <p class="text-2xl font-bold text-indigo-700">Rp {{ number_format($operationalBalance ?? 0, 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-gray-600">Pengeluaran tidak boleh melebihi saldo operasional tersedia.</p>
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700">Tanggal Transaksi</label>
            <input type="date" name="transaction_date" value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required @disabled($readonly ?? false)>
            @error('transaction_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-semibold text-gray-700">Kategori Pengeluaran</label>
                    @unless($readonly ?? false)
                        @if(auth()->user()->isSuperuser())
                            <a href="{{ route('keuangan.kategori.create', ['return_to' => 'transaction_edit', 'transaction_id' => $transaction->id]) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">Tambah Kategori</a>
                        @endif
                    @endunless
                </div>
                <select name="transaction_category_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required @disabled($readonly ?? false)>
                    <option value="">Pilih kategori pengeluaran</option>
                    @if($readonly ?? false)
                        <option value="{{ $transaction->transaction_category_id }}" selected>{{ $transaction->category?->name ?? 'Kategori pemasukan lama' }}</option>
                    @endif
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('transaction_category_id', request('category_id', $transaction->transaction_category_id)) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Transaksi manual hanya boleh memakai kategori pengeluaran.</p>
                @error('transaction_category_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Jumlah (Rp)</label>
                <input type="number" name="amount" value="{{ old('amount', $transaction->amount) }}" step="0.01" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required @disabled($readonly ?? false)>
                <p class="mt-1 text-xs text-gray-500">Maksimal sesuai saldo operasional tersedia.</p>
                @error('amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-2">
            <div>
                <label class="block text-sm font-semibold text-gray-700">Bukti Transaksi</label>
                <input type="file" name="proof_file" accept="image/*,.pdf" class="mt-2 w-full text-sm text-gray-700" @disabled($readonly ?? false)>
                <p class="mt-1 text-xs text-gray-500">Opsional. Upload nota, struk, invoice, atau bukti pembayaran.</p>
                @error('proof_file')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                @if($transaction->proof_file)
                    <p class="mt-2 text-sm text-gray-500">File saat ini: <a href="{{ asset('storage/' . $transaction->proof_file) }}" target="_blank" class="text-indigo-600 hover:underline">Lihat bukti</a></p>
                @endif
            </div>

            <div>
                <!-- reserved for helper or small validation note -->
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700">Keterangan</label>
            <textarea name="description" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" @disabled($readonly ?? false)>{{ old('description', $transaction->description) }}</textarea>
            @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('keuangan.index') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50 transition">Batal</a>
            @unless($readonly ?? false)
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700 transition">Simpan</button>
            @endunless
        </div>
    </form>
</div>
@endsection
