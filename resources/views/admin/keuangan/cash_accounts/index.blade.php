@extends('layouts.admin')

@section('title', 'Akun Kas - SIMAS')
@section('page_title', 'Akun Kas')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3">{{ session('error') }}</div>
    @endif

    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Master Akun Kas / Tempat Dana</h2>
            <p class="text-sm text-gray-500">Kelola posisi dana masjid: tunai, bank, QRIS, e-wallet, dan lainnya.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('keuangan.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left"></i> Keuangan
            </a>
            <a href="{{ route('keuangan.mutasi-akun-kas.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-indigo-600 px-5 py-3 text-indigo-600 hover:bg-indigo-50">
                <i class="fas fa-right-left"></i> Mutasi Akun Kas
            </a>
            <a href="{{ route('keuangan.akun-kas.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700">
                <i class="fas fa-plus"></i> Tambah Akun Kas
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama Akun</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jenis</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Bank</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nomor Rekening</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Atas Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pemakaian</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Saldo ZIS</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Saldo Operasional</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Mutasi Bersih</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Saldo Akun</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($cashAccounts as $account)
                    <tr>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-900">{{ $account->name }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $account->accountTypeLabel() }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $account->bank_name ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $account->account_number ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $account->account_holder ?: '-' }}</td>
                        <td class="px-4 py-4 text-sm">
                            @if($account->is_active)
                                <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">Aktif</span>
                            @else
                                <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm">
                            <div class="flex flex-wrap gap-1">
                                @if($account->can_receive_zis)
                                    <span class="inline-flex rounded-full bg-green-50 px-2 py-1 text-xs font-semibold text-green-700">Terima ZIS</span>
                                @endif
                                @if($account->can_distribute_zis)
                                    <span class="inline-flex rounded-full bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-700">Salur ZIS</span>
                                @endif
                                @if($account->can_operational)
                                    <span class="inline-flex rounded-full bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700">Operasional</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4 text-right text-sm font-semibold text-gray-800">Rp {{ number_format($account->zis_balance, 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-right text-sm font-semibold text-gray-800">Rp {{ number_format($account->operational_balance, 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-right text-sm font-semibold {{ $account->transfer_balance < 0 ? 'text-red-700' : 'text-gray-800' }}">Rp {{ number_format($account->transfer_balance, 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-right text-sm font-bold text-indigo-700">Rp {{ number_format($account->available_balance, 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-right text-sm space-x-2">
                            <a href="{{ route('keuangan.akun-kas.edit', $account) }}" class="text-green-600 hover:text-green-900">Edit</a>
                            <form action="{{ route('keuangan.akun-kas.destroy', $account) }}" method="POST" class="inline" onsubmit="return confirm('Akun kas yang sudah memiliki transaksi tidak akan dihapus, hanya dinonaktifkan. Lanjutkan?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">{{ $account->is_used ? 'Nonaktifkan' : 'Hapus' }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada akun kas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
