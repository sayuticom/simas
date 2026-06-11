@extends('layouts.admin')

@section('title', 'Detail Muzakki')
@section('page_title', 'Detail Muzakki')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <h3 class="text-sm font-semibold uppercase text-gray-500">Nama</h3>
                <p class="mt-2 text-gray-700">{{ $muzakki->nama }}</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold uppercase text-gray-500">Nama Kepala Keluarga</h3>
                <p class="mt-2 text-gray-700">{{ $muzakki->nama_kepala_keluarga ?? '-' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold uppercase text-gray-500">No HP</h3>
                <p class="mt-2 text-gray-700">{{ $muzakki->no_hp ?? '-' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold uppercase text-gray-500">Jumlah Anggota Keluarga</h3>
                <p class="mt-2 text-gray-700">{{ $muzakki->jumlah_anggota_keluarga ?? '-' }}</p>
            </div>
            <div class="md:col-span-2">
                <h3 class="text-sm font-semibold uppercase text-gray-500">Alamat</h3>
                <p class="mt-2 text-gray-700">{{ $muzakki->alamat ?? '-' }}</p>
            </div>
            <div class="md:col-span-2">
                <h3 class="text-sm font-semibold uppercase text-gray-500">Keterangan</h3>
                <p class="mt-2 text-gray-700">{{ $muzakki->keterangan ?? '-' }}</p>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('zis.muzakkis.index') }}" class="inline-flex items-center rounded-lg bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200">Kembali</a>
        </div>
    </div>
@endsection
