@extends('layouts.admin')

@section('title', 'Kategori ZIS - SIMAS')
@section('page_title', 'Kategori ZIS')

@section('content')
@php
    $typeBadgeClasses = [
        'zakat' => 'bg-amber-100 text-amber-700',
        'infak' => 'bg-green-100 text-green-700',
        'sedekah' => 'bg-emerald-100 text-emerald-700',
        'wakaf' => 'bg-purple-100 text-purple-700',
        'bantuan' => 'bg-blue-100 text-blue-700',
        'donasi' => 'bg-cyan-100 text-cyan-700',
        'pendapatan_layanan' => 'bg-indigo-100 text-indigo-700',
        'lainnya' => 'bg-gray-100 text-gray-700',
    ];
@endphp
<div class="bg-white rounded-lg shadow p-6">
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">{{ session('success') }}</div>
    @endif
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Kategori ZIS</h2>
            <p class="text-sm text-gray-500">Kategori menentukan jenis dana dan batas penggunaannya untuk masjid aktif.</p>
        </div>
        <a href="{{ route('zis.categories.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Tambah Kategori</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nama Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jenis Dana</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Sifat Penggunaan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Boleh Transfer ke Kas Operasional</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($categories as $category)
                    @php
                        $usageCount = ($category->receipts_count ?? 0) + ($category->distributions_count ?? 0);
                    @endphp
                    <tr>
                        <td class="px-4 py-4 text-sm font-semibold text-gray-800">{{ $category->name }}</td>
                        <td class="px-4 py-4 text-sm">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $typeBadgeClasses[$category->type] ?? $typeBadgeClasses['lainnya'] }}">
                                {{ $typeOptions[$category->type] ?? ucfirst(str_replace('_', ' ', $category->type)) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700">{{ $usageOptions[$category->usage_type] ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $category->allow_operational_transfer ? 'bg-green-100 text-green-700' : 'bg-red-50 text-red-700' }}">
                                {{ $category->allow_operational_transfer ? 'Boleh ke Kas Operasional' : 'Dana Terikat' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $category->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right text-sm space-x-2">
                            <a href="{{ route('zis.categories.edit', $category) }}" class="text-green-600 hover:text-green-900">Edit</a>
                            <form action="{{ route('zis.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Kategori yang sudah memiliki transaksi tidak akan dihapus, hanya dinonaktifkan. Lanjutkan?');">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:text-red-900">{{ $usageCount > 0 ? 'Nonaktifkan' : 'Hapus' }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada kategori.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
