@extends('layouts.admin')

@section('title', 'Detail Aset Wakaf - SIMAS')
@section('page_title', 'Detail Aset Wakaf')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $asset->nama_aset }}</h2>
            <p class="text-sm text-gray-500">Detail aset wakaf masjid aktif.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('wakaf.assets.edit', $asset) }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-3 text-white hover:bg-green-700">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('wakaf.assets.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Sumber Wakaf</p>
            <p class="text-lg font-semibold text-gray-800">{{ ['wakaf_tunai' => 'Wakaf Tunai', 'wakaf_non_tunai' => 'Wakaf Non-Tunai', 'lainnya' => 'Lainnya'][$asset->sumber_wakaf] ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Nazhir</p>
            <p class="text-lg font-semibold text-gray-800">{{ $asset->nazhir?->nama ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Jenis Aset</p>
            <p class="text-lg font-semibold text-gray-800">{{ $asset->jenis_aset ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Nilai Estimasi</p>
            <p class="text-lg font-semibold text-gray-800">Rp {{ number_format((float) $asset->nilai_estimasi, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Kondisi</p>
            <p class="text-lg font-semibold text-gray-800">{{ $asset->kondisi ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Status Hukum</p>
            <p class="text-lg font-semibold text-gray-800">{{ $asset->status_hukum ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Status Pemanfaatan</p>
            <p class="text-lg font-semibold text-gray-800">{{ $asset->status_pemanfaatan ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Produktif</p>
            <p class="text-lg font-semibold {{ $asset->produktif ? 'text-green-700' : 'text-gray-800' }}">{{ $asset->produktif ? 'Ya' : 'Tidak' }}</p>
        </div>
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-5">
        <p class="text-sm text-gray-500">Referensi Wakaf</p>
        <p class="mt-2 text-gray-800">
            @if($asset->sumber_wakaf === 'wakaf_tunai' && $asset->wakafCash)
                Wakaf Tunai #{{ $asset->wakafCash->id }} - {{ $asset->wakafCash->wakif?->nama ?? 'Wakif' }} - Rp {{ number_format((float) $asset->wakafCash->nominal, 0, ',', '.') }}
            @elseif($asset->sumber_wakaf === 'wakaf_non_tunai' && $asset->wakafNonCash)
                Wakaf Non-Tunai #{{ $asset->wakafNonCash->id }} - {{ $asset->wakafNonCash->nama_aset }} - {{ $asset->wakafNonCash->wakif?->nama ?? 'Wakif' }}
            @else
                -
            @endif
        </p>
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-5">
        <p class="text-sm text-gray-500">Lokasi</p>
        <p class="mt-2 text-gray-800">{{ $asset->lokasi ?: '-' }}</p>
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-5">
        <p class="text-sm text-gray-500">Keterangan</p>
        <p class="mt-2 text-gray-800">{{ $asset->keterangan ?: '-' }}</p>
    </div>
</div>
@endsection
