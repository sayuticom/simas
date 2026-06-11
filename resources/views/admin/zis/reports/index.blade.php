@extends('layouts.admin')

@section('title', 'Laporan ZIS - SIMAS')
@section('page_title', 'Laporan ZIS')

@section('content')
@php
    $rupiah = fn ($amount) => 'Rp ' . number_format($amount ?? 0, 0, ',', '.');
@endphp

<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-5">
            <h2 class="text-lg font-bold text-gray-800">Filter Laporan ZIS</h2>
            <p class="text-sm text-gray-500">Pilih periode, jenis, kategori, dan tipe laporan yang ingin ditampilkan.</p>
        </div>

        @if($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="GET" action="{{ route('zis.reports.index') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                <input type="date" id="start_date" name="start_date" value="{{ $filters['start_date'] }}" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                <input type="date" id="end_date" name="end_date" value="{{ $filters['end_date'] }}" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Jenis ZIS</label>
                <select id="type" name="type" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    @foreach($typeOptions as $value => $label)
                        <option value="{{ $value }}" @selected($filters['type'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori ZIS</label>
                <select id="category_id" name="category_id" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) $filters['category_id'] === (string) $category->id)>
                            {{ $category->name }} - {{ $typeOptions[$category->type] ?? ucfirst($category->type) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="report_type" class="block text-sm font-medium text-gray-700">Tipe Laporan</label>
                <select id="report_type" name="report_type" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="ringkasan" @selected($filters['report_type'] === 'ringkasan')>Ringkasan</option>
                    <option value="detail" @selected($filters['report_type'] === 'detail')>Detail</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Tampilkan</button>
                <a href="{{ route('zis.reports.index') }}" class="rounded-lg border px-4 py-2 text-gray-700 hover:bg-gray-50">Reset</a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Total Penerimaan ZIS</p>
            <p class="mt-2 text-3xl font-bold text-green-700">{{ $rupiah($totalReceipts) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Total Penyaluran ZIS</p>
            <p class="mt-2 text-3xl font-bold text-red-700">{{ $rupiah($totalDistributions) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Saldo ZIS</p>
            <p class="mt-2 text-3xl font-bold text-indigo-700">{{ $rupiah($remainingBalance) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Total Zakat</p>
            <p class="mt-2 text-2xl font-bold text-gray-800">{{ $rupiah($totalZakat) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Total Infak</p>
            <p class="mt-2 text-2xl font-bold text-gray-800">{{ $rupiah($totalInfak) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-600">Total Sedekah</p>
            <p class="mt-2 text-2xl font-bold text-gray-800">{{ $rupiah($totalSedekah) }}</p>
        </div>
    </div>

    @if($filters['report_type'] === 'detail')
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="mb-4 text-lg font-bold text-gray-800">Detail Penerimaan</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jenis</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Donatur/Muzakki</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Akun Kas</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nominal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Bukti</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($receipts as $receipt)
                            @php
                                $proofFile = $receipt->proof_file ?? $receipt->bukti_file;
                                $amount = $receipt->amount ?? $receipt->nominal_uang ?? 0;
                            @endphp
                            <tr>
                                <td class="px-4 py-4 text-sm text-gray-700">{{ $receipt->receipt_date?->format('d-m-Y') ?? $receipt->tanggal?->format('d-m-Y') }}</td>
                                <td class="px-4 py-4 text-sm text-gray-700">{{ $receipt->category?->name ?? $receipt->jenis_penerimaan ?? '-' }}</td>
                                <td class="px-4 py-4 text-sm text-gray-700">{{ $typeOptions[$receipt->category?->type] ?? '-' }}</td>
                                <td class="px-4 py-4 text-sm text-gray-700">{{ $receipt->donor_name ?: '-' }}</td>
                                <td class="px-4 py-4 text-sm text-gray-700">
                                    {{ $receipt->cashAccount?->name ?? '-' }}
                                    <span class="block text-xs text-gray-500">{{ $receipt->cashAccount?->accountTypeLabel() ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-4 text-sm font-semibold text-gray-800">{{ $rupiah($amount) }}</td>
                                <td class="px-4 py-4 text-sm text-gray-700">{{ $receipt->description ?: '-' }}</td>
                                <td class="px-4 py-4 text-sm">
                                    @if($proofFile)
                                        <a href="{{ asset('storage/' . $proofFile) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800">Lihat</a>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">Tidak ada penerimaan pada filter ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="mb-4 text-lg font-bold text-gray-800">Detail Penyaluran</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jenis</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Penerima</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Asnaf/Jenis</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nominal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Keterangan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Bukti</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($distributions as $distribution)
                            @php
                                $proofFile = $distribution->proof_file ?? $distribution->bukti_serah_terima;
                                $amount = $distribution->amount ?? $distribution->nominal ?? 0;
                            @endphp
                            <tr>
                                <td class="px-4 py-4 text-sm text-gray-700">{{ $distribution->distribution_date?->format('d-m-Y') ?? $distribution->tanggal?->format('d-m-Y') }}</td>
                                <td class="px-4 py-4 text-sm text-gray-700">{{ $distribution->category?->name ?? $distribution->sumber_dana ?? '-' }}</td>
                                <td class="px-4 py-4 text-sm text-gray-700">{{ $typeOptions[$distribution->category?->type] ?? '-' }}</td>
                                <td class="px-4 py-4 text-sm text-gray-700">{{ $distribution->recipient_name ?: '-' }}</td>
                                <td class="px-4 py-4 text-sm text-gray-700">{{ $distribution->recipient_type ?: '-' }}</td>
                                <td class="px-4 py-4 text-sm font-semibold text-gray-800">{{ $rupiah($amount) }}</td>
                                <td class="px-4 py-4 text-sm text-gray-700">{{ $distribution->description ?: '-' }}</td>
                                <td class="px-4 py-4 text-sm">
                                    @if($proofFile)
                                        <a href="{{ asset('storage/' . $proofFile) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800">Lihat</a>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">Tidak ada penyaluran pada filter ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
