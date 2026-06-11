@extends('layouts.admin')

@section('title', 'User & Hak Akses - SIMAS')
@section('page_title', 'User & Hak Akses')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    @if(session('success'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Daftar User</h3>
            <p class="mt-1 text-sm text-gray-500">Daftar akun dan hak akses masjid yang telah diberikan.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('users.index') }}" class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <label for="q" class="mb-1 block text-sm font-medium text-gray-700">Cari User</label>
                <input type="text" id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Nama, email, nomor WhatsApp" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="role_id" class="mb-1 block text-sm font-medium text-gray-700">Role</label>
                <select id="role_id" name="role_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" @selected((string) ($filters['role_id'] ?? '') === (string) $role->id)>{{ $role->label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="mosque_id" class="mb-1 block text-sm font-medium text-gray-700">Masjid</label>
                <select id="mosque_id" name="mosque_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua Masjid</option>
                    @foreach($mosques as $mosque)
                        <option value="{{ $mosque->id }}" @selected((string) ($filters['mosque_id'] ?? '') === (string) $mosque->id)>{{ $mosque->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-4 flex flex-wrap items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                <i class="fas fa-filter"></i> Terapkan Filter
            </button>
            <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                <i class="fas fa-rotate-left"></i> Reset
            </a>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Kontak</th>
                    <th class="px-4 py-3">Role &amp; Masjid</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-900">{{ $user->name }}</td>
                        <td class="px-4 py-4 text-sm text-gray-700">
                            <div>{{ $user->email }}</div>
                            <div class="text-xs text-gray-500">{{ $user->phone ?: 'Nomor WhatsApp belum ada' }}</div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">
                            @forelse($user->roles as $role)
                                <div class="mb-2 last:mb-0">
                                    <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                                        {{ $role->label ?? ucfirst(str_replace('_', ' ', $role->name)) }}
                                    </span>
                                    <span class="ml-2 text-xs text-gray-500">
                                        {{ $role->pivot->mosque_id ? ($mosqueNames[$role->pivot->mosque_id] ?? 'Masjid tidak ditemukan') : 'Akses Global' }}
                                    </span>
                                </div>
                            @empty
                                <span class="text-sm text-gray-400">Belum ada hak akses</span>
                            @endforelse
                        </td>
                        <td class="px-4 py-4 text-right text-sm">
                            @if(! $user->isSuperuser() || auth()->user()->isSuperSuperuser())
                                <a href="{{ route('users.edit', $user) }}" class="rounded-lg border border-indigo-200 px-3 py-2 font-semibold text-indigo-600 hover:bg-indigo-50">
                                    Edit
                                </a>
                            @else
                                <span class="text-xs font-semibold text-gray-400">Terproteksi</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada user terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
