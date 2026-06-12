@extends('layouts.admin')

@section('title', 'Dashboard ZIS - SIMAS')
@section('page_title', 'Dashboard ZIS')

@section('content')
<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Total Penerimaan ZIS</p>
            <p class="mt-1 text-3xl font-bold text-green-700">Rp {{ number_format($totalReceipts, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Total Penyaluran ZIS</p>
            <p class="mt-1 text-3xl font-bold text-red-700">Rp {{ number_format($totalDistributions, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Saldo ZIS</p>
            <p class="mt-1 text-3xl font-bold text-indigo-700">Rp {{ number_format($remainingBalance, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Total Penerimaan Zakat</p>
            <p class="mt-1 text-2xl font-bold text-gray-800">Rp {{ number_format($totalZakat, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Total Penerimaan Infak</p>
            <p class="mt-1 text-2xl font-bold text-gray-800">Rp {{ number_format($totalInfak, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Total Penerimaan Sedekah</p>
            <p class="mt-1 text-2xl font-bold text-gray-800">Rp {{ number_format($totalSedekah, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <div class="mb-3">
            <h3 class="text-lg font-bold text-gray-800">Saldo ZIS per Akun Kas</h3>
            <p class="text-sm text-gray-500">Posisi dana amanah berdasarkan tempat penyimpanan uang.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($accountBalances as $account)
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $account->name }}</p>
                            <p class="text-xs text-gray-500">{{ \App\Models\CashAccount::TYPE_OPTIONS[$account->type] ?? ucfirst($account->type) }}</p>
                        </div>
                        @unless($account->is_active)
                            <span class="rounded-full bg-gray-200 px-2 py-1 text-xs font-semibold text-gray-600">Nonaktif</span>
                        @endunless
                    </div>
                    <p class="mt-3 text-2xl font-bold text-indigo-700">Rp {{ number_format($account->zis_balance, 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>
    </div>


    <div class="bg-white rounded-lg shadow p-4">
        <div class="mb-3">
            <h3 class="text-lg font-bold text-gray-800">Ringkasan Saldo per Kategori ZIS</h3>
            <p class="text-sm text-gray-500">Pantau total penerimaan, penyaluran, dan sisa dana amanah untuk kategori aktif serta kategori nonaktif yang punya riwayat.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Nama Kategori</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Jenis Dana</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Sifat Penggunaan</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Total Penerimaan</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Total Penyaluran</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Sisa / Saldo</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Kas Operasional</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($categorySummaries as $category)
                        @php
                            $typeLabel = \App\Models\ZisCategory::TYPE_OPTIONS[$category->type] ?? ucfirst(str_replace('_', ' ', $category->type));
                            $usageLabel = \App\Models\ZisCategory::USAGE_OPTIONS[$category->usage_type] ?? ($category->usage_type ? ucfirst(str_replace('_', ' ', $category->usage_type)) : '-');
                            $status = 'Belum Ada Dana';
                            $statusClass = 'bg-gray-100 text-gray-700';

                            if ($category->balance < 0) {
                                $status = 'Perlu Dicek';
                                $statusClass = 'bg-red-100 text-red-700';
                            } elseif ($category->balance > 0) {
                                $status = 'Masih Ada Saldo';
                                $statusClass = 'bg-green-100 text-green-700';
                            } elseif ($category->total_receipts > 0) {
                                $status = 'Sudah Tersalurkan';
                                $statusClass = 'bg-indigo-100 text-indigo-700';
                            }

                            $typeClass = match ($category->type) {
                                'zakat' => 'bg-amber-100 text-amber-700',
                                'infak' => 'bg-blue-100 text-blue-700',
                                'sedekah' => 'bg-purple-100 text-purple-700',
                                'wakaf' => 'bg-emerald-100 text-emerald-700',
                                'bantuan' => 'bg-orange-100 text-orange-700',
                                'donasi' => 'bg-pink-100 text-pink-700',
                                'pendapatan_layanan' => 'bg-cyan-100 text-cyan-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp
                        <tr>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                                {{ $category->name }}
                                @unless($category->is_active)
                                    <span class="ml-2 inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600">Nonaktif</span>
                                @endunless
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $typeClass }}">{{ $typeLabel }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $usageLabel }}</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-700">Rp {{ number_format($category->total_receipts, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-sm text-gray-700">Rp {{ number_format($category->total_distributions, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-sm font-semibold {{ $category->balance < 0 ? 'text-red-700' : 'text-gray-900' }}">Rp {{ number_format($category->balance, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $status }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($category->allow_operational_transfer)
                                    <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">Boleh</span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">Dana Terikat</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada kategori ZIS.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
