@extends('layouts.admin')

@section('title', 'Edit Mustahik')
@section('page_title', 'Edit Mustahik')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <form action="{{ route('zis.mustahiks.update', $mustahik) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" name="nama" value="{{ old('nama', $mustahik->nama) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">NIK</label>
                    <input type="text" name="nik" value="{{ old('nik', $mustahik->nik) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">No HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $mustahik->no_hp) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Kategori Asnaf</label>
                    <select name="kategori_asnaf" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @foreach(['Fakir', 'Miskin', 'Amil', 'Mualaf', 'Riqab', 'Gharimin', 'Fi Sabilillah', 'Ibnu Sabil'] as $kategori)
                            <option value="{{ $kategori }}" {{ old('kategori_asnaf', $mustahik->kategori_asnaf) === $kategori ? 'selected' : '' }}>{{ $kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Jumlah Tanggungan</label>
                    <input type="number" name="jumlah_tanggungan" value="{{ old('jumlah_tanggungan', $mustahik->jumlah_tanggungan) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea name="alamat" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('alamat', $mustahik->alamat) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Kondisi Ekonomi</label>
                    <textarea name="kondisi_ekonomi" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('kondisi_ekonomi', $mustahik->kondisi_ekonomi) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Catatan Survei</label>
                    <textarea name="catatan_survei" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('catatan_survei', $mustahik->catatan_survei) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Foto</label>
                    <input type="file" name="foto" class="w-full rounded-lg border-gray-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @if($mustahik->foto)
                        <p class="mt-2 text-sm text-gray-600">Foto saat ini: <a href="{{ asset('storage/' . $mustahik->foto) }}" class="text-indigo-600 hover:underline" target="_blank">Lihat</a></p>
                    @endif
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-white hover:bg-indigo-700">Perbarui</button>
                <a href="{{ route('zis.mustahiks.index') }}" class="text-gray-600 hover:text-gray-900">Batal</a>
            </div>
        </form>
    </div>
@endsection
