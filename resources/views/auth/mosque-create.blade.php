@extends('layouts.admin')

@section('title', 'Tambah Masjid - SIMAS')
@section('page_title', 'Tambah Masjid')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
    <h2 class="text-2xl font-bold mb-4">Tambah Masjid Baru</h2>

    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('mosque.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-semibold text-gray-700">Nama Masjid</label>
            <input type="text" name="name" value="{{ old('name') }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3" required>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700">Alamat</label>
            <textarea name="address" rows="3" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3">{{ old('address') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700">No. Telepon</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3">
        </div>

        <div class="flex justify-end">
            <a href="{{ route('mosque.select') }}" class="mr-3 text-sm text-gray-600 hover:underline">Kembali</a>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg">Buat Masjid</button>
        </div>
    </form>
</div>
@endsection
