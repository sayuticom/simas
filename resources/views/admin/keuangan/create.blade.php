@extends('layouts.admin')

@section('title', 'Tambah Pengeluaran Kas Masjid - SIMAS')
@section('page_title', 'Tambah Pengeluaran Kas Masjid')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800">Tambah Pengeluaran Kas Masjid</h2>
        <p class="text-sm text-gray-500">Transaksi manual hanya untuk pengeluaran. Dana masuk dicatat melalui Modul ZIS.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('keuangan.transaksi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-gray-700">Tanggal Transaksi</label>
            <input type="date" name="transaction_date" value="{{ old('transaction_date') }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
            @error('transaction_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <div class="flex items-center justify-between gap-3">
                    <label class="block text-sm font-semibold text-gray-700">Kategori Pengeluaran</label>
                    <a href="{{ route('keuangan.kategori.create', ['return_to' => 'transaction_create']) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                        + Tambah Kategori
                    </a>
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
            <div>
                <label class="block text-sm font-semibold text-gray-700">Akun Kas</label>
                <select name="cash_account_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
                    <option value="">Pilih akun kas</option>
                    @foreach($cashAccounts as $account)
                        <option value="{{ $account->id }}" {{ old('cash_account_id') == $account->id ? 'selected' : '' }}>
                            {{ $account->name }} - {{ $account->accountTypeLabel() }}
                        </option>
                    @endforeach
                </select>
                @error('cash_account_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700">Jumlah (Rp)</label>
                <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
                @error('amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700">Bukti Transaksi</label>
                <input type="file" name="proof_file" accept="image/*,.pdf" class="mt-2 w-full text-sm text-gray-700">
                @error('proof_file')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700">Keterangan</label>
            <textarea name="description" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
            @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('keuangan.index') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50 transition">Batal</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700 transition">Simpan</button>
        </div>
    </form>
</div>
@endsection
