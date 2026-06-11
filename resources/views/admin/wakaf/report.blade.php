@extends('layouts.admin')

@section('title', 'Laporan Wakaf')
@section('page_title', 'Laporan Wakaf')

@section('content')
@php
    $cards = [
        ['title' => 'Wakaf Tunai', 'count' => $summary['totalWakafCash'], 'amount' => $summary['totalCashNominal'], 'note' => 'Jumlah transaksi dan nominal diterima'],
        ['title' => 'Wakaf Non-Tunai', 'count' => $summary['totalWakafNonCash'], 'amount' => $summary['totalNonCashValue'], 'note' => 'Jumlah aset diterima dan nilai estimasi'],
        ['title' => 'Aset Wakaf', 'count' => $summary['totalAssets'], 'amount' => $summary['totalAssetValue'], 'note' => 'Total aset tercatat dan estimasi nilai'],
        ['title' => 'Aset Produktif', 'count' => $summary['totalProductiveAssets'], 'amount' => $summary['totalProductiveTarget'], 'note' => 'Total aset produktif dan target pendapatan'],
        ['title' => 'Hasil Kelola', 'count' => $summary['totalManagementResults'], 'amount' => $summary['totalManagementNominal'], 'note' => 'Jumlah penerimaan hasil kelola'],
        ['title' => 'Perawatan Aset', 'count' => $summary['totalMaintenances'], 'amount' => $summary['totalMaintenanceNominal'], 'note' => 'Jumlah pengeluaran perawatan'],
        ['title' => 'Dokumen Wakaf', 'count' => $summary['totalDocuments'], 'amount' => null, 'note' => 'Aktif: '.$summary['activeDocuments'].' | Kedaluwarsa: '.$summary['expiredDocuments']],
    ];
@endphp

<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Ringkasan Laporan Wakaf</h2>
                <p class="text-sm text-gray-500">Semua angka dibatasi pada masjid aktif. Filter tanggal berlaku untuk wakaf tunai, wakaf non-tunai, hasil kelola, dan perawatan aset.</p>
            </div>
            <form action="{{ route('wakaf.report') }}" method="GET" class="grid gap-3 sm:grid-cols-[1fr_1fr_auto_auto]">
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Tanggal awal</label>
                    <input type="date" name="from" value="{{ $from }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Tanggal akhir</label>
                    <input type="date" name="to" value="{{ $to }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <button type="submit" class="self-end rounded-lg bg-indigo-600 px-5 py-2 text-white hover:bg-indigo-700">Filter</button>
                <a href="{{ route('wakaf.report') }}" class="self-end rounded-lg border border-gray-300 px-5 py-2 text-center text-gray-700 hover:bg-gray-50">Reset</a>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm font-semibold text-gray-500">Total Wakif</p>
            <p class="mt-3 text-3xl font-bold text-gray-900">{{ number_format($summary['totalWakif']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm font-semibold text-gray-500">Total Nazhir</p>
            <p class="mt-3 text-3xl font-bold text-gray-900">{{ number_format($summary['totalNazhir']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm font-semibold text-gray-500">Total Program Wakaf</p>
            <p class="mt-3 text-3xl font-bold text-gray-900">{{ number_format($summary['totalPrograms']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5">
            <p class="text-sm font-semibold text-gray-500">Dokumen Kedaluwarsa</p>
            <p class="mt-3 text-3xl font-bold text-gray-900">{{ number_format($summary['expiredDocuments']) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($cards as $card)
            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-gray-800">{{ $card['title'] }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ $card['note'] }}</p>
                    </div>
                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">{{ number_format($card['count']) }} data</span>
                </div>
                @if(! is_null($card['amount']))
                    <p class="mt-4 text-2xl font-bold text-gray-900">Rp {{ number_format($card['amount'], 0, ',', '.') }}</p>
                @endif
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-base font-bold text-gray-800">10 Wakaf Tunai Terbaru</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Wakif</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($latestCash as $cash)
                            <tr>
                                <td class="px-4 py-3 text-gray-700">{{ $cash->tanggal_terima?->format('d-m-Y') }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $cash->wakif?->nama ?? '-' }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp {{ number_format((float) $cash->nominal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-3 text-gray-500">Belum ada wakaf tunai.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-base font-bold text-gray-800">10 Wakaf Non-Tunai Terbaru</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Aset</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600">Estimasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($latestNonCash as $item)
                            <tr>
                                <td class="px-4 py-3 text-gray-700">{{ $item->tanggal_terima?->format('d-m-Y') }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $item->nama_aset }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp {{ number_format((float) $item->nilai_estimasi, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-3 text-gray-500">Belum ada wakaf non-tunai.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-base font-bold text-gray-800">10 Aset Wakaf Terbaru</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Aset</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Jenis</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600">Estimasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($latestAssets as $asset)
                            <tr>
                                <td class="px-4 py-3 text-gray-700">{{ $asset->nama_aset }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $asset->jenis_aset ?: '-' }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp {{ number_format((float) $asset->nilai_estimasi, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-3 text-gray-500">Belum ada aset wakaf.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-base font-bold text-gray-800">10 Hasil Kelola Terbaru</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Aset</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($latestResults as $result)
                            <tr>
                                <td class="px-4 py-3 text-gray-700">{{ $result->tanggal_penerimaan?->format('d-m-Y') }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $result->productiveAsset?->wakafAsset?->nama_aset ?? '-' }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp {{ number_format((float) $result->nominal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-3 text-gray-500">Belum ada hasil kelola.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-base font-bold text-gray-800">10 Perawatan Aset Terbaru</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Aset</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($latestMaintenances as $maintenance)
                            <tr>
                                <td class="px-4 py-3 text-gray-700">{{ $maintenance->tanggal_pengeluaran?->format('d-m-Y') }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $maintenance->wakafAsset?->nama_aset ?? '-' }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp {{ number_format((float) $maintenance->nominal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-3 text-gray-500">Belum ada perawatan aset.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-base font-bold text-gray-800">Dokumen Perlu Perhatian</h3>
            <div class="mt-4 space-y-4">
                <div>
                    <h4 class="text-sm font-semibold text-gray-700">Akan Kedaluwarsa dalam 30 Hari</h4>
                    <div class="mt-2 divide-y divide-gray-200 rounded-lg border border-gray-200">
                        @forelse($expiringDocuments as $document)
                            <div class="p-3 text-sm">
                                <p class="font-semibold text-gray-800">{{ $document->jenis_dokumen ?: 'Dokumen' }} - {{ $document->wakafAsset?->nama_aset ?? '-' }}</p>
                                <p class="text-gray-600">Berakhir: {{ $document->tanggal_berakhir?->format('d-m-Y') }}</p>
                            </div>
                        @empty
                            <p class="p-3 text-sm text-gray-500">Tidak ada dokumen yang akan kedaluwarsa.</p>
                        @endforelse
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-700">Sudah Kedaluwarsa</h4>
                    <div class="mt-2 divide-y divide-gray-200 rounded-lg border border-gray-200">
                        @forelse($expiredDocuments as $document)
                            <div class="p-3 text-sm">
                                <p class="font-semibold text-gray-800">{{ $document->jenis_dokumen ?: 'Dokumen' }} - {{ $document->wakafAsset?->nama_aset ?? '-' }}</p>
                                <p class="text-gray-600">Berakhir: {{ $document->tanggal_berakhir?->format('d-m-Y') }}</p>
                            </div>
                        @empty
                            <p class="p-3 text-sm text-gray-500">Tidak ada dokumen kedaluwarsa.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
