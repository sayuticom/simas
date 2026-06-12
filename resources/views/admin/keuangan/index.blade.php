@extends('layouts.admin')

@section('title', 'Keuangan Masjid - SIMAS')
@section('page_title', 'Keuangan Operasional')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[320px_1fr]">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Saldo Operasional</h2>
            <div class="space-y-4">
                <div class="rounded-lg bg-green-50 p-4">
                    <p class="text-sm text-gray-600">Total Infak Masuk</p>
                    <p class="text-2xl font-bold text-green-700">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg bg-red-50 p-4">
                    <p class="text-sm text-gray-600">Total Pengeluaran</p>
                    <p class="text-2xl font-bold text-red-700">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg bg-indigo-50 p-4">
                    <p class="text-sm text-gray-600">Saldo Operasional</p>
                    <p class="text-2xl font-bold text-indigo-700">Rp {{ number_format($saldo, 0, ',', '.') }}</p>
                </div>
                <p class="mt-3 text-xs text-gray-600">Saldo operasional dihitung otomatis dari Penerimaan ZIS pada kategori yang boleh digunakan untuk operasional, ditambah pemasukan operasional lain, dikurangi pengeluaran operasional.</p>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Daftar Transaksi</h2>
                    <p class="text-sm text-gray-500">Transaksi operasional masjid. Pemasukan dari Penyaluran ZIS ke Operasional tampil otomatis, sedangkan pengeluaran dicatat manual.</p>
                    <p class="text-xs text-gray-500 mt-1">Catatan: Transfer dari ZIS yang berasal dari penyaluran lama tetap tampil sebagai riwayat, tetapi tidak dihitung ganda dalam saldo operasional.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    @if(auth()->user()->isSuperuser())
                        <a href="{{ route('keuangan.kategori.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-indigo-600 px-5 py-3 text-indigo-600 hover:bg-indigo-50 transition"><i class="fas fa-tags"></i> Kategori</a>
                    @endif
                    <a href="{{ route('keuangan.transaksi.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700 transition"><i class="fas fa-plus"></i> Tambah Pengeluaran</a>
                </div>
            </div>

            <form action="{{ route('keuangan.index') }}" method="GET" class="mt-6 grid gap-4 lg:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Kategori</label>
                    <select name="category_id" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }} ({{ $category->type }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Dari tanggal</label>
                        <input type="date" name="from" value="{{ request('from') }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Sampai tanggal</label>
                        <input type="date" name="to" value="{{ request('to') }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit" class="w-full rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700 transition">Filter</button>
                    <a href="{{ route('keuangan.index') }}" class="w-full rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50 transition">Reset</a>
                </div>
            </form>

            <div class="overflow-x-auto mt-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Sumber</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe Akun</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($transactions as $transaction)
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">{{ $transaction->transaction_date->format('d-m-Y') }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    @if($transaction->isFromZisDistribution())
                                        <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">Transfer dari ZIS</span>
                                    @elseif($transaction->isFromWakafCash())
                                        <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Wakaf Tunai</span>
                                    @elseif($transaction->isFromWakafManagementResult())
                                        <span class="inline-flex rounded-full bg-teal-100 px-3 py-1 text-xs font-semibold text-teal-700">Hasil Kelola Wakaf</span>
                                    @elseif($transaction->isFromWakafAssetMaintenance())
                                        <span class="inline-flex rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700">Perawatan Aset Wakaf</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">Manual</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">{{ $transaction->category?->name ?? '-' }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">{{ $transaction->cashAccount?->name ?? '-' }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $transaction->cashAccount?->accountTypeLabel() ?? ($transaction->payment_method ?: '-') }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    @if($transaction->isFromZisDistribution())
                                        @if($transaction->sourceDistribution)
                                            <a href="{{ route('zis.distributions.show', $transaction->sourceDistribution) }}" class="text-blue-600 hover:text-blue-900">Lihat Penyaluran ZIS</a>
                                        @else
                                            <span class="text-gray-500">Sumber ZIS tidak ditemukan</span>
                                        @endif
                                    @else
                                        <a href="{{ route('keuangan.transaksi.show', $transaction) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                        @if($transaction->type === 'keluar')
                                            <a href="{{ route('keuangan.transaksi.edit', $transaction) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                        @else
                                            <a href="{{ route('keuangan.transaksi.edit', $transaction) }}" class="text-gray-500 hover:text-gray-700">Readonly</a>
                                        @endif
                                        <form action="{{ route('keuangan.transaksi.destroy', $transaction) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus transaksi ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
