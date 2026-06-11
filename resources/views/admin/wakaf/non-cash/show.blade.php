@extends('layouts.admin')

@section('title', 'Detail Wakaf Non-Tunai - SIMAS')
@section('page_title', 'Detail Wakaf Non-Tunai')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $wakafNonCash->nama_aset }}</h2>
            <p class="text-sm text-gray-500">Detail penerimaan aset wakaf non-tunai.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('wakaf.non-cash.edit', $wakafNonCash) }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-3 text-white hover:bg-green-700">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('wakaf.non-cash.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Tanggal Terima</p>
            <p class="text-lg font-semibold text-gray-800">{{ $wakafNonCash->tanggal_terima?->format('d-m-Y') }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Nilai Estimasi</p>
            <p class="text-lg font-semibold text-gray-800">Rp {{ number_format((float) $wakafNonCash->nilai_estimasi, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Wakif</p>
            <p class="text-lg font-semibold text-gray-800">{{ $wakafNonCash->wakif?->nama ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Nazhir</p>
            <p class="text-lg font-semibold text-gray-800">{{ $wakafNonCash->nazhir?->nama ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Jenis Aset</p>
            <p class="text-lg font-semibold text-gray-800">{{ $wakafNonCash->jenis_aset ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Jumlah / Luas</p>
            <p class="text-lg font-semibold text-gray-800">{{ $wakafNonCash->jumlah ?? '-' }} / {{ $wakafNonCash->luas ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Nomor Sertifikat</p>
            <p class="text-lg font-semibold text-gray-800">{{ $wakafNonCash->nomor_sertifikat ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Status Dokumen</p>
            <p class="text-lg font-semibold text-gray-800">{{ $wakafNonCash->status_dokumen ?: '-' }}</p>
        </div>
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-5">
        <p class="text-sm text-gray-500">Lokasi</p>
        <p class="mt-2 text-gray-800">{{ $wakafNonCash->lokasi ?: '-' }}</p>
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-5">
        <p class="text-sm text-gray-500">Dokumen</p>
        <div class="mt-2 flex flex-wrap gap-3 text-sm font-semibold">
            @if($wakafNonCash->dokumen_ikrar)
                <a href="{{ asset('storage/'.$wakafNonCash->dokumen_ikrar) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">Dokumen Ikrar</a>
            @endif
            @if($wakafNonCash->dokumen_aset)
                <a href="{{ asset('storage/'.$wakafNonCash->dokumen_aset) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">Dokumen Aset</a>
            @endif
            @if(! $wakafNonCash->dokumen_ikrar && ! $wakafNonCash->dokumen_aset)
                <span class="text-gray-700">-</span>
            @endif
        </div>
    </div>

    @if($wakafNonCash->foto)
        <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Foto</p>
            <img src="{{ asset('storage/'.$wakafNonCash->foto) }}" alt="Foto {{ $wakafNonCash->nama_aset }}" class="mt-3 max-h-80 rounded-lg border border-gray-200 object-contain">
        </div>
    @endif

    <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-5">
        <p class="text-sm text-gray-500">Keterangan</p>
        <p class="mt-2 text-gray-800">{{ $wakafNonCash->keterangan ?: '-' }}</p>
    </div>
</div>
@endsection
