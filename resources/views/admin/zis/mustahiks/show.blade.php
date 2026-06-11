@extends('layouts.admin')

@section('title', 'Detail Mustahik')
@section('page_title', 'Detail Mustahik')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <h3 class="text-sm font-semibold uppercase text-gray-500">Nama</h3>
                <p class="mt-2 text-gray-700">{{ $mustahik->nama }}</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold uppercase text-gray-500">Kategori Asnaf</h3>
                <p class="mt-2 text-gray-700">{{ $mustahik->kategori_asnaf }}</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold uppercase text-gray-500">No HP</h3>
                <p class="mt-2 text-gray-700">{{ $mustahik->no_hp ?? '-' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold uppercase text-gray-500">Jumlah Tanggungan</h3>
                <p class="mt-2 text-gray-700">{{ $mustahik->jumlah_tanggungan ?? '-' }}</p>
            </div>
            <div class="md:col-span-2">
                <h3 class="text-sm font-semibold uppercase text-gray-500">Alamat</h3>
                <p class="mt-2 text-gray-700">{{ $mustahik->alamat ?? '-' }}</p>
            </div>
            <div class="md:col-span-2">
                <h3 class="text-sm font-semibold uppercase text-gray-500">Kondisi Ekonomi</h3>
                <p class="mt-2 text-gray-700">{{ $mustahik->kondisi_ekonomi ?? '-' }}</p>
            </div>
            <div class="md:col-span-2">
                <h3 class="text-sm font-semibold uppercase text-gray-500">Catatan Survei</h3>
                <p class="mt-2 text-gray-700">{{ $mustahik->catatan_survei ?? '-' }}</p>
            </div>
            <div class="md:col-span-2">
                <h3 class="text-sm font-semibold uppercase text-gray-500">Foto</h3>
                @if($mustahik->foto)
                    <a href="{{ asset('storage/' . $mustahik->foto) }}" class="text-indigo-600 hover:underline" target="_blank">Lihat file</a>
                @else
                    <p class="text-gray-700">Tidak ada foto.</p>
                @endif
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('zis.mustahiks.index') }}" class="inline-flex items-center rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200">Kembali</a>
        </div>
    </div>
@endsection
