@extends('layouts.admin')

@section('title', 'Detail Dokumen Wakaf - SIMAS')
@section('page_title', 'Detail Dokumen Wakaf')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">{{ $document->jenis_dokumen ?: 'Dokumen Wakaf' }}</h2>
            <p class="text-sm text-gray-500">Detail dokumen aset wakaf.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('wakaf.documents.edit', $document) }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-3 text-white hover:bg-green-700">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('wakaf.documents.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Aset Wakaf</p>
            <p class="text-lg font-semibold text-gray-800">{{ $document->wakafAsset?->nama_aset ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Nomor Dokumen</p>
            <p class="text-lg font-semibold text-gray-800">{{ $document->nomor_dokumen ?: '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Tanggal Terbit</p>
            <p class="text-lg font-semibold text-gray-800">{{ $document->tanggal_terbit?->format('d-m-Y') ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
            <p class="text-sm text-gray-500">Tanggal Berakhir</p>
            <p class="text-lg font-semibold text-gray-800">{{ $document->tanggal_berakhir?->format('d-m-Y') ?? 'Tidak ada masa berlaku' }}</p>
        </div>
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-5">
        <p class="text-sm text-gray-500">File Dokumen</p>
        @if($document->file_dokumen)
            <a href="{{ asset('storage/'.$document->file_dokumen) }}" target="_blank" class="mt-2 inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-900">Lihat dokumen</a>
        @else
            <p class="mt-2 text-gray-800">-</p>
        @endif
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-5">
        <p class="text-sm text-gray-500">Keterangan</p>
        <p class="mt-2 text-gray-800">{{ $document->keterangan ?: '-' }}</p>
    </div>
</div>
@endsection
