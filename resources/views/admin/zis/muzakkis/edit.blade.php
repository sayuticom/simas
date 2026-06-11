@extends('layouts.admin')

@section('title', 'Edit Muzakki')
@section('page_title', 'Edit Muzakki')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <form action="{{ route('zis.muzakkis.update', $muzakki) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" name="nama" value="{{ old('nama', $muzakki->nama) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    @error('nama') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Nama Kepala Keluarga</label>
                    <input type="text" name="nama_kepala_keluarga" value="{{ old('nama_kepala_keluarga', $muzakki->nama_kepala_keluarga) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">No HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $muzakki->no_hp) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Jumlah Anggota Keluarga</label>
                    <input type="number" name="jumlah_anggota_keluarga" value="{{ old('jumlah_anggota_keluarga', $muzakki->jumlah_anggota_keluarga) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea name="alamat" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('alamat', $muzakki->alamat) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Keterangan</label>
                    <textarea name="keterangan" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('keterangan', $muzakki->keterangan) }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-white hover:bg-indigo-700">Perbarui</button>
                <a href="{{ route('zis.muzakkis.index') }}" class="text-gray-600 hover:text-gray-900">Batal</a>
            </div>
        </form>
    </div>
@endsection
