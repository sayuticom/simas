@extends('layouts.admin')

@section('title', 'Kategori Pengeluaran - SIMAS')
@section('page_title', 'Kategori Pengeluaran')

@section('content')
<div class="space-y-6">

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">

        {{-- HEADER --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-800">
                    Kategori Pengeluaran Operasional
                </h2>
                <p class="text-sm text-gray-500">
                    Atur kategori pengeluaran operasional masjid (hanya superuser).
                </p>
            </div>

            <div class="flex flex-wrap gap-3">

                {{-- BACK --}}
                <a href="{{ route('pengaturan.dashboard') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-gray-700 hover:bg-gray-50">
                    Kembali
                </a>

                {{-- ADD CATEGORY --}}
                <a href="{{ route('pengaturan.kategori-pengeluaran.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700">
                    <i class="fas fa-plus"></i>
                    Tambah Kategori
                </a>

            </div>
        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto mt-6">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Nama Kategori
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Status
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Keterangan
                        </th>

                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">

                    @forelse($categories as $category)
                        <tr>

                            {{-- NAME --}}
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">
                                {{ $category->name }}
                            </td>

                            {{-- STATUS --}}
                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                    {{ $category->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            {{-- DESCRIPTION --}}
                            <td class="px-4 py-4 text-sm text-gray-600">
                                {{ $category->description ?: '-' }}
                            </td>

                            {{-- ACTION --}}
                            <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">

                                <a href="{{ route('pengaturan.kategori-pengeluaran.edit', $category->id) }}"
                                   class="text-green-600 hover:text-green-800">
                                    Edit
                                </a>

                                <form action="{{ route('pengaturan.kategori-pengeluaran.destroy', $category->id) }}"
                                      method="POST"
                                      class="inline-block"
                                      onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="text-red-600 hover:text-red-800">
                                        Hapus
                                    </button>

                                </form>

                            </td>

                        </tr>
                    @empty

                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                                Belum ada kategori pengeluaran.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>
    </div>
</div>
@endsection