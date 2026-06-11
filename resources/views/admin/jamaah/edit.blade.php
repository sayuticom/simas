@extends('layouts.admin')

@section('title', 'Edit Jamaah - SIMAS')
@section('page_title', 'Edit Jamaah')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800">Edit Data Jamaah</h2>
        <p class="text-sm text-gray-500">Perbarui informasi jamaah dengan mudah.</p>
    </div>

    <form action="{{ route('jamaah.update', $jamaah) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700">Nama</label>
                <input type="text" name="nama" value="{{ old('nama', $jamaah->nama) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
                @error('nama')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
                    <option value="">Pilih jenis kelamin</option>
                    @foreach($genderOptions as $option)
                        <option value="{{ $option }}" {{ old('jenis_kelamin', $jamaah->jenis_kelamin) === $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
                @error('jenis_kelamin')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700">Alamat</label>
            <textarea name="alamat" rows="2" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('alamat', $jamaah->alamat) }}</textarea>
            @error('alamat')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700">No. WhatsApp/Telepon</label>
                <input type="text" name="no_hp" value="{{ old('no_hp', $jamaah->no_hp) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                @error('no_hp')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700">Tanggal Lahir (Opsional)</label>
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $jamaah->tanggal_lahir?->format('Y-m-d')) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                @error('tanggal_lahir')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700">Umur (Opsional)</label>
                <input type="number" name="umur" min="0" max="120" value="{{ old('umur', $jamaah->umur) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: 35">
                <p class="mt-1 text-xs text-gray-500">Dipakai bila tanggal lahir tidak diisi.</p>
                @error('umur')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                @php
                    $pekerjaanSaatIni = old('pekerjaan', $jamaah->pekerjaan);
                    $pekerjaanTerdaftar = ! $pekerjaanSaatIni || in_array($pekerjaanSaatIni, $pekerjaanOptions, true);
                    $pekerjaanTerpilih = $pekerjaanTerdaftar ? $pekerjaanSaatIni : \App\Models\Jamaah::PEKERJAAN_LAINNYA;
                    $pekerjaanLainnya = old('pekerjaan_lainnya', $pekerjaanTerdaftar ? null : $jamaah->pekerjaan);
                @endphp
                <label class="block text-sm font-semibold text-gray-700">Pekerjaan</label>
                <select id="pekerjaan" name="pekerjaan" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Pilih pekerjaan</option>
                    @foreach($pekerjaanOptions as $option)
                        <option value="{{ $option }}" {{ $pekerjaanTerpilih === $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
                @error('pekerjaan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                <div id="pekerjaan-lainnya-container" class="mt-4 hidden">
                    <label class="block text-sm font-semibold text-gray-700">Pekerjaan Lainnya</label>
                    <input id="pekerjaan-lainnya" type="text" name="pekerjaan_lainnya" value="{{ $pekerjaanLainnya }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Tulis pekerjaan">
                    @error('pekerjaan_lainnya')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700">Keahlian</label>
                <input type="text" name="keahlian" value="{{ old('keahlian', $jamaah->keahlian) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                @error('keahlian')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="rounded-lg border border-gray-200 p-4">
                @php
                    $selectedCategoryIds = old('category_ids', $jamaah->categories->pluck('id')->all());
                @endphp
                <label class="block text-sm font-semibold text-gray-700">Kategori</label>
                <p class="mt-1 text-xs text-gray-500">Pilih minimal satu kategori.</p>
                <div class="mt-3 grid gap-2 sm:grid-cols-2">
                    @foreach($categories as $category)
                        <label class="flex items-center gap-2 rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-700">
                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" {{ in_array($category->id, $selectedCategoryIds) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            {{ $category->label }}
                        </label>
                    @endforeach
                </div>
                @error('category_ids')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                @error('category_ids.*')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700">Status Verifikasi</label>
                <select name="status" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" required>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}" {{ old('status', $jamaah->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700">Keterangan</label>
            <input type="text" name="keterangan" value="{{ old('keterangan', $jamaah->keterangan) }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            @error('keterangan')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('jamaah.index') }}" class="rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50 transition">Batal</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700 transition">Simpan Perubahan</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pekerjaan = document.getElementById('pekerjaan');
        const container = document.getElementById('pekerjaan-lainnya-container');
        const lainnya = document.getElementById('pekerjaan-lainnya');

        function togglePekerjaanLainnya() {
            const tampil = pekerjaan.value === @json(\App\Models\Jamaah::PEKERJAAN_LAINNYA);
            container.classList.toggle('hidden', !tampil);
            lainnya.disabled = !tampil;
        }

        pekerjaan.addEventListener('change', togglePekerjaanLainnya);
        togglePekerjaanLainnya();
    });
</script>
@endsection
