@extends('layouts.admin')

@section('title', 'Ubah Password - SIMAS')
@section('page_title', 'Ubah Password')

@section('content')
<div class="mx-auto max-w-2xl">
    <div class="rounded-lg bg-white p-6 shadow">
        @if(session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800">Password Akun Saya</h2>
            <p class="mt-1 text-sm text-gray-500">Gunakan halaman ini untuk mengubah password akun login Anda sendiri.</p>
        </div>

        <form action="{{ route('account.password.update') }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="block text-sm font-semibold text-gray-700">Password Lama</label>
                <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700">Password Baru</label>
                <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter.</p>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">Konfirmasi Password Baru</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('dashboard') }}" class="rounded-lg border px-5 py-3 text-gray-700 hover:bg-gray-50">Batal</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-3 font-semibold text-white hover:bg-indigo-700">Simpan Password</button>
            </div>
        </form>
    </div>
</div>
@endsection
