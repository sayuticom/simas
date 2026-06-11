@extends('layouts.admin')

@section('title', 'Tambah User - SIMAS')
@section('page_title', 'Tambah User')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="mb-6">
        <h3 class="text-lg font-bold text-gray-800">Tambah User Baru</h3>
        <p class="mt-1 text-sm text-gray-500">Buat akun dan tetapkan hak akses masjidnya.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input id="password" name="password" type="password" required minlength="8" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>

        @if($canAssignSuperuser)
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                <label class="inline-flex items-center gap-3 text-sm font-semibold text-gray-800">
                    <input id="is_superuser" name="is_superuser" type="checkbox" value="1" {{ old('is_superuser') ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    Jadikan user sebagai Superuser
                </label>
                <p class="mt-2 text-xs text-gray-500">Superuser memperoleh akses global dan tidak memerlukan penetapan masjid.</p>
            </div>
        @endif

        <div id="access-section" class="rounded-lg border border-gray-200 p-5">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h4 class="font-semibold text-gray-800">Akses Masjid</h4>
                    <p class="text-xs text-gray-500">User biasa wajib memiliki minimal satu akses.</p>
                </div>
                <button id="add-access" type="button" class="rounded-lg border border-indigo-600 bg-white px-4 py-2 text-sm font-semibold text-indigo-600 hover:bg-indigo-50">
                    Tambah Akses
                </button>
            </div>

            <div id="access-rows" class="space-y-3">
                @php
                    $accessRows = old('accesses', [['mosque_id' => '', 'role_id' => '']]);
                @endphp
                @foreach($accessRows as $index => $access)
                    <div class="access-row grid grid-cols-1 items-end gap-3 rounded-lg bg-gray-50 p-4 md:grid-cols-[1fr_1fr_auto]">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Masjid</label>
                            <select name="accesses[{{ $index }}][mosque_id]" class="access-input mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Pilih Masjid --</option>
                                @foreach($mosques as $mosque)
                                    <option value="{{ $mosque->id }}" @selected((string) ($access['mosque_id'] ?? '') === (string) $mosque->id)>{{ $mosque->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Role</label>
                            <select name="accesses[{{ $index }}][role_id]" class="access-input mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Pilih Role --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" @selected((string) ($access['role_id'] ?? '') === (string) $role->id)>{{ $role->label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" class="remove-access rounded-lg border border-red-200 px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-50">Hapus</button>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('users.index') }}" class="rounded-lg border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Simpan User</button>
        </div>
    </form>
</div>

<template id="access-row-template">
    <div class="access-row grid grid-cols-1 items-end gap-3 rounded-lg bg-gray-50 p-4 md:grid-cols-[1fr_1fr_auto]">
        <div>
            <label class="block text-sm font-medium text-gray-700">Masjid</label>
            <select data-name="mosque_id" class="access-input mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">-- Pilih Masjid --</option>
                @foreach($mosques as $mosque)
                    <option value="{{ $mosque->id }}">{{ $mosque->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Role</label>
            <select data-name="role_id" class="access-input mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">-- Pilih Role --</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->label }}</option>
                @endforeach
            </select>
        </div>
        <button type="button" class="remove-access rounded-lg border border-red-200 px-4 py-3 text-sm font-semibold text-red-600 hover:bg-red-50">Hapus</button>
    </div>
</template>

<script>
    (() => {
        const superuser = document.getElementById('is_superuser');
        const section = document.getElementById('access-section');
        const rows = document.getElementById('access-rows');
        const template = document.getElementById('access-row-template');
        const addButton = document.getElementById('add-access');

        const updateNames = () => {
            rows.querySelectorAll('.access-row').forEach((row, index) => {
                row.querySelectorAll('.access-input').forEach((input) => {
                    const field = input.dataset.name || input.name.match(/\[(mosque_id|role_id)\]$/)?.[1];
                    input.name = `accesses[${index}][${field}]`;
                });
            });
        };

        const updateAccessState = () => {
            const disabled = superuser ? superuser.checked : false;
            section.classList.toggle('opacity-50', disabled);
            section.querySelectorAll('select, button').forEach((input) => {
                input.disabled = disabled;
            });
        };

        addButton.addEventListener('click', () => {
            rows.appendChild(template.content.cloneNode(true));
            updateNames();
        });

        rows.addEventListener('click', (event) => {
            if (!event.target.classList.contains('remove-access')) {
                return;
            }

            if (rows.querySelectorAll('.access-row').length === 1) {
                rows.querySelectorAll('select').forEach((select) => {
                    select.value = '';
                });
                return;
            }

            event.target.closest('.access-row').remove();
            updateNames();
        });

        if (superuser) {
            superuser.addEventListener('change', updateAccessState);
        }
        updateNames();
        updateAccessState();
    })();
</script>
@endsection
