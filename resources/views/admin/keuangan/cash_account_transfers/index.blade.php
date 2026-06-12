@extends('layouts.admin')

@section('title', 'Mutasi Akun Kas - SIMAS')
@section('page_title', 'Mutasi Akun Kas')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Mutasi / Transfer Antar Akun Kas</h2>
            <p class="text-sm text-gray-500">Riwayat pemindahan dana antar tempat penyimpanan dalam masjid aktif.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('keuangan.akun-kas.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-wallet"></i> Akun Kas
            </a>
            <a href="{{ route('keuangan.mutasi-akun-kas.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700">
                <i class="fas fa-right-left"></i> Tambah Mutasi
            </a>
        </div>
    </div>

    @if(isset($accounts) && $accounts->isNotEmpty())
        <div class="mb-6">
            <h3 class="text-md font-semibold text-gray-800 mb-3">Rekap Saldo Akun Kas</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($accounts as $account)
                    <div class="rounded-lg border border-gray-200 p-4 bg-white shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm text-gray-600">{{ $account->name }}</p>
                                <p class="mt-2 text-lg font-bold text-gray-900">Rp {{ number_format($account->available_balance ?? $account->availableBalance(), 0, ',', '.') }}</p>
                            </div>
                            <div class="text-sm">
                                @if($account->is_active)
                                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700">Aktif</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-600">Nonaktif</span>
                                @endif
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">{{ $account->accountTypeLabel() }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Akun Asal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Akun Tujuan</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Nominal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Catatan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Petugas</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($transfers as $transfer)
                    <tr>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $transfer->transfer_date?->format('d-m-Y') }}</td>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-900">{{ $transfer->fromAccount?->name ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-900">{{ $transfer->toAccount?->name ?? '-' }}</td>
                        <td class="px-4 py-4 text-right text-sm font-semibold text-gray-900">Rp {{ number_format($transfer->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $transfer->note ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $transfer->creator?->name ?? '-' }}</td>
                        <td class="px-4 py-4 text-right text-sm">
                            <a href="{{ route('keuangan.mutasi-akun-kas.show', $transfer) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada mutasi akun kas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $transfers->links() }}
    </div>
</div>
@endsection
