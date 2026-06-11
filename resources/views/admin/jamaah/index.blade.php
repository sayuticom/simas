@extends('layouts.admin')

@section('title', 'Data Jamaah - SIMAS')
@section('page_title', 'Data Jamaah')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Daftar Jamaah</h2>
                <p class="text-sm text-gray-500">Kelola data jamaah di masjid.</p>
            </div>
            <a href="{{ route('jamaah.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700 transition">
                <i class="fas fa-plus"></i> Tambah Jamaah
            </a>
        </div>

        <form method="GET" action="{{ route('jamaah.index') }}" class="mt-6 grid gap-4 lg:grid-cols-[1.5fr_1fr_1fr_1fr] items-end">
            <div>
                <label class="block text-sm font-semibold text-gray-700">Cari nama, no. WhatsApp/telepon, atau alamat</label>
                <input type="search" name="search" value="{{ request('search') }}" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ketik pencarian...">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700">Filter kategori</label>
                <select name="kategori" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->name }}" {{ request('kategori') === $category->name ? 'selected' : '' }}>{{ $category->label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700">Filter status</label>
                <select name="status" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua status</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="w-full rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700 transition">Filter</button>
                <a href="{{ route('jamaah.index') }}" class="w-full rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50 transition">Reset</a>
            </div>
        </form>

        <div class="overflow-x-auto mt-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis Kelamin</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. WhatsApp/Telepon</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Alamat</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($jamaahs as $jamaah)
                        <tr>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800">{{ $jamaah->nama }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $jamaah->jenis_kelamin }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ $jamaah->no_hp ?: '-' }}</td>
                            <td class="px-4 py-4 text-sm text-gray-600">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($jamaah->categories as $category)
                                        <span class="inline-flex rounded-full bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700">{{ $category->label }}</span>
                                    @empty
                                        <span class="inline-flex rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-600">
                                            {{ \App\Models\Jamaah::CATEGORY_OPTIONS[$jamaah->kategori] ?? $jamaah->kategori }}
                                        </span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                @php
                                    $statusClass = match($jamaah->status) {
                                        'verified' => 'bg-green-50 text-green-700',
                                        'inactive' => 'bg-gray-100 text-gray-600',
                                        default => 'bg-yellow-50 text-yellow-700',
                                    };
                                @endphp
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                    {{ $statusOptions[$jamaah->status] ?? ucfirst($jamaah->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">{{ Str::limit($jamaah->alamat, 60, '...') }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <a href="{{ route('jamaah.show', $jamaah) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                <a href="{{ route('jamaah.edit', $jamaah) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                <form action="{{ route('jamaah.destroy', $jamaah) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus data jamaah ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada data jamaah.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $jamaahs->links() }}
        </div>
    </div>
</div>
@endsection
